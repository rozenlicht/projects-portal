@extends('errors.layout')

@section('title', 'Page Expired')
@section('code', '419')
@section('heading', 'Page expired')
@section('message', 'Your session has expired or the form took too long to submit. Please refresh the page and try again.')

@section('actions')
    <a href="{{ request()->fullUrl() }}" class="bg-[#7fabc9] text-white px-5 py-2.5 rounded-lg font-medium hover:bg-[#5a8ba8] transition-colors text-sm sm:text-base text-center">
        Refresh
    </a>
    <a href="{{ url()->previous() }}" class="bg-white text-[#7fabc9] border-2 border-[#7fabc9] px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition-colors text-sm sm:text-base text-center">
        Go back
    </a>
@endsection


