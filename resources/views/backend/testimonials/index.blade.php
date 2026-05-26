@extends('layouts.admin')
@section('title', 'Manage Testimonials')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Testimonials & Reviews</h2>
        <p class="text-sm text-gray-500 mt-1">Add parent or student reviews and assign them to specific pages.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 text-sm font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Global Error Summary (Optional, but good for visibility) --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm font-bold shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                Please fix the errors below to continue.
            </div>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-8">
        
        {{-- Add Form --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <h3 class="font-bold text-xl mb-6 text-[#0b2415] border-b pb-4">Add New Testimonial</h3>
                
                <form action="{{ route('admin.testimonials.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    {{-- Full Name --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Ram Sharma" 
                               class="w-full px-4 py-3 border {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-300 focus:border-[#1a5632] focus:ring-[#1a5632]/20' }} rounded-xl text-sm transition-all bg-gray-50 focus:bg-white placeholder-gray-400">
                        @error('name')
                            <p class="text-red-500 text-xs font-bold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Role / Relation <span class="text-red-500">*</span></label>
                        <input type="text" name="role" value="{{ old('role') }}" required placeholder="e.g., Parent of Grade 8 Student" 
                               class="w-full px-4 py-3 border {{ $errors->has('role') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-300 focus:border-[#1a5632] focus:ring-[#1a5632]/20' }} rounded-xl text-sm transition-all bg-gray-50 focus:bg-white placeholder-gray-400">
                        @error('role')
                            <p class="text-red-500 text-xs font-bold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Assign to Page <span class="text-red-500">*</span></label>
                        <select name="category" required 
                                class="w-full px-4 py-3 border {{ $errors->has('category') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-300 focus:border-[#1a5632] focus:ring-[#1a5632]/20' }} rounded-xl text-sm transition-all bg-gray-50 focus:bg-white cursor-pointer">
                            <option value="home" {{ old('category') == 'home' ? 'selected' : '' }}>Homepage (General)</option>
                            <option value="elementary" {{ old('category') == 'elementary' ? 'selected' : '' }}>Kids School (Nursery - Gr 3)</option>
                            <option value="primary" {{ old('category') == 'primary' ? 'selected' : '' }}>Middle School (Gr 4 - 8)</option>
                            <option value="secondary" {{ old('category') == 'secondary' ? 'selected' : '' }}>High School (+2 Level)</option>
                        </select>
                        @error('category')
                            <p class="text-red-500 text-xs font-bold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Content --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Review Content <span class="text-red-500">*</span></label>
                        {{-- Fixed the name from "info" to "content" to match DB --}}
                        <textarea name="info" required rows="5" placeholder="Enter the testimonial here..." 
                                  class="w-full px-4 py-3 border {{ $errors->has('info') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-gray-300 focus:border-[#1a5632] focus:ring-[#1a5632]/20' }} rounded-xl text-sm transition-all bg-gray-50 focus:bg-white resize-none placeholder-gray-400">{{ old('info') }}</textarea>
                        @error('info')
                            <p class="text-red-500 text-xs font-bold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active Toggle --}}
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                                   class="w-5 h-5 rounded border-gray-300 text-[#1a5632] focus:ring-[#1a5632] transition-colors">
                            <span class="text-sm font-bold text-gray-800">Publish Immediately</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#1a5632] text-white font-bold py-3.5 rounded-xl hover:bg-[#0b2415] hover:shadow-lg hover:-translate-y-0.5 transition-all">
                            Save Testimonial
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- List --}}
        <div class="lg:col-span-2 space-y-5">
            @forelse($testimonials as $test)
            @php
                $initials = collect(explode(' ', $test->name))->map(fn($n) => substr($n, 0, 1))->take(2)->implode('');
            @endphp
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border {{ $test->is_active ? 'border-l-4 border-l-[#1a5632]' : 'border-l-4 border-l-gray-300 opacity-75' }} transition-all hover:shadow-md">
                <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-6">
                    
                    {{-- Avatar --}}
                    <div class="w-14 h-14 rounded-full bg-[#1a5632] text-[#e2a024] flex items-center justify-center font-bold text-xl shrink-0 shadow-inner">
                        {{ strtoupper($initials) }}
                    </div>
                    
                    {{-- Details --}}
                    <div class="flex-1 w-full">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-3">
                            <div>
                                <h4 class="font-bold text-lg text-gray-900 leading-tight">{{ $test->name }}</h4>
                                <p class="text-sm text-gray-500 font-medium">{{ $test->role }}</p>
                            </div>
                            <span class="inline-block text-[10px] font-bold uppercase px-3 py-1.5 rounded-md bg-blue-50 text-blue-700 tracking-wider shadow-sm border border-blue-100">
                                {{ $test->category }}
                            </span>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4 mb-5 border border-gray-100">
                            <p class="text-sm text-gray-700 leading-relaxed italic">"{{ $test->content }}"</p>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-3">
                            <form action="{{ route('admin.testimonials.toggle', $test->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-bold px-4 py-2.5 rounded-lg border transition-colors {{ $test->is_active ? 'bg-green-50 border-green-200 text-green-700 hover:bg-green-100' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100' }}">
                                    {{ $test->is_active ? 'Status: Active (Turn Off)' : 'Status: Draft (Turn On)' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.testimonials.destroy', $test->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this testimonial?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold px-4 py-2.5 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
                <div class="flex flex-col items-center justify-center py-20 bg-white border border-gray-100 rounded-3xl shadow-sm">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <p class="text-gray-500 font-medium text-lg">No testimonials added yet.</p>
                    <p class="text-sm text-gray-400 mt-1">Use the form to add your first review.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection