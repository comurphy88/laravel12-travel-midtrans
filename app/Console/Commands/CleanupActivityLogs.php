<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class CleanupActivityLogs extends Command
{
    protected $signature = 'activity-logs:cleanup {--days=90 : Jumlah hari untuk dipertahankan}';

    protected $description = 'Hapus activity logs yang lebih lama dari periode retensi yang ditentukan';

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Menghapus activity logs sebelum {$cutoffDate->format('Y-m-d H:i:s')}...");

        $deleted = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("✅ Berhasil dihapus: {$deleted} logs");
        
        return Command::SUCCESS;
    }
}
