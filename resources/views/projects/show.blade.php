@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="{{ route('projects.index') }}" class="text-[#7fabc9] hover:text-[#5a8ba8] text-sm font-medium mb-4 inline-block">
            ← Back to Projects
        </a>
        
        @if($project->featured_image)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($project->featured_image) }}" alt="{{ $project->name }}" class="w-full h-64 object-cover rounded-lg mb-6">
        @endif

        <h1 class="text-4xl font-heading text-gray-900 mb-4">{{ $project->name }}</h1>
        
        <div class="flex items-center space-x-4 mb-6">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[#7fabc9] text-white">
                @if($project->type === 'internship')
                    Internship
                @elseif($project->type === 'bachelor_thesis')
                    Bachelor Thesis Project
                @else
                    Master Thesis Project
                @endif
            </span>
            @if($project->tags->count() > 0)
                <div class="flex flex-wrap gap-2">
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

    <article class="prose prose-slate max-w-none mb-12">
        {!! $project->richtext_content !!}
    </article>

    @if($project->organization && $project->supervisors->count() === 0)
        <div class="border-t border-gray-200 pt-8 mt-12">
            <h2 class="text-2xl font-heading text-gray-900 mb-6">Organization</h2>
            <div class="flex items-center gap-3">
                @if($project->organization->logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($project->organization->logo) }}"
                         alt="{{ $project->organization->name }}"
                         class="w-16 h-16 object-contain">
                @endif
                <div>
                    @if($project->organization->url)
                        <a href="{{ $project->organization->url }}" target="_blank" rel="noopener noreferrer" class="text-xl font-semibold text-[#7fabc9] hover:text-[#5a8ba8]">
                            {{ $project->organization->name }}
                        </a>
                    @else
                        <h3 class="text-xl font-semibold text-gray-900">{{ $project->organization->name }}</h3>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($project->supervisors->count() > 0)
        @php
            $primarySupervisor = $project->supervisors->first();
        @endphp
        <div class="border-t border-gray-200 pt-8 mt-12">
            <h2 class="text-2xl font-heading text-gray-900 mb-6">Supervisors</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Left: Supervisor List --}}
                <div class="space-y-4">
                    @foreach($project->supervisors as $index => $supervisor)
                        <div class="flex items-center space-x-4">
                            @if($supervisor->avatar_url)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($supervisor->avatar_url) }}" 
                                     alt="{{ $supervisor->name }}" 
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-[#7fabc9] flex items-center justify-center text-white font-semibold text-lg">
                                    {{ substr($supervisor->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-gray-900">{{ $supervisor->name }}</h3>
                                    @if($index === 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#7fabc9] text-white">
                                            Primary Supervisor
                                        </span>
                                    @endif
                                </div>
                                <a href="#" 
                                   class="obfuscated-email text-[#7fabc9] hover:text-[#5a8ba8] text-sm"
                                   data-encoded="{{ bin2hex($supervisor->email) }}">
                                    {{ $supervisor->email }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Right: Details --}}
                <div>
                    <h3 class="text-xl font-heading text-gray-900 mb-4">Details</h3>
                    <div class="space-y-3">
                        @if($project->organization)
                            <div>
                                <span class="text-sm font-medium text-gray-600">Organization:</span>
                                <div class="flex items-center gap-2 mt-1">
                                    @if($project->organization->logo)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($project->organization->logo) }}"
                                             alt="{{ $project->organization->name }}"
                                             class="w-8 h-8 object-contain">
                                    @endif
                                    @if($project->organization->url)
                                        <a href="{{ $project->organization->url }}" target="_blank" rel="noopener noreferrer" class="text-[#7fabc9] hover:text-[#5a8ba8] text-sm">
                                            {{ $project->organization->name }}
                                        </a>
                                    @else
                                        <p class="text-gray-900 text-sm">{{ $project->organization->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($primarySupervisor && $primarySupervisor->group)
                            <div>
                                <span class="text-sm font-medium text-gray-600">Group:</span>
                                <p class="text-gray-900">{{ $primarySupervisor->group->name }}</p>
                            </div>
                        @endif
                        @if($primarySupervisor->group && $primarySupervisor->group->section)
                            <div>
                                <span class="text-sm font-medium text-gray-600">Section:</span>
                                <p class="text-gray-900">{{ $primarySupervisor->group->section->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

