@extends('errors.layout')

@section('title', 'Server Error')
@section('code', '500')
@section('heading', 'Something went wrong')
@section('message', 'An unexpected error occurred on our side. Please try again in a few minutes, or contact us if the problem persists.')

@section('actions')
    <a href="{{ route('home') }}" class="bg-[#7fabc9] text-white px-5 py-2.5 rounded-lg font-medium hover:bg-[#5a8ba8] transition-colors text-sm sm:text-base text-center">
        Go to homepage
    </a>
    <a href="{{ route('contact') }}" class="bg-white text-[#7fabc9] border-2 border-[#7fabc9] px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition-colors text-sm sm:text-base text-center">
        Contact us
    </a>
@endsection


