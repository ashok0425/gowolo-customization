<?php

namespace App\Console\Commands;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

/**
 * Generate a PDF documenting all the features built into the customization
 * portal — useful for presenting scope and progress to a contractor/client.
 *
 * Usage:
 *   php artisan docs:features
 *   php artisan docs:features --out=/tmp/report.pdf
 */
class GenerateFeaturesPdf extends Command
{
    protected $signature = 'docs:features {--out= : Output file path (default: storage/app/customization-portal-features.pdf)}';
    protected $description = 'Generate a feature documentation PDF for contractor review';

    public function handle(): int
    {
        $this->info('Generating customization portal features documentation...');

        $path = $this->option('out')
            ?: storage_path('app/customization-portal-features.pdf');

        $pdf = Pdf::loadView('pdf.features')
            ->setPaper('a4', 'portrait');

        $pdf->save($path);

        $this->info('Saved to: ' . $path);
        $this->line('Open with your PDF viewer or share with your contractor.');

        return self::SUCCESS;
    }
}
