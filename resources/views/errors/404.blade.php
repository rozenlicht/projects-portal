@extends('errors.layout')

@section('title', 'Not Found')
@section('code', '404')
@section('heading', 'Page not found')
@section('message', 'The page you’re looking for doesn’t exist, was moved, or is temporarily unavailable.')

@section('actions')
    <a href="{{ route('projects.index') }}" class="bg-[#7fabc9] text-white px-5 py-2.5 rounded-lg font-medium hover:bg-[#5a8ba8] transition-colors text-sm sm:text-base text-center">
        Browse projects
    </a>
    <a href="{{ route('home') }}" class="bg-white text-[#7fabc9] border-2 border-[#7fabc9] px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition-colors text-sm sm:text-base text-center">
        Go to homepage
    </a>
@endsection

@section('details')
    Requested URL: <span class="font-mono">{{ request()->fullUrl() }}</span>
@endsection


