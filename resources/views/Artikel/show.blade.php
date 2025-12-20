@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- ================= HEADER ================= --}}
    <div class="bg-white border-b sticky top-0 z-10">
        <div class="container mx-auto px-4 py-3">
            <a href="{{ route('artikel.index') }}"
               class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7" />
                </svg>
                <span class="font-medium">Kembali ke Daftar Artikel</span>
            </a>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 max-w-5xl">
        <div class="grid lg:grid-cols-2 gap-8">

            {{-- ================= FEATURED IMAGE ================= --}}
            <div class="max-w-sm mx-auto lg:max-w-none">
                <div class="relative group">
                    <div class="overflow-hidden rounded-2xl shadow-lg border bg-white">
                        @if ($article->image)
                            <div class="relative w-full h-64 md:h-80 lg:h-[360px] bg-gray-100">
                                <img
                                    src="{{ asset('storage/'.$article->image) }}"
                                    alt="{{ $article->title }}"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                >
                            </div>
                        @else
                            <div class="w-full h-64 md:h-80 lg:h-[360px] bg-gray-100 flex items-center justify-center">
                                <div class="text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm">Tidak ada gambar</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ================= ARTICLE DETAIL ================= --}}
            <div class="space-y-6">

                {{-- TITLE + META --}}
                <div>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mb-2">
                        {{-- TANGGAL --}}
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $article->created_at?->translatedFormat('d M Y') ?? '-' }}</span>
                        </div>

                        <span class="text-gray-300">•</span>

                        {{-- KATEGORI --}}
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span>{{ $article->category->name ?? 'Tanpa Kategori' }}</span>
                        </div>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">
                        {{ $article->title }}
                    </h1>
                </div>

                {{-- CONTENT --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">
                        Konten Artikel
                    </h3>

                    <div class="prose prose-gray max-w-none prose-img:rounded-xl prose-img:shadow">
                        {!! $article->content !!}
                    </div>
                </div>

                {{-- INFO CARD --}}
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <h4 class="font-semibold text-gray-900 mb-3">
                        Informasi Artikel
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $article->created_at?->translatedFormat('d F Y') ?? '-' }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span>{{ $article->category->name ?? 'Tanpa Kategori' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
