@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- HEADER --}}
    <div class="bg-white border-b sticky top-0 z-10">
        <div class="container mx-auto px-6 py-4">
            <a href="{{ route('agenda.index') }}"
               class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="font-medium">Kembali ke daftar Agenda</span>
            </a>
        </div>
    </div>

    <div class="container mx-auto px-6 py-10 max-w-7xl">

        {{-- HERO + INFO --}}
        <div class="grid lg:grid-cols-2 gap-14 items-start">

            {{-- HERO IMAGE --}}
            <div class="relative">
                <div class="overflow-hidden rounded-2xl shadow-lg bg-white">
                    @if($agenda->image)
                        <img src="{{ asset('storage/'.$agenda->image) }}"
                             class="w-full h-[420px] object-cover"
                             alt="{{ $agenda->title }}">
                    @else
                        <div class="h-[420px] flex items-center justify-center text-gray-400">
                            Tidak ada gambar
                        </div>
                    @endif

                    {{-- BADGES --}}
                    <div class="absolute top-5 left-5 flex gap-2">
                        @if($agenda->type)
                            <span class="px-3 py-1 text-xs rounded-full bg-blue-600 text-white shadow">
                                {{ ucfirst($agenda->type) }}
                            </span>
                        @endif
                        <span class="px-3 py-1 text-xs rounded-full
                            {{ $agenda->status === 'Open'
                                ? 'bg-green-600 text-white'
                                : 'bg-red-600 text-white' }}">
                            {{ $agenda->status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- INFO CARD --}}
            <div class="space-y-6">

                <h1 class="text-3xl font-bold text-gray-900 leading-tight">
                    {{ $agenda->title }}
                </h1>

                <div class="bg-white rounded-2xl shadow p-6 space-y-5">

                    {{-- Jadwal --}}
                    <div class="flex gap-4">
                        <svg class="w-5 h-5 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10"/>
                        </svg>
                        <div>
                            <p class="font-semibold">Jadwal Acara</p>
                            <p class="text-sm text-gray-600">
                                {{ $agenda->start_datetime }} <br>
                                s/d {{ $agenda->end_datetime }}
                            </p>
                        </div>
                    </div>

                    {{-- Lokasi --}}
                    <div class="flex gap-4">
                        <svg class="w-5 h-5 text-gray-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9"/>
                        </svg>
                        <div>
                            <p class="font-semibold">Tempat</p>
                            <p class="text-sm text-gray-600">{{ $agenda->location }}</p>
                        </div>
                    </div>

                    {{-- Organizer --}}
                    <div class="flex gap-4">
                        <svg class="w-5 h-5 text-gray-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0"/>
                        </svg>
                        <div>
                            <p class="font-semibold">Penyelenggara</p>
                            <p class="text-sm text-gray-600">{{ $agenda->event_organizer }}</p>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="flex gap-3 pt-4">
                        @if($agenda->register_link)
                            <a href="{{ $agenda->register_link }}" target="_blank"
                               class="flex-1 text-center py-3 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700">
                                Daftar Sekarang
                            </a>
                        @endif
                        @if($agenda->youtube_link)
                            <a href="{{ $agenda->youtube_link }}" target="_blank"
                               class="flex-1 text-center py-3 rounded-xl bg-red-600 text-white font-medium hover:bg-red-700">
                                Tonton di YouTube
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- DESKRIPSI --}}
        <div class="mt-16">
            <div class="bg-white rounded-2xl shadow p-8 max-w-5xl">
                <h2 class="text-xl font-bold mb-4">Deskripsi Acara</h2>
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    {!! $agenda->description !!}
                </div>
            </div>
        </div>

        {{-- PEMBICARA --}}
        @if($agenda->speakers->count())
            <div class="mt-16">
                <h2 class="text-xl font-bold mb-6">Pembicara</h2>

                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($agenda->speakers as $speaker)
                        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                            @if($speaker->photo)
                                <img src="{{ asset('storage/'.$speaker->photo) }}"
                                     class="w-12 h-12 rounded-full object-cover border"
                                     alt="{{ $speaker->name }}">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-200"></div>
                            @endif
                            <div>
                                <p class="font-semibold text-sm">{{ $speaker->name }}</p>
                                <p class="text-xs text-gray-500">{{ $speaker->title }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- GALERI --}}
        @if($agenda->extraImages && $agenda->extraImages->count())
            <div class="mt-20">
                <h2 class="text-xl font-bold mb-6">Galeri Kegiatan</h2>

                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($agenda->extraImages as $img)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

                        {{-- IMAGE --}}
                        <div class="bg-gray-50 flex items-center justify-center p-4 min-h-[220px]">
                            <img src="{{ asset('storage/'.$img->image) }}"
                                class="max-h-[200px] w-auto object-contain"
                                alt="{{ $img->title }}">
                        </div>

                        {{-- TEXT --}}
                        @if($img->title || $img->subtitle)
                            <div class="p-4">
                                @if($img->title)
                                    <p class="text-sm font-semibold mb-1">
                                        {{ $img->title }}
                                    </p>
                                @endif

                                @if($img->subtitle)
                                    <div class="text-xs text-gray-600 prose prose-sm max-w-none">
                                        {!! $img->subtitle !!}
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            </div>
        @endif

    </div>
</div>
@endsection
