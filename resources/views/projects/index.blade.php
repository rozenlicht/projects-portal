@extends('layouts.app')

@section('title', 'Research Projects')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-heading text-gray-900 mb-4">Research Projects</h1>
            <p class="text-lg text-gray-600 mb-6">Explore available research opportunities</p>

            <div class="flex flex-wrap gap-6">
                <div class="flex flex-col">
                    <label for="type-filter" class="text-xs font-medium text-gray-600 mb-1">Filter by type</label>
                    <select id="type-filter" onchange="updateFilters('type', this.value)"
                        class="border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-[#7fabc9] focus:border-[#7fabc9]">
                        <option value="">All Projects</option>
                        <option value="internship" {{ request('type') === 'internship' ? 'selected' : '' }}>Internships
                        </option>
                        <option value="bachelor_thesis" {{ request('type') === 'bachelor_thesis' ? 'selected' : '' }}>
                            Bachelor Thesis Projects</option>
                        <option value="master_thesis" {{ request('type') === 'master_thesis' ? 'selected' : '' }}>Master
                            Thesis Projects</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="nature-filter" class="text-xs font-medium text-gray-600 mb-1">Project nature</label>
                    <select id="nature-filter" onchange="updateFilters('nature', this.value)"
                        class="border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-[#7fabc9] focus:border-[#7fabc9]">
                        <option value="">All</option>
                        @foreach ($natureTags as $tag)
                            <option value="{{ $tag->id }}"
                                {{ (string) request('nature') === (string) $tag->id ? 'selected' : '' }}>{{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="section-filter" class="text-xs font-medium text-gray-600 mb-1">Section</label>
                    <select id="section-filter" onchange="updateFilters('section', this.value)"
                        class="border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-[#7fabc9] focus:border-[#7fabc9]">
                        <option value="">All</option>
                        @foreach ($sectionTags as $tag)
                            <option value="{{ $tag->id }}"
                                {{ (string) request('section') === (string) $tag->id ? 'selected' : '' }}>{{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="focus-filter" class="text-xs font-medium text-gray-600 mb-1">Focus</label>
                    <select id="focus-filter" onchange="updateFilters('focus', this.value)"
                        class="border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-[#7fabc9] focus:border-[#7fabc9]">
                        <option value="">All</option>
                        @foreach ($focusTags as $tag)
                            <option value="{{ $tag->id }}"
                                {{ (string) request('focus') === (string) $tag->id ? 'selected' : '' }}>{{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="with-company-filter" class="text-xs font-medium text-gray-600 mb-1">In cooperation with a company</label>
                    <select id="with-company-filter" onchange="updateFilters('with_company', this.value)"
                        class="border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-[#7fabc9] focus:border-[#7fabc9]">
                        <option value="">All</option>
                        <option value="yes" {{ request('with_company') === 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ request('with_company') === 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>

            <script>
                function updateFilters(filterName, filterValue) {
                    // Get current filter values
                    const type = document.getElementById('type-filter').value;
                    const nature = document.getElementById('nature-filter').value;
                    const section = document.getElementById('section-filter').value;
                    const focus = document.getElementById('focus-filter').value;
                    const withCompany = document.getElementById('with-company-filter').value;

                    // Build new params with all current filter values
                    const params = new URLSearchParams();
                    if (type) params.set('type', type);
                    if (nature) params.set('nature', nature);
                    if (section) params.set('section', section);
                    if (focus) params.set('focus', focus);
                    if (withCompany) params.set('with_company', withCompany);

                    // Navigate with all filters
                    const queryString = params.toString();
                    window.location.href = '{{ route('projects.index') }}' + (queryString ? '?' + queryString : '');
                }
            </script>
        </div>

        @if ($projects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($projects as $project)
                    <a href="{{ route('projects.show', $project) }}"
                        class="group bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        @if ($project->featured_image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($project->featured_image) }}"
                                alt="{{ $project->name }}" class="w-full h-48 object-cover">
                        @else
                            <div
                                class="w-full h-48 bg-gradient-to-br from-[#7fabc9] to-[#5a8ba8] flex items-center justify-center">
                                <span class="text-white text-2xl font-bold">{{ substr($project->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-2">
                                <h2
                                    class="text-lg font-heading text-gray-900 group-hover:text-[#7fabc9] transition-colors flex-1">
                                    {{ $project->name }}</h2>
                                @if ($project->organization && $project->organization->logo)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($project->organization->logo) }}"
                                        alt="{{ $project->organization->name }}"
                                        class="w-12 h-12 object-contain ml-2 flex-shrink-0">
                                @endif
                            </div>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $project->short_description }}</p>

                            @if ($project->tags->count() > 0)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach ($project->tags as $tag)
                                        @php
                                            $colorClasses = match ($tag->category->value) {
                                                'group' => 'bg-blue-100 text-blue-800',
                                                'nature' => 'bg-green-100 text-green-800',
                                                'focus' => 'bg-amber-100 text-amber-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $colorClasses }}">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($project->supervisors->count() > 0)
                                <div class="flex items-center space-x-2 pt-4 border-t border-gray-200">
                                    <span class="text-sm text-gray-500">Supervisors:</span>
                                    <div class="flex -space-x-2">
                                        @foreach ($project->supervisors->take(3) as $index => $supervisor)
                                            <div class="relative {{ $index === 0 ? 'z-30' : ($index === 1 ? 'z-20' : 'z-10') }} w-8 h-8 rounded-full border-2 border-white overflow-hidden"
                                                title="{{ $supervisor->name }}">
                                                @if ($supervisor->avatar_url)
                                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($supervisor->avatar_url) }}"
                                                        alt="{{ $supervisor->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-[#7fabc9] flex items-center justify-center text-white text-xs font-medium">
                                                        {{ substr($supervisor->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if ($project->supervisors->count() > 3)
                                            <div
                                                class="relative z-0 w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-xs font-medium border-2 border-white">
                                                +{{ $project->supervisors->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-600 ml-2">
                                        @foreach ($project->supervisors->take(2) as $supervisor)
                                            {{ $supervisor->name }}@if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                        @if ($project->supervisors->count() > 2)
                                            <span class="text-gray-500">+{{ $project->supervisors->count() - 2 }}
                                                more</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center pt-2 divide-x divide-gray-400">
                                    @if ($project->section)
                                        <div class="text-sm text-gray-500 pr-3">{{ $project->section->name }}</div>
                                    @endif
                                    @if ($project->group)
                                        <div class="text-sm text-gray-500 pl-3">{{ $project->group->name }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $projects->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-600 text-lg">No projects available at the moment.</p>
            </div>
        @endif
    </div>
@endsection
