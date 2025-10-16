<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define el schedule de comandos de la aplicación.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 🔥 Ejecuta cada 30 minutos para limpiar archivos temporales
        $schedule->command('files:clear-temp')->everyThirtyMinutes();
    }

    /**
     * Registra los comandos de la aplicación.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
