@extends('errors.layout')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('heading', 'Too many requests')
@section('message', 'You’re doing that a bit too quickly. Please wait a moment and try again.')

@section('actions')
    <a href="{{ request()->fullUrl() }}" class="bg-[#7fabc9] text-white px-5 py-2.5 rounded-lg font-medium hover:bg-[#5a8ba8] transition-colors text-sm sm:text-base text-center">
        Try again
    </a>
    <a href="{{ route('home') }}" class="bg-white text-[#7fabc9] border-2 border-[#7fabc9] px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition-colors text-sm sm:text-base text-center">
        Go to homepage
    </a>
@endsection


