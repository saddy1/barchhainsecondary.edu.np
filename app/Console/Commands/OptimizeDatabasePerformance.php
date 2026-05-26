<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class OptimizeDatabasePerformance extends Command
{
    protected $signature = 'app:optimize-database';
    protected $description = 'Optimize database tables and queries for faster performance';

    public function handle()
    {
        $this->info('🚀 Starting database optimization...');

        try {
            // Clear Laravel cache
            $this->call('cache:clear');
            $this->info('✓ Cache cleared');

            // Clear config cache
            $this->call('config:clear');
            $this->info('✓ Config cache cleared');

            // Rebuild autoloader using shell
            $this->rebuildComposerAutoloader();
            $this->info('✓ Composer autoloader rebuilt');

            // Clear query cache
            $this->call('view:clear');
            $this->info('✓ View cache cleared');

            // Try to optimize application (if command exists)
            try {
                $this->call('app:cache-all');
                $this->info('✓ Application optimized');
            } catch (\Exception $e) {
                $this->warn('⚠ app:cache-all not available (Laravel version specific)');
            }

            // Analyze tables (MySQL specific)
            if (config('database.default') === 'mysql') {
                $this->analyzeTablesMysql();
            }

            $this->info("\n✨ Database optimization completed!");
            $this->info("\n📊 Performance Tips:");
            $this->line("  • Enable query caching in config/database.php");
            $this->line("  • Use Redis for session/cache (recommended)");
            $this->line("  • Add database query indexes where needed");
            $this->line("  • Enable Gzip compression in web server");
            $this->line("  • Use lazy loading for large datasets");

        } catch (\Exception $e) {
            $this->error('Error during optimization: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function rebuildComposerAutoloader()
    {
        try {
            // Try using composer directly
            $process = new Process(['composer', 'dump-autoload', '--no-dev'], base_path());
            $process->run();

            if (!$process->isSuccessful()) {
                // Fallback: try via PHP
                $process = new Process(['php', 'composer.phar', 'dump-autoload', '--no-dev'], base_path());
                $process->run();
            }
        } catch (\Exception $e) {
            $this->warn('⚠ Could not run composer dump-autoload (may not be needed in all environments)');
        }
    }

    private function analyzeTablesMysql()
    {
        try {
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = ?", [
                config('database.connections.mysql.database')
            ]);

            if (empty($tables)) {
                $this->warn('⚠ No tables found to optimize');
                return;
            }

            $this->info("\n🔧 Analyzing tables...");

            foreach ($tables as $table) {
                DB::statement("ANALYZE TABLE `{$table->table_name}`");
            }

            $this->info('✓ Tables analyzed');

            // Optimize tables
            $this->info('🔧 Optimizing tables...');

            foreach ($tables as $table) {
                DB::statement("OPTIMIZE TABLE `{$table->table_name}`");
            }

            $this->info('✓ Tables optimized');
        } catch (\Exception $e) {
            $this->warn('⚠ Could not optimize tables: ' . $e->getMessage());
        }
    }
}
