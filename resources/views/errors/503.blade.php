@extends('errors.layout')

@section('title', 'Maintenance')
@section('code', '503')
@section('heading', 'Down for maintenance')
@section('message', 'We’re performing a quick maintenance update. Please try again in a moment.')

@section('head')
    <meta http-equiv="refresh" content="20">
@endsection

@section('actions')
    <a href="{{ request()->fullUrl() }}" class="bg-[#7fabc9] text-white px-5 py-2.5 rounded-lg font-medium hover:bg-[#5a8ba8] transition-colors text-sm sm:text-base text-center">
        Try again
    </a>
    <a href="{{ route('home') }}" class="bg-white text-[#7fabc9] border-2 border-[#7fabc9] px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition-colors text-sm sm:text-base text-center">
        Go to homepage
    </a>
@endsection


