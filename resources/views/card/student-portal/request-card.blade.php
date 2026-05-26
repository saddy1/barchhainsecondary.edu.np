@extends('card.student-portal.layout')
@section('title', 'ID Card Request')

@section('content')
@php
    $status = $cardRequest?->status ?? 'not_requested';
    $statusMap = [
        'not_requested' => ['Not Requested', 'bg-gray-100 text-gray-700', 'Submit a request to start card processing.'],
        'pending' => ['Payment Pending', 'bg-amber-100 text-amber-700', 'Complete payment and visit the admin office with proof.'],
        'approved' => ['Approved', 'bg-green-100 text-green-700', 'Your card is ready to collect.'],
        'collected' => ['Collected', 'bg-blue-100 text-blue-700', 'Your card has already been collected.'],
        'rejected' => ['Rejected', 'bg-red-100 text-red-700', 'Please contact the admin office for details.'],
    ];
    [$label, $class, $message] = $statusMap[$status] ?? $statusMap['not_requested'];
@endphp

<div class="mx-auto max-w-6xl space-y-6">
    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">ID Card Module</p>
                <h1 class="mt-2 text-2xl font-extrabold text-gray-950">Student ID Card Request</h1>
                <p class="mt-1 text-sm font-medium text-gray-500">{{ $message }}</p>
            </div>
            <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-extrabold {{ $class }}">{{ $label }}</span>
        </div>
    </section>

    <section class="grid gap-5 lg:grid-cols-[1fr_360px]">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            @if(!$cardRequest || $cardRequest->status === 'rejected')
                <h2 class="text-lg font-extrabold text-gray-950">{{ $cardRequest?->status === 'rejected' ? 'Submit New Request' : 'Request Your ID Card' }}</h2>
                <p class="mt-2 text-sm font-medium leading-6 text-gray-500">
                    Before submitting, confirm that your profile photo and personal details are correct.
                </p>
                @if($cardRequest?->admin_note)
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                        Previous note: {{ $cardRequest->admin_note }}
                    </div>
                @endif
                <div class="mt-5 rounded-2xl border border-blue-100 bg-blue-50 p-4">
                    <p class="text-sm font-extrabold text-blue-950">Checklist</p>
                    <ul class="mt-2 space-y-2 text-sm font-semibold text-blue-800">
                        <li>Clear passport-size photo is uploaded.</li>
                        <li>Name, class, section, and roll number are correct.</li>
                        <li>Contact details are updated.</li>
                    </ul>
                </div>
                <form method="POST" action="{{ route('student.request-card') }}" class="mt-6">
                    @csrf
                    <button class="w-full rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415]">Submit ID Card Request</button>
                </form>
            @elseif($cardRequest->status === 'pending')
                <h2 class="text-lg font-extrabold text-gray-950">Payment Details</h2>
                <p class="mt-2 text-sm font-medium text-gray-500">Pay the card fee and submit proof to the admin office.</p>
                <div class="mt-5 rounded-2xl bg-gray-50 p-6 text-center">
                    @if(file_exists(public_path('img/payment_qr.png')))
                        <img src="{{ asset('img/payment_qr.png') }}" alt="Payment QR Code" class="mx-auto h-56 w-56 rounded-xl border-4 border-white object-contain shadow-sm">
                    @else
                        <div class="mx-auto flex h-56 w-56 flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-white text-gray-400">
                            <p class="text-sm font-extrabold">QR not configured</p>
                            <p class="mt-1 text-xs font-medium">Contact admin office.</p>
                        </div>
                    @endif
                    <p class="mt-4 text-3xl font-extrabold text-[#1a5632]">Rs 200</p>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400">ID Card Fee</p>
                </div>
            @elseif($cardRequest->status === 'approved')
                <h2 class="text-lg font-extrabold text-green-700">Card Approved</h2>
                <p class="mt-2 text-sm font-medium leading-6 text-gray-600">Your ID card is ready. Visit the admin/card office with your payment slip to collect it.</p>
            @elseif($cardRequest->status === 'collected')
                <h2 class="text-lg font-extrabold text-blue-700">Card Collected</h2>
                <p class="mt-2 text-sm font-medium leading-6 text-gray-600">You have already collected your ID card. Contact admin office for replacement requests.</p>
            @endif
        </div>

        <aside class="space-y-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-extrabold text-gray-950">Student Summary</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    @foreach([
                        'Name' => $student->full_name,
                        'Roll' => $student->roll_number,
                        'Class' => $student->stream,
                        'Section' => $student->section,
                    ] as $key => $value)
                        <div class="flex justify-between gap-3 border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                            <dt class="font-bold text-gray-400">{{ $key }}</dt>
                            <dd class="text-right font-semibold text-gray-800">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            @if($cardRequest)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-base font-extrabold text-gray-950">Request History</h2>
                    <p class="mt-3 text-sm font-semibold text-gray-600">Submitted: {{ $cardRequest->created_at->format('d M Y, h:i A') }}</p>
                    <p class="mt-1 text-sm font-semibold text-gray-600">Last updated: {{ $cardRequest->updated_at->format('d M Y') }}</p>
                    @if($cardRequest->admin_note)
                        <p class="mt-3 rounded-xl bg-gray-50 p-3 text-sm font-medium text-gray-600">{{ $cardRequest->admin_note }}</p>
                    @endif
                </div>
            @endif
        </aside>
    </section>
</div>
@endsection
