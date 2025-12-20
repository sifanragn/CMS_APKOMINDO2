@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
{{-- GREETING WRAPPER --}}
<div id="greetingWrapper"
     class="max-w-6xl mx-auto
            overflow-hidden
            transition-[max-height] duration-700 ease-[cubic-bezier(.22,.61,.36,1)]
            max-h-0">

    {{-- GREETING BAR --}}
    <div id="greetingBar"
         class="mb-4
                opacity-0 -translate-y-4
                transition-all duration-500 ease-out">

        <div class="backdrop-blur-md bg-white/70
                    border border-blue-100
                    rounded-xl
                    px-5 py-4
                    flex items-center justify-between
                    shadow-sm">

            <div class="text-center w-full">
                <p class="text-sm text-gray-600">
                    <span id="greetingText"></span>,
                    <span class="font-semibold text-blue-700">Apkomindo</span> 👋
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    Semoga aktivitas hari ini berjalan lancar
                </p>
            </div>

        </div>
    </div>
</div>


    <div class="relative w-full max-w-6xl mx-auto">
        {{-- Slider Wrapper --}}
        <div id="slider" class="relative overflow-hidden rounded-xl shadow-lg">
            @if ($sliders->count() > 0)
                {{-- Slide Items dari Database --}}
                @foreach ($sliders as $index => $slider)
                    <div
                        class="slide {{ $index === 0 ? 'opacity-100' : 'opacity-0 absolute' }} inset-0 transition-opacity duration-700 ease-in-out">
                        <div class="relative h-64 md:h-80 lg:h-96">
                            {{-- Background Image --}}
                            @if ($slider->image)
                                <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}"
                                    class="w-full h-full object-cover">
                                {{-- Overlay untuk keterbacaan text --}}
                                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                            @else
                                {{-- Fallback jika tidak ada gambar --}}
                                <div class="w-full h-full bg-gradient-to-r from-purple-500 to-blue-500"></div>
                            @endif

                            {{-- Content Overlay --}}
                            <div
                                class="absolute inset-0 flex flex-col items-center justify-center text-center text-white px-4">
                                @if ($slider->title)
                                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-2 drop-shadow-lg">
                                        {{ $slider->title }}
                                    </h2>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Fallback jika tidak ada data slider --}}
                <div
                    class="slide bg-purple-400 text-white h-64 flex flex-col items-center justify-center text-center transition-opacity duration-700 ease-in-out opacity-100">
                    <h2 class="text-3xl font-bold mb-2">Selamat Datang</h2>
                    <p class="text-xl font-semibold">Belum ada slider yang tersedia</p>
                </div>
            @endif
        </div>

        {{-- Navigasi Manual --}}
        @if ($sliders->count() > 1)
            <div class="mt-4 flex justify-center space-x-2">
                @foreach ($sliders as $index => $slider)
                    <button onclick="showSlide({{ $index }})"
                        class="w-3 h-3 rounded-full bg-gray-400 hover:bg-gray-600 transition-colors duration-200 slide-indicator {{ $index === 0 ? 'bg-gray-600' : '' }}">
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Konten Lain di bawah Slider --}}
    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    {{-- Agenda Terdekat --}}
    <a href="{{ route('agenda.index') }}" 
       class="block bg-white p-6 rounded shadow hover:shadow-lg hover:-translate-y-1 transition">
        <h3 class="text-lg font-semibold mb-2">Agenda Terdekat</h3>

        @if ($agendas->count())
            <ul class="text-gray-600 space-y-2">
                @foreach ($agendas as $agenda)
                    <li>
                        <strong>{{ \Carbon\Carbon::parse($agenda->start_datetime)->translatedFormat('d F Y') }}</strong> –
                        {{ $agenda->title }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Belum ada agenda terdekat.</p>
        @endif
    </a>

    {{-- KTA Online --}}
    <a href="{{ route('hows.index') }}" 
       class="block bg-white p-6 rounded shadow hover:shadow-lg hover:-translate-y-1 transition">
        <h3 class="text-lg font-semibold mb-2">KTA Online</h3>
        <p class="text-gray-600">Kini anggota dapat mengakses KTA secara digital.</p>
    </a>

    {{-- Loker Terkini --}}
    <a href="{{ route('career.index') }}" 
       class="block bg-white p-6 rounded shadow hover:shadow-lg hover:-translate-y-1 transition">
        <h3 class="text-lg font-semibold mb-2">Loker Terkini</h3>

        @if ($totalLoker > 0)
            <p class="text-gray-600">Terdapat <strong>{{ $totalLoker }}</strong> lowongan yang tersedia.</p>
        @else
            <p class="text-gray-500">Belum ada lowongan tersedia saat ini.</p>
        @endif
    </a>

</div>

@endsection

@push('scripts')
    <script>
document.addEventListener("DOMContentLoaded", function () {

    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.slide-indicator');
    const totalSlides = slides.length;

    function showSlide(index) {

        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.remove("opacity-0", "absolute");
                slide.classList.add("opacity-100");
            } else {
                slide.classList.remove("opacity-100");
                slide.classList.add("opacity-0", "absolute");
            }
        });

        indicators.forEach((indicator, i) => {
            indicator.classList.toggle("bg-gray-600", i === index);
            indicator.classList.toggle("bg-gray-400", i !== index);
        });

        currentSlide = index;
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }

    // Klik bullet indicator
    indicators.forEach((btn, index) => {
        btn.addEventListener("click", () => {
            showSlide(index);
        });
    });

    // Auto slide setiap 5 detik
    if (totalSlides > 1) {
        setInterval(nextSlide, 5000);
    }

    showSlide(0);

});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const greetingText    = document.getElementById("greetingText");
    const greetingBar     = document.getElementById("greetingBar");
    const greetingWrapper = document.getElementById("greetingWrapper");

    const hour = new Date().getHours();
    let greeting = "Selamat Datang";

    if (hour >= 4 && hour < 11) {
        greeting = "Selamat Pagi";
    } else if (hour >= 11 && hour < 15) {
        greeting = "Selamat Siang";
    } else if (hour >= 15 && hour < 18) {
        greeting = "Selamat Sore";
    } else {
        greeting = "Selamat Malam";
    }

    greetingText.textContent = greeting;

    /* === MUNCUL === */
    setTimeout(() => {
        greetingWrapper.classList.remove("max-h-0");
        greetingWrapper.classList.add("max-h-40");

        greetingBar.classList.remove("opacity-0", "-translate-y-4");
        greetingBar.classList.add("opacity-100", "translate-y-0");
    }, 200);

    /* === HILANG SETELAH 5 DETIK === */
    setTimeout(() => {

        // fade + slide
        greetingBar.classList.remove("opacity-100", "translate-y-0");
        greetingBar.classList.add("opacity-0", "-translate-y-4");

        // collapse space
        setTimeout(() => {
            greetingWrapper.classList.remove("max-h-40");
            greetingWrapper.classList.add("max-h-0");
        }, 300);

    }, 5200);

});
</script>

@endpush
