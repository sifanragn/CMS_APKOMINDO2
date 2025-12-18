@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        {{-- Header --}}
        <div class="bg-white border-b sticky top-0 z-10">
            <div class="container mx-auto px-4 py-3">
                <a href="{{ route('ourblogs.index') }}"
                    class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="font-medium">Kembali ke Daftar Berita</span>
                </a>
            </div>
        </div>

        <div class="container mx-auto px-4 py-6 max-w-5xl">
            <div class="grid lg:grid-cols-2 gap-8">
                {{-- Featured Image --}}
                <div class="max-w-sm mx-auto lg:max-w-none">
                    <div class="relative group">
                        <div class="overflow-hidden rounded-xl shadow-lg">
                            @if ($blog->image)
                                <div
                                    class="relative w-full h-64 md:h-80 lg:h-[350px] bg-gradient-to-br from-gray-100 to-gray-200">
                                    <img src="{{ asset('storage/' . $blog->image) }}"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        alt="{{ $blog->title }}">
                                </div>
                            @else
                                <div
                                    class="w-full h-64 md:h-80 lg:h-[350px] bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center rounded-xl">
                                    <div class="text-center text-gray-500">
                                        <div
                                            class="w-12 h-12 mx-auto mb-3 bg-gray-200 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <p class="text-sm">Tidak ada gambar</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Blog Details --}}
                <div class="space-y-6">
                    {{-- Title and Meta --}}
                    <div>
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2 flex-wrap">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($blog->pub_date)->format('d M Y') }}</span>
                            </div>
                            <span class="text-gray-300">•</span>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                                <span>{{ $blog->category->name ?? 'Tanpa Kategori' }}</span>
                            </div>
                            @if ($blog->waktu_baca)
                                <span class="text-gray-300">•</span>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <span>{{ $blog->waktu_baca }}</span>
                                </div>
                            @endif
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ $blog->title }}</h1>
                    </div>

                    {{-- Reading Time Badge (Highlighted) --}}
                    @if ($blog->waktu_baca)
                        <div class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-full text-sm font-medium shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <span>Waktu Baca: {{ $blog->waktu_baca }}</span>
                        </div>
                    @endif

                    {{-- Excerpt --}}
                    @if ($blog->excerpt)
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                            <p class="text-gray-700 font-medium">{{ $blog->excerpt }}</p>
                        </div>
                    @endif

                    {{-- Content --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Konten Berita</h3>
                        <div class="prose prose-gray max-w-none">
                            <div class="prose max-w-none">{!! $blog->description !!}</div>
                        </div>
                    </div>

                    {{-- Additional Info Card --}}
                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                        <h4 class="font-semibold text-gray-900 mb-3">Informasi Artikel</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($blog->pub_date)->format('d F Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                                <span>{{ $blog->category->name ?? 'Tanpa Kategori' }}</span>
                            </div>
                            @if ($blog->waktu_baca)
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <span>{{ $blog->waktu_baca }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
       {{-- GALERI FOTO TAMBAHAN --}}
@if ($blog->extraImages && $blog->extraImages->count())
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Galeri Foto
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($blog->extraImages as $img)
                <div class="rounded-xl border bg-white shadow-sm overflow-hidden">
                    
                    {{-- GAMBAR --}}
                    <img
                        src="{{ asset('storage/' . $img->image) }}"
                        alt="{{ $img->title ?? 'Foto tambahan' }}"
                        class="w-full h-40 object-cover"
                    >

                    {{-- TEKS DI LUAR GAMBAR --}}
                    @if ($img->title || $img->subtitle)
                        <div class="p-3">
                            @if ($img->title)
                                <p class="text-sm font-semibold text-gray-900 mb-1">
                                    {{ $img->title }}
                                </p>
                            @endif

                            @if ($img->subtitle)
                                <div class="text-xs text-gray-600 leading-snug prose prose-sm max-w-none">
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
@endsection
