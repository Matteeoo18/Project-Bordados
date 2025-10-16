<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ClearTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan files:clear-temp
     */
    protected $signature = 'files:clear-temp';

    /**
     * The console command description.
     */
    protected $description = 'Elimina archivos temporales de la carpeta temp_uploads que tengan más de 30 minutos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 📁 Ruta base de tus archivos temporales
        $tempPath = storage_path('app/temp_uploads');

        // ⏳ Tiempo límite (30 minutos)
        $limit = Carbon::now()->subMinutes(30);

        // Si la carpeta no existe, salimos
        if (!File::exists($tempPath)) {
            $this->info('⚠️ La carpeta temp_uploads no existe.');
            return;
        }

        $files = File::allFiles($tempPath);
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(File::lastModified($file));
            if ($lastModified->lt($limit)) {
                File::delete($file);
                $deletedCount++;
            }
        }

        $this->info("✅ Se eliminaron {$deletedCount} archivos temporales antiguos.");
    }
}
