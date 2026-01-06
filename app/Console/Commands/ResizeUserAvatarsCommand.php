<?php

namespace App\Console\Commands;

use App\Jobs\ResizeUserAvatars;
use Illuminate\Console\Command;

class ResizeUserAvatarsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avatars:resize {--queue : Run the job in the queue instead of synchronously}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resize all user avatars to a maximum of 150x150 pixels';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting avatar resize process...');

        if ($this->option('queue')) {
            ResizeUserAvatars::dispatch();
            $this->info('Avatar resize job has been dispatched to the queue.');
        } else {
            $this->info('Running avatar resize synchronously...');
            try {
                $job = new ResizeUserAvatars();
                $job->handle();
                $this->info('✓ Avatar resize completed successfully!');
            } catch (\Exception $e) {
                $this->error('Failed to resize avatars: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}

