<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\BunnyStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChunkUploadController extends Controller
{
    public function upload(Request $request, BunnyStorageService $bunny)
    {
        if ($request->isMethod('GET')) {
            return $this->testChunk($request);
        }

        if ($request->isMethod('POST')) {
            return $this->uploadChunk($request, $bunny);
        }

        return response()->json(['error' => 'Method not allowed'], 405);
    }

    private function testChunk(Request $request)
    {
        $chunkNumber = $request->get('resumableChunkNumber');
        $identifier  = $request->get('resumableIdentifier');

        if (!$chunkNumber || !$identifier) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $chunkPath = storage_path("app/chunks/{$identifier}/chunk{$chunkNumber}");

        return file_exists($chunkPath)
            ? response()->json(['status' => 'chunk_exists'], 200)
            : response()->json(['status' => 'chunk_not_found'], 404);
    }

    private function uploadChunk(Request $request, BunnyStorageService $bunny)
    {
        $file        = $request->file('file');
        $chunkNumber = $request->get('resumableChunkNumber');
        $identifier  = $request->get('resumableIdentifier');
        $filename    = $request->get('resumableFilename');
        $totalChunks = (int) $request->get('resumableTotalChunks');
        $totalSize   = (int) $request->get('resumableTotalSize');
        $requestId   = $request->get('request_id');

        if (!$file || !$chunkNumber || !$identifier || !$filename || !$totalChunks) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        // Only allow video files
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, ['mp4', 'mov', 'webm'])) {
            return response()->json(['error' => 'Only MP4, MOV and WebM video files are supported'], 415);
        }

        // 1GB limit
        if ($totalSize > 1 * 1024 * 1024 * 1024) {
            return response()->json(['error' => 'File size exceeds 1GB limit'], 413);
        }

        $chunkDir  = storage_path("app/chunks/{$identifier}");
        $chunkPath = "{$chunkDir}/chunk{$chunkNumber}";

        // Clean up previous attempt on first chunk
        if ((int) $chunkNumber === 1 && file_exists($chunkDir)) {
            $this->cleanupChunks($chunkDir);
        }

        if (!file_exists($chunkDir)) {
            mkdir($chunkDir, 0755, true);
        }

        if (file_exists($chunkPath)) {
            unlink($chunkPath);
        }

        $file->move(dirname($chunkPath), basename($chunkPath));

        $uploadedCount = count(glob("{$chunkDir}/chunk*"));

        if ($uploadedCount == $totalChunks) {
            return $this->mergeAndUpload($identifier, $filename, $totalChunks, $chunkDir, $requestId, $bunny);
        }

        return response()->json([
            'status'          => 'chunk_uploaded',
            'uploaded_chunks' => $uploadedCount,
            'total_chunks'    => $totalChunks,
        ]);
    }

    private function mergeAndUpload(string $identifier, string $filename, int $totalChunks, string $chunkDir, ?string $requestId, BunnyStorageService $bunny)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $ext       = pathinfo($filename, PATHINFO_EXTENSION);
        $tempFile  = storage_path("app/chunks/{$identifier}_merged.{$ext}");

        try {
            $final = fopen($tempFile, 'wb');
            if (!$final) {
                throw new \Exception('Cannot create merged file');
            }

            for ($i = 1; $i <= $totalChunks; $i++) {
                $chunkPath = "{$chunkDir}/chunk{$i}";
                if (!file_exists($chunkPath)) {
                    fclose($final);
                    @unlink($tempFile);
                    throw new \Exception("Missing chunk {$i}");
                }
                $chunk = fopen($chunkPath, 'rb');
                stream_copy_to_stream($chunk, $final);
                fclose($chunk);
            }

            fclose($final);
            $fileSize = filesize($tempFile) ?: 0;
            $this->cleanupChunks($chunkDir);

            // Upload to Bunny CDN or save locally
            $ssoUser    = session('auth_user');
            $bunnyPath  = null;
            $localPath  = null;
            $cdnUrl     = null;

            if ($bunny->isConfigured()) {
                try {
                    $bunnyPath = $bunny->uploadFromPath($tempFile, 'requests/videos');
                    $cdnUrl    = $bunny->cdnUrl($bunnyPath);
                } catch (\Exception $e) {
                    Log::warning('Bunny video upload failed, saving locally: ' . $e->getMessage());
                }
            }

            if (!$bunnyPath) {
                $localName = time() . '_' . uniqid() . '.' . $ext;
                $localDir  = public_path('uploads/requests/videos');
                if (!file_exists($localDir)) {
                    mkdir($localDir, 0755, true);
                }
                copy($tempFile, "{$localDir}/{$localName}");
                $localPath = "/uploads/requests/videos/{$localName}";
            }

            @unlink($tempFile);

            // Store video info in session for linking after form submit
            $videoData = [
                'original_name' => $filename,
                'extension'     => strtolower($ext),
                'size_bytes'    => $fileSize,
                'bunny_path'    => $bunnyPath,
                'bunny_synced'  => $bunnyPath ? true : false,
                'local_path'    => $localPath,
                'uploaded_at'   => now()->toDateTimeString(),
            ];
            session(['pending_video_upload' => $videoData]);

            return response()->json([
                'status'    => 'upload_complete',
                'cdn_url'   => $cdnUrl,
                'file_name' => $filename,
            ]);
        } catch (\Exception $e) {
            @unlink($tempFile);
            Log::error('Chunk merge error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function cleanupChunks(string $chunkDir): void
    {
        foreach (glob("{$chunkDir}/chunk*") as $chunk) {
            @unlink($chunk);
        }
        @rmdir($chunkDir);
    }
}
