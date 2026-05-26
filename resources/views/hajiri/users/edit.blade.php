@extends('hajiri.layouts.app')

@section('content')

{{-- Hero --}}
<div class="bg-[#1a5632] rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-6 relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-48 h-48 bg-[#e2a024] rounded-full blur-3xl opacity-20 pointer-events-none"></div>
    <div class="relative z-10 flex items-center gap-4">
        <a href="{{ route('hajiri.users.index') }}"
           class="shrink-0 flex items-center gap-1.5 px-3 py-2 bg-white/10 border border-white/20 hover:bg-white/20 text-white text-sm font-bold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-white/20 border-2 border-[#e2a024] flex items-center justify-center text-xl font-extrabold shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#e2a024] uppercase tracking-widest mb-0.5">Employee Profile</p>
                <h2 class="text-xl font-extrabold leading-tight">{{ $user->name }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Main form ── --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('hajiri.users.update', $user) }}" class="space-y-8">
                @csrf @method('PUT')

                {{-- Personal Info --}}
                <fieldset>
                    <div class="flex items-center gap-3 mb-5">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest whitespace-nowrap">Personal Info</p>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-3 py-2.5 text-sm border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }} rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10">
                            @error('name')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
                                   class="w-full px-3 py-2.5 text-sm border {{ $errors->has('phone') ? 'border-red-400' : 'border-gray-200' }} rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10">
                            @error('phone')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-3 py-2.5 text-sm border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }} rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10">
                            @error('email')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Device ID</label>
                            <input type="number" name="device_id" value="{{ old('device_id', $user->device_id) }}" required
                                   class="w-full px-3 py-2.5 text-sm border {{ $errors->has('device_id') ? 'border-red-400' : 'border-gray-200' }} rounded-xl focus:outline-none focus:border-[#1a5632] focus:ring-2 focus:ring-[#1a5632]/10">
                            @error('device_id')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Address --}}
                <fieldset>
                    <div class="flex items-center gap-3 mb-5">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest whitespace-nowrap">Address</p>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Province</label>
                            <select name="province" id="provinceID"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:border-[#1a5632]">
                                <option value="{{ $user->province }}">{{ $user->province }}</option>
                            </select>
                            @error('province')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">District</label>
                            <select name="district" id="districtID"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:border-[#1a5632]">
                                <option value="{{ $user->district }}">{{ $user->district }}</option>
                            </select>
                            @error('district')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Municipality</label>
                            <select name="municipal" id="municipalID"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:border-[#1a5632]">
                                <option value="{{ $user->municipal }}">{{ $user->municipal }}</option>
                            </select>
                            @error('municipal')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Employment --}}
                <fieldset>
                    <div class="flex items-center gap-3 mb-5">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest whitespace-nowrap">Employment Details</p>
                        <div class="flex-1 h-px bg-gray-100"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Designation</label>
                            <select name="designation_id" required
                                    class="w-full px-3 py-2.5 text-sm border {{ $errors->has('designation_id') ? 'border-red-400' : 'border-gray-200' }} rounded-xl bg-white focus:outline-none focus:border-[#1a5632]">
                                <option value="" disabled>Select Designation</option>
                                @foreach($desig as $d)
                                    <option value="{{ $d->id }}" {{ $d->id == old('designation_id', $user->designation_id) ? 'selected' : '' }}>{{ $d->label }}</option>
                                @endforeach
                            </select>
                            @error('designation_id')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Work Area</label>
                            <select name="work_assigned_id" required
                                    class="w-full px-3 py-2.5 text-sm border {{ $errors->has('work_assigned_id') ? 'border-red-400' : 'border-gray-200' }} rounded-xl bg-white focus:outline-none focus:border-[#1a5632]">
                                <option value="" disabled>Select Work Area</option>
                                @foreach($work_assigned as $w)
                                    <option value="{{ $w->id }}" {{ $w->id == old('work_assigned_id', $user->work_assigned_id) ? 'selected' : '' }}>{{ $w->label }}</option>
                                @endforeach
                            </select>
                            @error('work_assigned_id')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Employment Type</label>
                            <select name="employment_type_id" required
                                    class="w-full px-3 py-2.5 text-sm border {{ $errors->has('employment_type_id') ? 'border-red-400' : 'border-gray-200' }} rounded-xl bg-white focus:outline-none focus:border-[#1a5632]">
                                <option value="" disabled>Select Type</option>
                                @foreach($employmentType as $et)
                                    <option value="{{ $et->id }}" {{ $et->id == old('employment_type_id', $user->employment_type_id) ? 'selected' : '' }}>{{ $et->label }}</option>
                                @endforeach
                            </select>
                            @error('employment_type_id')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Status</label>
                            <select name="status" required
                                    class="w-full px-3 py-2.5 text-sm border {{ $errors->has('status') ? 'border-red-400' : 'border-gray-200' }} rounded-xl bg-white focus:outline-none focus:border-[#1a5632]">
                                <option value="1" {{ old('status', $user->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $user->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                <button type="submit"
                        class="w-full py-3 bg-[#1a5632] hover:bg-[#0b2415] text-white text-sm font-extrabold rounded-xl transition-colors">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    {{-- ── Side panel ── --}}
    <div class="space-y-5">

        {{-- Quick info card --}}
        <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5 space-y-3">
            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-3">Quick Info</p>
            <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-500">Designation</span>
                <span class="font-bold text-gray-800">{{ $user->designation->label ?? '—' }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-500">Work Area</span>
                <span class="font-bold text-gray-800">{{ $user->working_at->label ?? '—' }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-500">Device ID</span>
                <span class="font-bold font-mono text-gray-800">{{ $user->device_id ?? '—' }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-medium text-gray-500">Status</span>
                @if($user->status)
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-extrabold rounded-md uppercase">Active</span>
                @else
                    <span class="px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-extrabold rounded-md uppercase">Inactive</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $.loadScript('https://district.opensource.onezero.com.np/js/ekSunyeLocalBodyNepal.js', function(){
        $(document).on('click', '#provinceID', function(){
            if ($('#provinceID option').length === 1) {
                $('#provinceID').provinceSelect({ targetDistrict: '#districtID', targetMunicipal: '#municipalID' });
            }
        });
    });
</script>
@endpush
