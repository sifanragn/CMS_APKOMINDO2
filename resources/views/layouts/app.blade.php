<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'APKOMINDO')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/37.0.1/classic/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    <!-- Custom CSS untuk CKEditor styling -->
    <style>
/* ============================= */
/* CKEDITOR – APKOMINDO THEME   */
/* ============================= */

/* Editor base */
.ck-editor__editable {
    min-height: 200px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #ffffff;
}

.ck.ck-editor {
    width: 100%;
}

/* ============================= */
/* LIST STYLE                   */
/* ============================= */
.ck-content ol {
    list-style-type: decimal;
    margin-left: 1.5em;
}

.ck-content ul {
    list-style-type: disc;
    margin-left: 1.5em;
}

.ck-content ol li,
.ck-content ul li {
    margin-bottom: 0.5em;
    padding-left: 0.4em;
}

/* Nested list */
.ck-content ol ol {
    list-style-type: lower-alpha;
}

.ck-content ol ol ol {
    list-style-type: lower-roman;
}

.ck-content ul ul {
    list-style-type: circle;
}

.ck-content ul ul ul {
    list-style-type: square;
}

/* ============================= */
/* TABLE STYLE                  */
/* ============================= */
.ck-content table {
    border-collapse: collapse;
    width: 100%;
    margin: 1em 0;
}

.ck-content table th,
.ck-content table td {
    border: 1px solid #cbd5e1;
    padding: 10px;
}

.ck-content table th {
    background-color: #e3f2fd;
    color: #0d47a1;
    font-weight: 600;
}

/* ============================= */
/* BLOCKQUOTE                   */
/* ============================= */
.ck-content blockquote {
    border-left: 4px solid #0d47a1;
    background-color: #f0f7ff;
    margin: 1em 0;
    padding: 0.75em 1em;
    font-style: italic;
    color: #334155;
}

/* ============================= */
/* MODAL + EDITOR FRAME         */
/* ============================= */
#addModal .ck.ck-editor,
#editModal .ck.ck-editor {
    border: 1px solid #cbd5e1;
    border-radius: 0.5rem;
}

/* ============================= */
/* LOADING STATE                */
/* ============================= */
.editor-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    border: 1px dashed #0d47a1;
    border-radius: 0.5rem;
    background-color: #f0f7ff;
}

.editor-loading::after {
    content: "Memuat editor...";
    color: #0d47a1;
    font-size: 14px;
    font-weight: 500;
}

/* ============================= */
/* RESPONSIVE                   */
/* ============================= */
@media (max-width: 768px) {
    .ck-editor__editable {
        min-height: 150px;
        font-size: 14px;
    }

    .ck.ck-toolbar {
        flex-wrap: wrap;
    }
}

/* ============================= */
/* SCROLLBAR – MODAL            */
/* ============================= */
#addModal .overflow-y-auto::-webkit-scrollbar,
#editModal .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

#addModal .overflow-y-auto::-webkit-scrollbar-track,
#editModal .overflow-y-auto::-webkit-scrollbar-track {
    background: #e3f2fd;
}

#addModal .overflow-y-auto::-webkit-scrollbar-thumb,
#editModal .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #0d47a1;
    border-radius: 4px;
}

#addModal .overflow-y-auto::-webkit-scrollbar-thumb:hover,
#editModal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #08306b;
}

/* ============================= */
/* GLOBAL SCROLLBAR             */
/* ============================= */
/* Scrollbar – lebih halus & profesional */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9; /* abu-biru lembut */
}

::-webkit-scrollbar-thumb {
  background: #c7d2fe; /* soft blue */
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; /* netral saat hover */
}


/* ============================= */
/* SMOOTH TRANSITION            */
/* ============================= */
* {
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
}

