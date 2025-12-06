@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-12">
    <div class="text-center mb-8 sm:mb-12">
        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-heading text-gray-900 mb-3 sm:mb-4 px-2">Welcome to the CEM Projects Portal</h1>
        <p class="text-base sm:text-lg md:text-xl text-gray-600 mb-4 sm:mb-6 px-2">
            Welcome to the website of the Computational and Experimental Mechanics Division at Eindhoven University of Technology!
        </p>
        <div class="max-w-4xl mx-auto mb-6 sm:mb-8 px-2">
            <p class="text-sm sm:text-base md:text-lg text-gray-700 leading-relaxed">
                The Computational and Experimental Mechanics (CEM) Division is one of three core divisions within the Department of Mechanical Engineering. Our division encompasses three research sections that drive innovation in materials science, manufacturing, and microsystems technology. Through the <strong>Mechanics of Materials (MoM)</strong> section, we explore advanced material behavior and design. The <strong>Processing and Performance (PP)</strong> section focuses on manufacturing processes and material performance optimization. Our <strong>Microsystems (MS)</strong> section pioneers research in microscale systems and technologies. Together, these sections offer cutting-edge research opportunities across advanced manufacturing, energy conversion and storage, and computational engineering, providing students with world-class projects in both computational and experimental domains.
            </p>
        </div>
        <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 px-2">
            <a href="{{ route('projects.index') }}" class="bg-[#7fabc9] text-white px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg font-medium hover:bg-[#5a8ba8] transition-colors text-sm sm:text-base">
                Browse Projects
            </a>
            <a href="{{ route('contact') }}" class="bg-white text-[#7fabc9] border-2 border-[#7fabc9] px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg font-medium hover:bg-gray-50 transition-colors text-sm sm:text-base">
                Contact Us
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 mt-8 sm:mt-12 lg:mt-16">
        <div class="text-center p-4 sm:p-6 bg-gray-50 rounded-lg">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-2">Internships</h2>
            <p class="text-sm sm:text-base text-gray-600">Explore internship opportunities in our research department</p>
            <a href="{{ route('projects.index', ['type' => 'internship']) }}" class="text-[#7fabc9] hover:text-[#5a8ba8] font-medium mt-3 sm:mt-4 inline-block text-sm sm:text-base">
                View Internships →
            </a>
        </div>
        <div class="text-center p-4 sm:p-6 bg-gray-50 rounded-lg">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-2">Bachelor Thesis Projects</h2>
            <p class="text-sm sm:text-base text-gray-600">Find bachelor thesis projects that match your interests</p>
            <a href="{{ route('projects.index', ['type' => 'bachelor_thesis']) }}" class="text-[#7fabc9] hover:text-[#5a8ba8] font-medium mt-3 sm:mt-4 inline-block text-sm sm:text-base">
                View Projects →
            </a>
        </div>
        <div class="text-center p-4 sm:p-6 bg-gray-50 rounded-lg">
            <h2 class="text-xl sm:text-2xl font-heading text-gray-900 mb-2">Master Thesis Projects</h2>
            <p class="text-sm sm:text-base text-gray-600">Discover advanced research projects for master students</p>
            <a href="{{ route('projects.index', ['type' => 'master_thesis']) }}" class="text-[#7fabc9] hover:text-[#5a8ba8] font-medium mt-3 sm:mt-4 inline-block text-sm sm:text-base">
                View Projects →
            </a>
        </div>
    </div>
</div>
@endsection
