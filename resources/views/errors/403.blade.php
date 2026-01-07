@extends('errors.layout')

@section('title', 'Forbidden')
@section('code', '403')
@section('heading', 'Access denied')
@section('message', 'You don’t have permission to view this page. If you believe this is a mistake, please contact us.')

@section('actions')
    <a href="{{ route('home') }}" class="bg-[#7fabc9] text-white px-5 py-2.5 rounded-lg font-medium hover:bg-[#5a8ba8] transition-colors text-sm sm:text-base text-center">
        Go to homepage
    </a>
    <a href="{{ route('contact') }}" class="bg-white text-[#7fabc9] border-2 border-[#7fabc9] px-5 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition-colors text-sm sm:text-base text-center">
        Contact us
    </a>
    <a href="{{ url('/admin/login') }}" class="text-sm text-gray-600 hover:text-gray-800 self-center sm:self-auto sm:ml-auto">
        Admin login
    </a>
@endsection


