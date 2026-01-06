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
        
        $bachelorType = ProjectType::where('slug', 'bachelor_thesis')->first();
        $masterType = ProjectType::where('slug', 'master_thesis')->first();
        
        $bachelorTheses = $bachelorType ? Project::available()->whereHas('types', fn($q) => $q->where('project_types.id', $bachelorType->id))->count() : 0;
        $masterTheses = $masterType ? Project::available()->whereHas('types', fn($q) => $q->where('project_types.id', $masterType->id))->count() : 0;

        $totalProjectsPerMonthPast6Months = Project::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get()
            ->pluck('total')
            ->toArray();

        $totalAvailableProjectsPerMonthPast6Months = Project::query()
            ->available()
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
                ->color('success')
                ->chart(
                    [...$totalAvailableProjectsPerMonthPast6Months, $availableProjects]
                ),

            Stat::make('Completed Projects', $pastProjects)
                ->description('Projects with assigned students')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info'),

            Stat::make('Currently available by Type', $bachelorTheses + $masterTheses)
                ->description("Bachelor: {$bachelorTheses} | Master: {$masterTheses}")
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('warning'),
        ];
    }
}
