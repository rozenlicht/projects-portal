@extends('errors.layout')

@section('title', 'Unauthorized')
@section('code', '401')
@section('heading', 'Please sign in')
@section('message', 'You need to be authenticated to access this page.')

@section('actions')
    <a href="{{ url('/admin/login') }}" class="bg-[#7fabc9] text-white px-5 py-2.5 rounded-lg font-medium hover:bg-[#5a8ba8] transition-colors text-sm sm:text-base text-center">
        Admin login
    </a>
    <a href="{{ route('home') }}" class="bg-white text-[#7fabc9] border-2 border-[#7fabc9] px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition-colors text-sm sm:text-base text-center">
        Go to homepage
    </a>
@endsection


