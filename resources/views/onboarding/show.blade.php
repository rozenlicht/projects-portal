@extends('layouts.app')

@section('title', 'Complete Your Registration')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#7fabc9]/10 to-[#7fabc9]/20 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-[#7fabc9] to-[#5a8ba8] px-8 py-6">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-white mb-2">TU/e</h1>
                    <h2 class="text-xl text-white">Complete Your Registration</h2>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-8">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <ul class="list-disc list-inside text-red-800">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('onboarding.store', $token) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Welcome Message -->
                    <div class="mb-6">
                        <p class="text-gray-700 text-lg">Welcome, <strong>{{ $user->name }}</strong>!</p>
                        <p class="text-gray-600 mt-2">Please complete your account setup by filling in the information below.</p>
                    </div>

                    <!-- Avatar Upload -->
                    <div>
                        <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">
                            Profile Picture (Optional)
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div id="avatar-preview" class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border-2 border-gray-300">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file" 
                                       id="avatar" 
                                       name="avatar" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#7fabc9]/10 file:text-[#7fabc9] hover:file:bg-[#7fabc9]/20"
                                       onchange="previewAvatar(this)">
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-[#7fabc9]">*</span>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               minlength="8"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7fabc9] focus:border-[#7fabc9] sm:text-sm @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password <span class="text-[#7fabc9]">*</span>
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               minlength="8"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7fabc9] focus:border-[#7fabc9] sm:text-sm @error('password_confirmation') border-red-500 @enderror">
                    </div>

                    <!-- Group Selection -->
                    <div>
                        <label for="group_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Your Group <span class="text-[#7fabc9]">*</span>
                        </label>
                        <select id="group_id" 
                                name="group_id" 
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#7fabc9] focus:border-[#7fabc9] sm:text-sm @error('group_id') border-red-500 @enderror">
                            <option value="">-- Please select a group --</option>
                            @foreach($groups as $group)
                                <option value="{{ $group['id'] }}" {{ old('group_id') == $group['id'] ? 'selected' : '' }}>
                                    {{ $group['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('group_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#7fabc9] hover:bg-[#5a8ba8] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7fabc9] transition-colors">
                            Complete Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    const preview = document.getElementById('avatar-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Avatar preview" class="h-full w-full object-cover">';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';
    }
}
</script>
@endsection
