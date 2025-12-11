@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-12">
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('projects.index') }}" class="text-[#7fabc9] hover:text-[#5a8ba8] text-sm font-medium mb-3 sm:mb-4 inline-block">
            ← Back to Projects
        </a>
        
        @if($project->featured_image)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($project->featured_image) }}" alt="{{ $project->name }}" class="w-full h-48 sm:h-64 object-cover rounded-lg mb-4 sm:mb-6">
        @endif

        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-heading text-gray-900 mb-3 sm:mb-4">{{ $project->name }}</h1>
        
        <div class="flex flex-wrap items-center gap-2 sm:gap-4 mb-4 sm:mb-6">
            @foreach($project->types as $type)
                <span class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-[#7fabc9] text-white">
                    {{ $type->name }}
                </span>
            @endforeach
            @if($project->tags->count() > 0)
                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                    @foreach($project->tags as $tag)
                        @php
                            $colorClasses = match($tag->category->value) {
                                'group' => 'bg-blue-100 text-blue-800',
                                'nature' => 'bg-green-100 text-green-800',
                                'focus' => 'bg-amber-100 text-amber-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $colorClasses }}">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <article class="prose prose-slate prose-sm sm:prose-base max-w-none mb-8 sm:mb-12">
        {!! $project->richtext_content !!}
    </article>

    @if($project->supervisorLinks->count() > 0)
        @php
            $primarySupervisor = $project->supervisorLinks->first()->supervisor;
        @endphp
        <div class="border-t border-gray-200 pt-6 sm:pt-8 mt-8 sm:mt-12">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-4 sm:mb-6">Supervisors</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                {{-- Left: Supervisor List --}}
                <div class="space-y-3 sm:space-y-4">
                    @foreach($project->supervisorLinks as $index => $supervisorLink)
                        @php
                            $supervisor = $supervisorLink->supervisor;
                        @endphp
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            @if($supervisor->avatar_url)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($supervisor->avatar_url) }}" 
                                     alt="{{ $supervisor->name }}" 
                                     class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#7fabc9] flex items-center justify-center text-white font-semibold text-base sm:text-lg flex-shrink-0">
                                    {{ substr($supervisor->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                    <h3 class="font-semibold text-gray-900 text-sm sm:text-base break-words">{{ $supervisor->name }}</h3>
                                    @if($index === 0)
                                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-medium bg-[#7fabc9] text-white whitespace-nowrap">
                                            Primary Supervisor
                                        </span>
                                    @endif
                                </div>
                                <a href="#" 
                                   class="obfuscated-email text-[#7fabc9] hover:text-[#5a8ba8] text-xs sm:text-sm break-all"
                                   data-encoded="{{ bin2hex($supervisor->email) }}">
                                    {{ $supervisor->email }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Right: Details --}}
                <div>
                    <h3 class="text-lg sm:text-xl font-heading text-gray-900 mb-3 sm:mb-4">Details</h3>
                    <div class="space-y-2.5 sm:space-y-3">
                        @if($project->organization)
                            <div>
                                <span class="text-xs sm:text-sm font-medium text-gray-600">Organization:</span>
                                <div class="flex items-center gap-2 mt-1">
                                    @if($project->organization->logo)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($project->organization->logo) }}"
                                             alt="{{ $project->organization->name }}"
                                             class="w-6 h-6 sm:w-8 sm:h-8 object-contain flex-shrink-0">
                                    @endif
                                    @if($project->organization->url)
                                        <a href="{{ $project->organization->url }}" target="_blank" rel="noopener noreferrer" class="text-[#7fabc9] hover:text-[#5a8ba8] text-xs sm:text-sm break-words">
                                            {{ $project->organization->name }}
                                        </a>
                                    @else
                                        <p class="text-gray-900 text-xs sm:text-sm break-words">{{ $project->organization->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($primarySupervisor && $primarySupervisor->group)
                            <div>
                                <span class="text-xs sm:text-sm font-medium text-gray-600">Group:</span>
                                <p class="text-gray-900 text-xs sm:text-sm sm:text-base">{{ $primarySupervisor->group->name }}</p>
                            </div>
                        @endif
                        @if($primarySupervisor->group && $primarySupervisor->group->section)
                            <div>
                                <span class="text-xs sm:text-sm font-medium text-gray-600">Section:</span>
                                <p class="text-gray-900 text-xs sm:text-sm sm:text-base">{{ $primarySupervisor->group->section->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

