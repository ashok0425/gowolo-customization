<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BunnyStorageService
{
    private string $storageZone;
    private string $apiKey;
    private string $hostname;
    private string $cdnHostname;
    private string $tokenKey;

    public function __construct()
    {
        $this->storageZone = env('BUNNY_STORAGE_ZONE', '');
        $this->apiKey      = env('BUNNY_STORAGE_API_KEY', '');
        $this->hostname    = env('BUNNY_STORAGE_HOSTNAME', 'storage.bunnycdn.com');
        $this->cdnHostname = env('BUNNY_CDN_HOSTNAME', '');
        $this->tokenKey    = env('BUNNY_TOKEN_KEY', '');
    }

    public function isConfigured(): bool
    {
        return $this->storageZone && $this->apiKey && $this->cdnHostname;
    }

    /**
     * Upload a file to Bunny storage.
     * Returns the bunny_path (e.g. /chat/images/1735000000_file.jpg)
     */
    public function upload(UploadedFile $file, string $folder): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename  = time() . '_' . uniqid() . '.' . $extension;
        $bunnyPath = '/' . ltrim($folder, '/') . '/' . $filename;

        $url = "https://{$this->hostname}/{$this->storageZone}{$bunnyPath}";

        $response = Http::withHeaders([
            'AccessKey'    => $this->apiKey,
            'Content-Type' => 'application/octet-stream',
        ])->withBody(file_get_contents($file->getRealPath()), 'application/octet-stream')
          ->put($url);

        if (!$response->successful()) {
            throw new \Exception('Bunny upload failed: ' . $response->body());
        }

        return $bunnyPath;
    }

    /**
     * Migrate a local file to Bunny.
     * $localPath: absolute server path (e.g. public_path('/customization/logo/file.jpg'))
     */
    public function migrateLocalFile(string $localPath, string $folder): string
    {
        if (!file_exists($localPath)) {
            throw new \Exception("Local file not found: {$localPath}");
        }

        $filename  = basename($localPath);
        $bunnyPath = '/' . ltrim($folder, '/') . '/' . $filename;
        $url       = "https://{$this->hostname}/{$this->storageZone}{$bunnyPath}";

        $response = Http::withHeaders([
            'AccessKey'    => $this->apiKey,
            'Content-Type' => 'application/octet-stream',
        ])->withBody(file_get_contents($localPath), 'application/octet-stream')
          ->put($url);

        if (!$response->successful()) {
            throw new \Exception('Bunny migrate failed: ' . $response->body());
        }

        return $bunnyPath;
    }

    /**
     * Generate a signed URL for private file access.
     * Default expiry: 1 hour.
     */
    public function signedUrl(string $bunnyPath, int $expiresInSeconds = 3600): string
    {
        $expires    = time() + $expiresInSeconds;
        $url        = "https://{$this->cdnHostname}{$bunnyPath}";
        $hashString = $this->tokenKey . $bunnyPath . $expires;
        $token      = base64_encode(hash('sha256', $hashString, true));
        $token      = strtr($token, '+/', '-_');
        $token      = rtrim($token, '=');

        return "{$url}?token={$token}&expires={$expires}";
    }

    /**
     * Delete a file from Bunny storage.
     */
    public function delete(string $bunnyPath): bool
    {
        $url = "https://{$this->hostname}/{$this->storageZone}{$bunnyPath}";

        $response = Http::withHeaders(['AccessKey' => $this->apiKey])->delete($url);

        return $response->successful();
    }
}
