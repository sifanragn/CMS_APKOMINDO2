@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">



    {{-- Header --}}
    <div class="bg-white border-b sticky top-0 z-10">
        <div class="container mx-auto px-4 py-3">
            <a href="{{ route('slider.index') }}"
               class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 19l-7-7 7-7"/>
                </svg>
                <span class="font-medium">Kembali ke Slider</span>
            </a>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 max-w-5xl">
        <div class="grid lg:grid-cols-2 gap-8">

            {{-- IMAGE --}}
            <div>
                <div class="overflow-hidden rounded-xl shadow-lg bg-gray-100">
                    @if($slider->image)
                        <img src="{{ asset('storage/'.$slider->image) }}"
                             class="w-full h-64 md:h-80 lg:h-[360px] object-cover">
                    @else
                        <div class="h-64 md:h-80 lg:h-[360px] flex items-center justify-center text-gray-400">
                            Tidak ada gambar
                        </div>
                    @endif
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="space-y-6">

                {{-- Title & Status --}}
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                        <span>Status:</span>
                        @if($slider->display_on_home)
                            <span class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs">
                                Aktif di Homepage
                            </span>
                        @else
                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-600 text-xs">
                                Tidak Ditampilkan
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl font-bold text-gray-900 leading-tight">
                        {{ $slider->title ?? 'Tanpa Judul' }}
                    </h1>
                </div>

                {{-- Subtitle --}}
                @if($slider->subtitle)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50
                            border border-blue-200 rounded-xl p-4">
                    <div class="ck-content text-gray-700">
                        {!! $slider->subtitle !!}
                    </div>
                </div>
                @endif

                {{-- Button Info --}}
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <h4 class="font-semibold text-gray-900 mb-3">Informasi Tombol</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Button Text</span>
                            <div class="mt-1 font-medium">
                                {{ $slider->button_text ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-gray-500">URL Link</span>
                            <div class="mt-1">
                                @if($slider->url_link)
                                    <a href="{{ $slider->url_link }}" target="_blank"
                                       class="text-blue-600 hover:underline break-all">
                                        {{ $slider->url_link }}
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- YouTube --}}
                @if($slider->youtube_id)
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <h4 class="font-semibold text-gray-900 mb-3">Video YouTube</h4>

                    <div class="aspect-video rounded overflow-hidden">
                        <iframe class="w-full h-full"
                            src="https://www.youtube.com/embed/{{ $slider->youtube_id }}"
                            frameborder="0"
                            allowfullscreen></iframe>
                    </div>
                </div>
                @endif

            </div>
        </div>

        {{-- EXTRA IMAGES --}}
        @if($slider->extraImages && $slider->extraImages->count())
        <div class="mt-10">
            <h3 class="text-lg font-semibold mb-4">Foto Tambahan</h3>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($slider->extraImages as $img)
                <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                    <img src="{{ asset('storage/'.$img->image) }}?v={{ $img->updated_at->timestamp }}"

                         class="h-40 w-full object-cover">

                    <div class="p-3 space-y-1">
                        @if($img->title)
                            <div class="font-semibold text-sm">
                                {{ $img->title }}
                            </div>
                        @endif

                        @if($img->subtitle)
                            <div class="ck-content text-xs text-gray-600">
                                {!! $img->subtitle !!}
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
