@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-5xl font-heading text-gray-900 mb-4">Welcome to the CEM Projects Portal</h1>
        <p class="text-xl text-gray-600 mb-6">
            Welcome to the website of the Computational and Experimental Mechanics Division at Eindhoven University of Technology!
        </p>
        <div class="max-w-4xl mx-auto mb-8">
            <p class="text-lg text-gray-700 leading-relaxed">
                The Computational and Experimental Mechanics (CEM) Division is one of three core divisions within the Department of Mechanical Engineering. Our division encompasses three research sections that drive innovation in materials science, manufacturing, and microsystems technology. Through the <strong>Mechanics of Materials (MoM)</strong> section, we explore advanced material behavior and design. The <strong>Processing and Performance (PP)</strong> section focuses on manufacturing processes and material performance optimization. Our <strong>Microsystems (MS)</strong> section pioneers research in microscale systems and technologies. Together, these sections offer cutting-edge research opportunities across advanced manufacturing, energy conversion and storage, and computational engineering, providing students with world-class projects in both computational and experimental domains.
            </p>
        </div>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('projects.index') }}" class="bg-[#7fabc9] text-white px-6 py-3 rounded-lg font-medium hover:bg-[#5a8ba8] transition-colors">
                Browse Projects
            </a>
            <a href="{{ route('contact') }}" class="bg-white text-[#7fabc9] border-2 border-[#7fabc9] px-6 py-3 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                Contact Us
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
        <div class="text-center p-6 bg-gray-50 rounded-lg">
            <h2 class="text-2xl font-heading text-gray-900 mb-2">Internships</h2>
            <p class="text-gray-600">Explore internship opportunities in our research department</p>
            <a href="{{ route('projects.index', ['type' => 'internship']) }}" class="text-[#7fabc9] hover:text-[#5a8ba8] font-medium mt-4 inline-block">
                View Internships →
            </a>
        </div>
        <div class="text-center p-6 bg-gray-50 rounded-lg">
            <h2 class="text-2xl font-heading text-gray-900 mb-2">Bachelor Thesis Projects</h2>
            <p class="text-gray-600">Find bachelor thesis projects that match your interests</p>
            <a href="{{ route('projects.index', ['type' => 'bachelor_thesis']) }}" class="text-[#7fabc9] hover:text-[#5a8ba8] font-medium mt-4 inline-block">
                View Projects →
            </a>
        </div>
        <div class="text-center p-6 bg-gray-50 rounded-lg">
            <h2 class="text-2xl font-heading text-gray-900 mb-2">Master Thesis Projects</h2>
            <p class="text-gray-600">Discover advanced research projects for master students</p>
            <a href="{{ route('projects.index', ['type' => 'master_thesis']) }}" class="text-[#7fabc9] hover:text-[#5a8ba8] font-medium mt-4 inline-block">
                View Projects →
            </a>
        </div>
    </div>
</div>
@endsection
