<?php

namespace App\Console\Commands;

use App\Models\CustomizationChat;
use App\Models\CustomizationFile;
use App\Services\BunnyStorageService;
use Illuminate\Console\Command;

class MigrateFilesToBunny extends Command
{
    protected $signature = 'customization:migrate-files-to-bunny
                            {--dry-run : List files that would be migrated without uploading}
                            {--type=all : Which type to migrate: all, chat, request}';

    protected $description = 'Migrate locally stored chat images and request files to Bunny CDN private storage';

    public function __construct(private BunnyStorageService $bunny)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (!$this->bunny->isConfigured()) {
            $this->error('Bunny CDN is not configured. Set BUNNY_STORAGE_ZONE, BUNNY_STORAGE_API_KEY, BUNNY_CDN_HOSTNAME in .env.');
            return self::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        $type   = $this->option('type');

        if ($dryRun) {
            $this->warn('DRY RUN — no files will be uploaded.');
        }

        $chatMigrated = $reqMigrated = $chatFailed = $reqFailed = 0;

        if (in_array($type, ['all', 'chat'])) {
            [$chatMigrated, $chatFailed] = $this->migrateChats($dryRun);
        }

        if (in_array($type, ['all', 'request'])) {
            [$reqMigrated, $reqFailed] = $this->migrateRequestFiles($dryRun);
        }

        $this->newLine();
        $this->info('Migration complete.');
        $this->table(['Type', 'Migrated', 'Failed'], [
            ['Chat messages', $chatMigrated, $chatFailed],
            ['Request files', $reqMigrated,  $reqFailed],
        ]);

        return self::SUCCESS;
    }

    private function migrateChats(bool $dryRun): array
    {
        $migrated = $failed = 0;

        $chats = CustomizationChat::whereNotNull('local_path')
            ->whereNull('bunny_path')
            ->cursor();

        $bar = $this->output->createProgressBar();
        $bar->setFormat(' %current% [%bar%] %elapsed% — %message%');
        $bar->start();

        foreach ($chats as $chat) {
            $bar->setMessage("chat #{$chat->id}");
            $bar->advance();

            $absolutePath = public_path($chat->local_path);

            if (!file_exists($absolutePath)) {
                $this->warn("  [SKIP] Local file missing: {$chat->local_path}");
                $failed++;
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY] Would upload chat #{$chat->id}: {$chat->local_path}");
                $migrated++;
                continue;
            }

            try {
                $folder    = 'chat/' . ($chat->file_type === 'image' ? 'images' : 'documents');
                $bunnyPath = $this->bunny->migrateLocalFile($absolutePath, $folder);
                $chat->update(['bunny_path' => $bunnyPath, 'bunny_synced' => true]);
                $migrated++;
            } catch (\Exception $e) {
                $this->error("  [FAIL] chat #{$chat->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $bar->finish();
        $this->newLine();

        return [$migrated, $failed];
    }

    private function migrateRequestFiles(bool $dryRun): array
    {
        $migrated = $failed = 0;

        $files = CustomizationFile::whereNotNull('local_path')
            ->whereNull('bunny_path')
            ->cursor();

        $bar = $this->output->createProgressBar();
        $bar->setFormat(' %current% [%bar%] %elapsed% — %message%');
        $bar->start();

        foreach ($files as $file) {
            $bar->setMessage("file #{$file->id}");
            $bar->advance();

            $absolutePath = public_path($file->local_path);

            if (!file_exists($absolutePath)) {
                $this->warn("  [SKIP] Local file missing: {$file->local_path}");
                $failed++;
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY] Would upload file #{$file->id}: {$file->local_path}");
                $migrated++;
                continue;
            }

            try {
                $folder    = 'requests/' . $file->file_category . 's';
                $bunnyPath = $this->bunny->migrateLocalFile($absolutePath, $folder);
                $file->update(['bunny_path' => $bunnyPath, 'bunny_synced' => true]);
                $migrated++;
            } catch (\Exception $e) {
                $this->error("  [FAIL] file #{$file->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $bar->finish();
        $this->newLine();

        return [$migrated, $failed];
    }
}
