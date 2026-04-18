<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

/**
 * Generate a brief work-overview PDF suitable for client presentation.
 * Includes an "Added Scope of Work" section for extra deliverables.
 *
 * Usage:
 *   php artisan docs:overview
 *   php artisan docs:overview --out=/tmp/overview.pdf
 */
class GenerateWorkOverviewPdf extends Command
{
    protected $signature = 'docs:overview {--out= : Output file path (default: storage/app/customization-portal-work-overview.pdf)}';
    protected $description = 'Generate a brief work-overview PDF for client presentation';

    public function handle(): int
    {
        $this->info('Generating work overview PDF...');

        $path = $this->option('out')
            ?: storage_path('app/customization-portal-work-overview.pdf');

        $pdf = Pdf::loadView('pdf.work-overview')->setPaper('a4', 'portrait');
        $pdf->save($path);

        $this->info('Saved to: ' . $path);
        $this->line('Share this with your client.');

        return self::SUCCESS;
    }
}
