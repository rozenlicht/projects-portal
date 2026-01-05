<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

class GenerateProjectNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:generate-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate project numbers for all existing projects that don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating project numbers for existing projects...');

        $projects = Project::whereNull('project_number')
            ->orWhere('project_number', '')
            ->get();

        if ($projects->isEmpty()) {
            $this->info('No projects found without project numbers.');
            return Command::SUCCESS;
        }

        $this->info("Found {$projects->count()} projects without project numbers.");

        $bar = $this->output->createProgressBar($projects->count());
        $bar->start();

        $successCount = 0;
        $failedCount = 0;

        foreach ($projects as $project) {
            try {
                // Load supervisor links with relationships
                $project->load('supervisorLinks.supervisor.group.section');
                
                $projectNumber = $project->generateProjectNumber();
                
                if ($projectNumber) {
                    $successCount++;
                } else {
                    $failedCount++;
                    $this->newLine();
                    $this->warn("Could not generate project number for project ID {$project->id} ({$project->name}) - missing supervisor, group, or section information.");
                }
            } catch (\Exception $e) {
                $failedCount++;
                $this->newLine();
                $this->error("Error generating project number for project ID {$project->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Completed!");
        $this->info("Successfully generated: {$successCount}");
        if ($failedCount > 0) {
            $this->warn("Failed to generate: {$failedCount}");
        }

        return Command::SUCCESS;
    }
}
