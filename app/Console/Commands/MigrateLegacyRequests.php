<?php

namespace App\Console\Commands;

use App\Models\CustomizationAnswer;
use App\Models\CustomizationChat;
use App\Models\CustomizationFile;
use App\Models\CustomizationRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One-time data migration from the legacy dashboard_db.customization_requests
 * table (and its related customization_questions table) into the new
 * customization portal schema.
 *
 * Usage:
 *   php artisan customization:migrate-legacy                # live run
 *   php artisan customization:migrate-legacy --dry-run      # preview only
 *   php artisan customization:migrate-legacy --fresh        # wipe new table first
 */
class MigrateLegacyRequests extends Command
{
    protected $signature = 'customization:migrate-legacy
                            {--dry-run : Preview what would be migrated without writing}
                            {--fresh : Truncate the destination table first (destructive!)}';

    protected $description = 'Migrate customization requests and answers from legacy dashboard_db schema';

    public function handle(): int
    {
        $dry   = $this->option('dry-run');
        $fresh = $this->option('fresh');

        $this->info('Legacy customization request migration');
        $this->line('  Source: dashboard_db.customization_requests');
        $this->line('  Dest:   mysql.customization_requests');
        if ($dry)   $this->warn('  DRY RUN — nothing will be written');
        if ($fresh) $this->warn('  FRESH  — destination table will be truncated first');

        if (!$this->confirm('Continue?', true)) {
            return self::SUCCESS;
        }

        if ($fresh && !$dry) {
            $this->warn('Truncating customization_chats, customization_files, customization_answers, customization_requests...');
            CustomizationChat::query()->delete();
            CustomizationFile::query()->delete();
            CustomizationAnswer::query()->delete();
            CustomizationRequest::query()->delete();
        }

        // Legacy → new status mapping
        // Legacy cust_status: 0=New, 1=Ongoing, 2=Completed
        // New status:         0=Pending, 1=Assigned, 2=In Review, 3=Sent for Review, 4=Approved, 5=Completed
        $statusMap = [
            0 => CustomizationRequest::STATUS_PENDING,     // 0 → 0
            1 => CustomizationRequest::STATUS_IN_REVIEW,   // 1 → 2
            2 => CustomizationRequest::STATUS_COMPLETED,   // 2 → 5
        ];

        $rows = DB::connection('dashboard_db')
            ->table('customization_requests')
            ->orderBy('cust_req_id')
            ->get();

        $this->line("Found {$rows->count()} legacy rows");

        $migrated = 0;
        $skipped  = 0;
        $errors   = 0;

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        // Base URL used to make legacy file paths absolute so the files keep
        // rendering from the dashboardv2 server until/unless physically copied.
        $legacyBaseUrl = rtrim(env('DASHBOARDV2_URL', 'https://dashboard.gowologlobal.com'), '/');

        foreach ($rows as $row) {
            try {
                $existing = CustomizationRequest::where('origin_cust_req_id', $row->cust_req_id)->first();

                // Always (re)sync chats and files even for already-migrated requests
                // — that way a partial previous run can be completed without --fresh.
                $resyncOnly = (bool) $existing;

                if ($resyncOnly) {
                    $newRequest = $existing;
                    $skipped++;
                    $this->syncLegacyChats($row, $newRequest, $legacyBaseUrl, $dry);
                    $this->syncLegacyFiles($row, $newRequest, $legacyBaseUrl, $dry);
                    $bar->advance();
                    continue;
                }

                $data = [
                    'cuid'                => (string) Str::ulid(),
                    'origin_cust_req_id'  => $row->cust_req_id,
                    'ref_number'          => $row->req_ref_num ?: ('LEG' . $row->cust_req_id),
                    'request_type'        => $row->request_type ?: 'customization',

                    // Customer
                    'user_id'             => $row->cust_user_id,
                    'user_email'          => $row->email,
                    'user_name'           => trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')),
                    'first_name'          => $row->first_name,
                    'last_name'           => $row->last_name,
                    'email'               => $row->email,
                    'phone'               => $row->phone,
                    'sec_phone'           => $row->sec_phone,

                    // Community → company_* fields (used by the new form)
                    'company_name'        => $row->community_name,
                    'company_phone'       => $row->community_handle_name,
                    'company_address'     => $row->community_domain_name,
                    'community_name'          => $row->community_name,
                    'community_handle_name'   => $row->community_handle_name,
                    'community_domain_name'   => $row->community_domain_name,

                    // Requirements
                    'req_logo'            => (bool) $row->req_logo,
                    'req_icon'            => (bool) $row->req_icon,
                    'req_app_background'  => (bool) $row->req_app_background,
                    'req_landing_page'    => (bool) $row->req_landing_page,
                    'req_others'          => (bool) $row->req_others,
                    'req_donation'        => (bool) $row->req_donation,
                    'req_primary_color'   => $row->req_primary_color,
                    'req_sec_color'       => $row->req_sec_color,
                    'request_description' => $row->request_description,
                    'request_donate_description' => $row->request_donate_description,
                    'additional_features' => (bool) ($row->additonal_features ?? 0), // legacy typo

                    // Status + payment
                    'status'              => $statusMap[$row->cust_status] ?? CustomizationRequest::STATUS_PENDING,
                    'pay_type'            => $row->cust_pay_type ?? 1,       // default free
                    'pay_amount'          => $row->cust_amount ?? 0,
                    'pay_status'          => $row->cust_pay_status ?? 0,     // default unpaid
                    'pay_id'              => $row->cust_pay_id,
                    'paid_at'             => $row->cust_paid_date,

                    // Assignment
                    'assigned_tech_id1'   => $row->technician_id1 ?: $row->tech_id,
                    'assigned_tech_name1' => $row->technician_name1 ?: $row->tech_name,
                    'assigned_tech_id2'   => $row->technician_id2,
                    'assigned_tech_name2' => $row->technician_name2,
                    'supervisor_id'       => $row->supervisor_id,
                    'supervisor_name'     => $row->supervisor_name,
                    'tech_receive_date'   => $row->tech_receive_date,
                    'tech_process_date'   => $row->tech_process_date,
                    'date_complete'       => $row->date_complete,
                    'num_of_days'         => $row->num_of_day,
                    'technician_comments' => $row->technician_comments,
                    'last_updated_by'     => $row->last_update_user_id,

                    'created_at'          => $row->created_at,
                    'updated_at'          => $row->updated_at,
                ];

                if ($dry) {
                    $migrated++;
                    $bar->advance();
                    continue;
                }

                // Use forceCreate so created_at/updated_at from legacy data
                // are preserved instead of being overwritten with now()
                $newRequest = CustomizationRequest::forceCreate($data);

                // Migrate related customization_questions (questionnaire answers)
                $questions = DB::connection('dashboard_db')
                    ->table('customization_questions')
                    ->where('cust_req_id', $row->cust_req_id)
                    ->first();

                if ($questions) {
                    $questionMap = [
                        'question_1'    => 'What domain name would you like to be displayed in your website?',
                        'question_2'    => 'What are your gifts, talents, products and/or services and what are you passionate about?',
                        'question_3'    => 'If you never got paid for it, what could you do for the rest of your life that brings you happiness?',
                        'question_4'    => 'List 5 things you love to do in order of importance.',
                        'question_5'    => 'How many followers do you have on other platforms',
                        'question_11'   => 'Do you have a thumbnail image for your content management or master courses?',
                        'question_12'   => 'Can you provide us with your website content for your landing page?',
                        'question_13'   => 'Can you provide us with your campaign content for your lead capture page?',
                        'question_14'   => 'Do you have product images for your e-commerce store?',
                        'question_15'   => 'Do you have a banner image for your e-commerce store?',
                        'question_16'   => 'Do you have any videos for your landing page, e-commerce store or master courses?',
                        'question_17'   => 'What would you like to do in your VIP to share your gift, talent, products and/or services?',
                        'requirement_1' => 'How will you use this order?',
                        'requirement_2' => 'Which industry is most relevant to your order?',
                        'requirement_3' => 'What are you looking to achieve with this order?',
                        'requirement_4' => 'Relevant data',
                    ];

                    foreach ($questionMap as $key => $text) {
                        $answer = $questions->$key ?? null;
                        if ($answer) {
                            CustomizationAnswer::create([
                                'request_id'    => $newRequest->id,
                                'question_key'  => $key,
                                'question_text' => $text,
                                'answer'        => $answer,
                            ]);
                        }
                    }

                    if (!empty($questions->login_email) || !empty($questions->password)) {
                        $newRequest->update([
                            'login_email'    => $questions->login_email,
                            'login_password' => $questions->password,
                        ]);
                    }

                    $newRequest->update(['origin_question_id' => $questions->id]);
                }

                // ============ Migrate related chats and files ============
                $this->syncLegacyChats($row, $newRequest, $legacyBaseUrl, $dry);
                $this->syncLegacyFiles($row, $newRequest, $legacyBaseUrl, $dry);

                $migrated++;
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error("  Row {$row->cust_req_id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Migrated: {$migrated}");
        $this->line("Skipped (already migrated): {$skipped}");
        if ($errors > 0) $this->error("Errors: {$errors}");

        if ($dry) {
            $this->warn('This was a dry run. Run without --dry-run to actually write the data.');
        }

        return self::SUCCESS;
    }

    private function parseSizeBytes($size): int
    {
        if (is_null($size) || $size === '') return 0;
        if (is_numeric($size)) return (int) $size;

        // Parse strings like "297.99 KB", "1.5 MB", etc.
        if (preg_match('/^([\d.]+)\s*(KB|MB|GB|B)?$/i', trim($size), $m)) {
            $val  = (float) $m[1];
            $unit = strtoupper($m[2] ?? 'B');
            return (int) match ($unit) {
                'KB' => $val * 1024,
                'MB' => $val * 1024 * 1024,
                'GB' => $val * 1024 * 1024 * 1024,
                default => $val,
            };
        }

        return 0;
    }

    /**
     * Prefix a relative legacy path with the dashboardv2 URL so it renders
     * from the original server. Full URLs are returned untouched.
     */
    private function normalizeLegacyPath(?string $path, string $legacyBaseUrl): ?string
    {
        if (empty($path)) return null;
        if (preg_match('#^https?://#i', $path)) return $path;
        return $legacyBaseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Sync chats for a legacy request. Idempotent — dedupes by
     * (request_id, created_at, sender_id, message) so re-runs don't duplicate.
     */
    private function syncLegacyChats($row, CustomizationRequest $newRequest, string $legacyBaseUrl, bool $dry): void
    {
        $legacyChats = DB::connection('dashboard_db')
            ->table('customization_chats')
            ->where(function ($q) use ($row) {
                $q->where('cust_req_id', $row->cust_req_id)
                  ->orWhere('customization_id', $row->cust_req_id);
            })
            ->orderBy('id')
            ->get();

        foreach ($legacyChats as $chat) {
            $isCustomer = ($chat->user_id && $row->cust_user_id && $chat->user_id == $row->cust_user_id);
            $senderType = $isCustomer ? 'user' : 'portal_user';

            // Resolve sender_name
            $senderName = null;
            if ($chat->user_id) {
                $u = DB::connection('dashboard_db')->table('users')
                    ->select('name', 'last_name')->where('id', $chat->user_id)->first();
                if ($u) $senderName = trim(($u->name ?? '') . ' ' . ($u->last_name ?? ''));
            }
            if (!$senderName) {
                $senderName = $isCustomer
                    ? trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''))
                    : ($row->technician_name1 ?? 'Staff');
            }

            // File path — rewrite to full dashboardv2 URL so it renders from the old server
            $localPath    = $this->normalizeLegacyPath($chat->file ?? null, $legacyBaseUrl);
            $originalName = $chat->file ? basename($chat->file) : null;

            // Dedup check: skip if a chat with the same created_at + sender_id + message already exists
            $exists = CustomizationChat::where('request_id', $newRequest->id)
                ->where('created_at', $chat->created_at)
                ->where(function ($q) use ($chat) {
                    $q->where('sender_id', $chat->user_id)
                      ->orWhereNull('sender_id');
                })
                ->where('message', $chat->comment)
                ->exists();

            if ($exists || $dry) continue;

            CustomizationChat::forceCreate([
                'request_id'        => $newRequest->id,
                'sender_type'       => $senderType,
                'sender_id'         => $chat->user_id,
                'sender_name'       => $senderName ?: 'Unknown',
                'message'           => $chat->comment,
                'local_path'        => $localPath,
                'bunny_path'        => null,
                'bunny_synced'      => false,
                'file_type'         => $chat->type,
                'original_filename' => $originalName,
                'read_by_user'      => true,
                'read_by_staff'     => true,
                'created_at'        => $chat->created_at,
                'updated_at'        => $chat->updated_at,
            ]);
        }
    }

    /**
     * Sync files for a legacy request. Dedupes by (request_id, original_name, created_at).
     */
    private function syncLegacyFiles($row, CustomizationRequest $newRequest, string $legacyBaseUrl, bool $dry): void
    {
        $legacyFiles = DB::connection('dashboard_db')
            ->table('customization_files')
            ->where('cust_id', $row->cust_req_id)
            ->orderBy('id')
            ->get();

        foreach ($legacyFiles as $file) {
            $originalName = $file->name ?: basename($file->files ?? '');
            $localPath    = $this->normalizeLegacyPath($file->files ?? null, $legacyBaseUrl);

            $exists = CustomizationFile::where('request_id', $newRequest->id)
                ->where('original_name', $originalName)
                ->where('created_at', $file->created_at)
                ->exists();

            if ($exists || $dry) continue;

            CustomizationFile::forceCreate([
                'request_id'       => $newRequest->id,
                'uploaded_by_type' => 'user',
                'uploaded_by_id'   => $file->user_id,
                'file_category'    => 'attachment',
                'original_name'    => $originalName,
                'extension'        => $file->extension,
                'size_bytes'       => $this->parseSizeBytes($file->size),
                'local_path'       => $localPath,
                'bunny_path'       => null,
                'bunny_synced'     => false,
                'created_at'       => $file->created_at,
                'updated_at'       => $file->updated_at,
            ]);
        }
    }
}
