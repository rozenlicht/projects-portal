<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\ProjectType;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectsStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $availableProjects = Project::available()->count();
        $pastProjects = Project::past()->count();
        $internships = Project::where('type', ProjectType::Internship->value)->count();
        $bachelorTheses = Project::where('type', ProjectType::BachelorThesis->value)->count();
        $masterTheses = Project::where('type', ProjectType::MasterThesis->value)->count();

        $totalProjectsPerMonthPast6Months = Project::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get()
            ->pluck('total')
            ->toArray();

        return [
            Stat::make('Total Projects', $totalProjects)
                ->description('All projects in the system')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary')
                ->chart(
                    [...$totalProjectsPerMonthPast6Months, $totalProjects]
                ),

            Stat::make('Available Projects', $availableProjects)
                ->description('Projects available for students')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Completed Projects', $pastProjects)
                ->description('Projects with assigned students')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info'),

            Stat::make('By Type', '')
                ->description("Internships: {$internships} | Bachelor: {$bachelorTheses} | Master: {$masterTheses}")
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('warning'),
        ];
    }
}