</style>

    <script>
        function toggleDropdown(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }

        // Function to highlight active menu
        function highlightActiveMenu() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('aside nav a');

            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href === currentPath) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }


        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('[id$="-dropdown"]');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target) && !event.target.closest('button[onclick*="' + dropdown
                        .id + '"]')) {
                    dropdown.classList.add('hidden');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', highlightActiveMenu);
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar - Fixed -->
        <aside class="w-64 bg-white shadow-lg border-r border-gray-200 flex-shrink-0 fixed h-full overflow-y-auto">
            <!-- Logo Section -->
            <div class="py-6 px-6 border-b border-gray-200">
                <div class="flex flex-col items-center space-y-3">
                    <img src="{{ asset('storage/logo.png') }}" class="h-12 w-12 object-contain" alt="Logo">
                    <div class="text-center">
                        <h2 class="text-xs font-bold" style="color:#0d47a1">APKOMINDO</h2>
                        <p class="text-xs text-gray-400">Asosiasi Pengusaha Komputer Indonesia</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="py-4 px-4 space-y-6">
                <!-- DASHBOARD -->
                <div>
                    <h3 class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dashboard</h3>
                    <a href="/dashboard"
                        class="group flex items-center px-4 py-3 rounded-lg text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Beranda</span>
                    </a>
                </div>

                <!-- PROFIL & AKUN -->
                <div>
                    <h3 class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Profil & Akun
                    </h3>
                    <a href="/profile-setting"
                        class="group flex items-center px-4 py-3 rounded-lg text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Profile Saya</span>
                    </a>
                </div>

                <!-- KONTEN & INFORMASI -->
                <div>
                    <h3 class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Konten &
                        Informasi</h3>
                    <div class="space-y-1">
                        <!-- Konten Utama -->
                        <button onclick="toggleDropdown('konten-dropdown')"
                            class="group w-full flex items-center justify-between px-4 py-3 rounded-lg text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                                <span>Konten Utama</span>
                            </div>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="konten-dropdown" class="ml-8 space-y-1 hidden">
                            <a href="{{ route('agenda.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Event</a>
                            <a href="{{ route('ourblogs.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-9000 transition-all duration-200">Berita</a>
                            <a href="{{ route('tentangkami.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Tentang
                                Kami</a>
                            <a href="{{ route('slider.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Slider</a>
                            <a href="{{ route('artikel.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Artikel</a>
                        </div>

                        <!-- Kategori Management -->
                        <button onclick="toggleDropdown('kategori-dropdown')"
                            class="group w-full flex items-center justify-between px-4 py-3 rounded-lg text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span>Kategori</span>
                            </div>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="kategori-dropdown" class="ml-8 space-y-1 hidden">
                            <a href="{{ route('category.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Kategori
                                Berita</a>
                            <a href="{{ route('category-artikel.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Kategori
                                Artikel</a>
                            <a href="{{ route('category-anggota.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Kategori
                                Anggota</a>
                            <a href="{{ route('category-kegiatan.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Kategori
                                Kegiatan</a>
                            <a href="{{ route('category-tentangkami.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Kategori
                                Tentangkami</a>
                            <a href="{{ route('category-store.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Kategori
                                Store</a>
                        </div>
                    </div>
                </div>

                <!-- AKTIVITAS & LAYANAN -->
                <div>
                    <h3 class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Aktivitas &
                        Layanan</h3>
                    <div class="space-y-1">
                        <!-- Kegiatan & Event -->
                        <button onclick="toggleDropdown('kegiatan-dropdown')"
                            class="group w-full flex items-center justify-between px-4 py-3 rounded-lg text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Kegiatan & Event</span>
                            </div>
                            <svg class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="kegiatan-dropdown" class="ml-8 space-y-1 hidden">
                            <a href="{{ route('kegiatan.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Kegiatan</a>
                            <a href="{{ route('agenda-speakers.index') }}"
                                class="block px-4 py-2 rounded-lg text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">Pembicara</a>
                        </div>
                        <a href="{{ route('hows.index') }}"
                            class="group flex items-center px-4 py-3 rounded-lg text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-all duration-200">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            <span>KTA (Kartu Tanda Anggota)</span>
                        </a>
                    </div>
                </div>

            
            </nav>
        </aside>

        <!-- Main Content Area - dengan margin left untuk sidebar -->
        <div class="flex-1 flex flex-col min-w-0 ml-64">
            <!-- Header - Fixed/Sticky -->
            <header class="app-header sticky top-0 z-40">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- User Menu -->
                    <div class="flex items-center ml-auto">
                        <!-- User Dropdown -->
                        <div class="relative">
                            <button onclick="toggleDropdown('user-dropdown')"
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                               <div class="w-6 h-6 rounded-full flex items-center justify-center bg-blue-900">
                                    @if (auth()->user()->profile_picture)
                                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                                            class="w-full h-full rounded-full object-cover" alt="Profile Picture">
                                    @else
                                        <span class="text-white text-xs font-medium">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-xs font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400">{{ auth()->user()->role }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="user-dropdown"
                                class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center bg-blue-900">
                                            @if (auth()->user()->profile_picture)
                                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                                                    class="w-full h-full rounded-full object-cover"
                                                    alt="Profile Picture">
                                            @else
                                                <span class="text-white text-xs font-medium">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-gray-400">{{ auth()->user()->role }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-2">
                                    <a href="/profile-setting"
                                        class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Profile Saya
                                    </a>
                                </div>

                                <div class="border-t border-gray-200 pt-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6 app-content overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('modals')

    @stack('scripts')
    <script>
/* ============================= */
/* APKOMINDO BRAND SYSTEM       */
/* ============================= */

:root {
  --apko-blue: #0d47a1;
  --apko-blue-soft: #e3f2fd;
  --apko-blue-dark: #08306b;
  --apko-red: #d32f2f;
}

/* Sidebar base */
.sidebar {
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
  position: relative;
}

/* Accent bar kanan */
.sidebar::after {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(
    to bottom,
    var(--apko-blue),
    #1976d2
  );
}
</script>

<style>
/* ============================= */
/* SOFT BLUE SIDEBAR THEME       */
/* ============================= */

:root {
  --blue-main: #2563eb;        /* blue-600 */
  --blue-soft: #eef4ff;        /* ⬅ lebih biru dikit */
  --blue-hover: #dbeafe;       /* blue-100 */
  --blue-border: #c7ddff;      /* ⬅ border lebih kebaca */
  --blue-text: #1e3a8a;        /* blue-900 */
  --blue-muted: #475569;       /* slate-600 */
  --bg-content: #f3f7ff;       /* ⬅ content super soft */
}


/* ============================= */
/* HEADER – MATCH SIDEBAR STYLE */
/* ============================= */

.app-header {
  background: linear-gradient(
    to bottom,
    #f9fbff,
    #ffffff
  );
  border-bottom: 1px solid var(--blue-border);
  box-shadow: 0 1px 2px rgba(30, 58, 138, 0.04); /* soft shadow */
}

/* User hover di header */
.app-header button:hover {
  background-color: var(--blue-hover);
}


/* Sidebar base */
aside {
  background: linear-gradient(
    to bottom,
    #f8fbff,
    #ffffff
  );
  border-right: 1px solid var(--blue-border);
}

/* Logo area */
aside .border-b {
  background: linear-gradient(
    to bottom,
    #f1f7ff,
    #ffffff
  );
}

/* Section title */
aside h3 {
  color: #475569;
  font-weight: 600;
  letter-spacing: 0.04em;
}

/* Menu item base */
aside nav a,
aside nav button {
  color: var(--blue-muted);
  border-radius: 0.75rem;
  position: relative;
}

/* Hover */
aside nav a:hover,
aside nav button:hover {
  background-color: var(--blue-hover);
  color: var(--blue-text);
}

/* Icon */
aside svg {
  color: #3b82f6; /* blue-500 soft */
}

/* Active menu */
aside nav a.active {
  background-color: var(--blue-soft);
  color: var(--blue-text);
  font-weight: 600;
}

/* Active indicator bar */
aside nav a.active::before {
  content: "";
  position: absolute;
  left: 0;
  top: 12%;
  height: 76%;
  width: 4px;
  background: linear-gradient(
    to bottom,
    #60a5fa,
    #2563eb
  );
  border-radius: 0 6px 6px 0;
}

/* Dropdown items */
aside nav div[id$="dropdown"] a {
  font-size: 0.7rem;
  padding-left: 1.25rem;
}

/* Dropdown hover */
aside nav div[id$="dropdown"] a:hover {
  background-color: #f1f7ff;
  color: var(--blue-text);
}

/* Smooth feel */
aside nav a,
aside nav button {
  transition: all 0.18s ease;
}


/* ================================================= */
/* GLOBAL SMOOTH TRANSITION (AMAN & RINGAN)          */
/* ================================================= */
a,
button,
input,
select,
textarea {
  transition:
    background-color .2s ease,
    color .2s ease,
    border-color .2s ease,
    box-shadow .2s ease,
    transform .15s ease;
}

/* ================================================= */
/* PAGE TRANSITION                                  */
/* ================================================= */
.app-content {
  animation: pageFade .35s ease;
}

@keyframes pageFade {
  from {
    opacity: 0;
    transform: translateY(6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ================================================= */
/* DROPDOWN ANIMATION (SIDEBAR)                      */
/* ================================================= */
.dropdown-enter {
  max-height: 0;
  opacity: 0;
  transform: translateY(-4px);
  overflow: hidden;
}

.dropdown-enter-active {
  max-height: 500px;
  opacity: 1;
  transform: translateY(0);
  transition: all .25s ease;
}

/* ================================================= */
/* BUTTON MICRO INTERACTION                          */
/* ================================================= */
button:active,
a:active {
  transform: scale(.97);
}

/* ================================================= */
/* CARD / BOX HOVER EFFECT                           */
/* ================================================= */
.card-hover {
  transition: transform .25s ease, box-shadow .25s ease;
}

.card-hover:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(15,23,42,.08);
}

/* ================================================= */
/* SCROLL SMOOTH                                    */
/* ================================================= */
html {
  scroll-behavior: smooth;
}

/* ================================================= */
/* RESPONSIVE SIDEBAR (MOBILE)                       */
/* ================================================= */
@media (max-width: 1024px) {
  aside {
    position: fixed;
    transform: translateX(-100%);
    transition: transform .3s ease;
    z-index: 60;
  }

  aside.open {
    transform: translateX(0);
  }

  .ml-64 {
    margin-left: 0;
  }
}

/* ===================================== */
/* SOFT DATA TABLE (CMS STYLE)           */
/* ===================================== */

/* wrapper */
.soft-table {
  border-collapse: separate;
  border-spacing: 0 10px; /* jarak antar row */
}

/* header */
.soft-table thead th {
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b; /* slate-500 */
  text-transform: uppercase;
  letter-spacing: .04em;
  padding-bottom: 12px;
}

/* row card */
.soft-table tbody tr {
  background: #ffffff;
  border-radius: 14px;
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
  transition: all .25s ease;
}

/* hover row */
.soft-table tbody tr:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
}

/* cell */
.soft-table td {
  padding: 14px 16px;
  border-top: 1px solid transparent;
  border-bottom: 1px solid transparent;
}

/* first & last cell radius */
.soft-table tbody tr td:first-child {
  border-radius: 14px 0 0 14px;
}
.soft-table tbody tr td:last-child {
  border-radius: 0 14px 14px 0;
}

/* divider halus antar kolom (opsional) */
.soft-table td + td {
  border-left: 1px dashed #e5e7eb;
}

/* image */
.table-avatar {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  object-fit: cover;
  box-shadow: 0 4px 10px rgba(0,0,0,.08);
}

/* status badge */
.badge-status {
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 0.7rem;
  font-weight: 600;
}

.badge-open {
  background: #dcfce7;
  color: #15803d;
}

/* action button */
.btn-action {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  font-size: 0.75rem;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background: #fff;
  transition: all .2s ease;
}

.btn-action:hover {
  background: #f8fafc;
  transform: translateY(-1px);
}

.btn-edit {
  border-color: #bfdbfe;
  color: #2563eb;
}

.btn-detail {
  border-color: #e5e7eb;
  color: #475569;
}


</style>

</body>
</html>
