@extends('layouts.app')

@section('content')
    <!-- Include SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Profile Settings</h2>

        <div class="grid md:grid-cols-3 gap-6">
            {{-- Left Column --}}
            <div class="space-y-6">
                {{-- Logo --}}
                <div class="bg-white shadow-lg rounded-lg p-4 text-center">
                    <img src="{{ asset('storage/logo.png') }}" class="w-24 mx-auto mb-2">
                    <h3 class="font-semibold text-xl">APKOMINDO</h3>
                </div>

                {{-- Contact --}}
                <div class="bg-white shadow-lg rounded-lg p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-lg">Contact</h4>
                        <button onclick="openContactModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition-colors duration-200">
                            <i class="fas fa-plus mr-1"></i>Add
                        </button>
                    </div>

                    {{-- Phone --}}
                    <div class="mb-4 relative group bg-gray-50 hover:bg-gray-100 p-3 rounded-lg transition-all duration-200">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-phone mr-2 text-blue-600"></i>
                                    <span class="font-medium text-gray-700">Phone Number</span>
                                </div>
                                <div id="notlp-display" class="text-sm text-blue-600 ml-6">
                                    <a href="tel:{{ $contact?->notlp }}"
                                        class="hover:underline">{{ $contact?->notlp ?? '-' }}</a>
                                </div>
                            </div>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button onclick="editField('notlp')"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                @if ($contact?->notlp)
                                    <button type="button" onclick="confirmContactDelete('notlp', 'phone number')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                        <form id="notlp-form" class="hidden mt-3" method="POST" action="{{ route('contact.update') }}">
                            @csrf
                            <input type="hidden" name="type" value="notlp">
                            <input type="text" name="notlp"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $contact?->notlp }}" placeholder="Enter phone number">
                            <div class="flex gap-2 mt-3">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-save mr-1"></i>Save
                                </button>
                                <button type="button" onclick="cancelEdit('notlp')"
                                    class="bg-gray-500 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Email --}}
                    <div class="relative group bg-gray-50 hover:bg-gray-100 p-3 rounded-lg transition-all duration-200">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-envelope mr-2 text-blue-600"></i>
                                    <span class="font-medium text-gray-700">Email</span>
                                </div>
                                <div id="email-display" class="text-sm text-blue-600 ml-6">
                                    <a href="mailto:{{ $contact?->email }}"
                                        class="hover:underline">{{ $contact?->email ?? '-' }}</a>
                                </div>
                            </div>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button onclick="editField('email')"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>
                                @if ($contact?->email)
                                    <button type="button" onclick="confirmContactDelete('email', 'email')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                        <form id="email-form" class="hidden mt-3" method="POST" action="{{ route('contact.update') }}">
                            @csrf
                            <input type="hidden" name="type" value="email">
                            <input type="email" name="email"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ $contact?->email }}" placeholder="Enter email">
                            <div class="flex gap-2 mt-3">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-save mr-1"></i>Save
                                </button>
                                <button type="button" onclick="cancelEdit('email')"
                                    class="bg-gray-500 hover:bg-gray-600 text-white text-sm px-4 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Hidden Delete Forms for Contact --}}
                    <form id="delete-notlp-form" method="POST" action="{{ route('contact.update') }}" style="display: none;">
                        @csrf
                        <input type="hidden" name="type" value="notlp">
                        <input type="hidden" name="notlp" value="">
                    </form>

                    <form id="delete-email-form" method="POST" action="{{ route('contact.update') }}" style="display: none;">
                        @csrf
                        <input type="hidden" name="type" value="email">
                        <input type="hidden" name="email" value="">
                    </form>
                </div>

                {{-- Social Account --}}
                <div class="bg-white shadow-lg rounded-lg p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-lg">Social Accounts</h4>
                        <button onclick="openSocialModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition-colors duration-200">
                            <i class="fas fa-plus mr-1"></i>Add
                        </button>
                    </div>

                    @foreach ($socialAccounts as $sosmed)
                        @php
                            $iconClass = match (strtolower($sosmed->nama)) {
                                'instagram' => 'fab fa-instagram',
                                'youtube' => 'fab fa-youtube',
                                'facebook' => 'fab fa-facebook',
                                'linkedin' => 'fab fa-linkedin',
                                'twitter' => 'fab fa-twitter',
                                'tiktok' => 'fab fa-tiktok',
                                'whatsapp' => 'fab fa-whatsapp',
                                default => 'fas fa-globe',
                            };
                        @endphp

                        <div class="mb-3 relative group bg-gray-50 hover:bg-gray-100 p-3 rounded-lg transition-all duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <i class="{{ $iconClass }} mr-2 text-blue-600"></i>
                                        <strong class="capitalize text-gray-700">{{ $sosmed->nama }}</strong>
                                    </div>

                                    <div id="sosmed-display-{{ $sosmed->id }}" class="ml-6">
                                        <a href="{{ $sosmed->link }}"
                                            class="text-sm text-blue-600 break-all block hover:text-blue-800 hover:underline transition-colors duration-200"
                                            target="_blank">
                                            {{ $sosmed->link }}
                                        </a>
                                    </div>

                                    <form id="sosmed-form-{{ $sosmed->id }}" method="POST"
                                        action="{{ route('social.update', $sosmed->id) }}" class="hidden mt-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="link" value="{{ $sosmed->link }}"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <div class="flex gap-2 mt-3">
                                            <button type="submit"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                                                <i class="fas fa-save mr-1"></i>Save
                                            </button>
                                            <button type="button" onclick="cancelSosmedEdit({{ $sosmed->id }})"
                                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                                                <i class="fas fa-times mr-1"></i>Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <button onclick="toggleSosmedEdit({{ $sosmed->id }})"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <button type="button" onclick="confirmSosmedDelete({{ $sosmed->id }})"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </div>

                            {{-- Hidden Delete Form for Social Media --}}
                            <form id="delete-sosmed-form-{{ $sosmed->id }}" method="POST"
                                  action="{{ route('social.destroy', $sosmed->id) }}" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right Column --}}
            <div class="md:col-span-2 space-y-4">
                <div class="flex justify-between items-center">
                    <h4 class="font-semibold text-lg">Address</h4>
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow transition-colors duration-200"
                        onclick="document.getElementById('addAddressForm').classList.remove('hidden')">
                        <i class="fas fa-plus mr-2"></i>Add Address
                    </button>
                </div>

                {{-- Form Add --}}
                <div id="addAddressForm" class="bg-white shadow-lg rounded-lg p-6 hidden">
                    <h5 class="font-semibold mb-4 text-gray-800">Add New Address</h5>
                    <form action="{{ route('company-profile.store') }}" method="POST"
                        class="grid md:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Place Name</label>
                            <input type="text" name="nama_tempat"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Place Name" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" name="lokasi"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Location" required>
                        </div>
                        <div class="md:col-span-2 flex gap-3 mt-2">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>Save
                            </button>
                            <button type="button"
                                onclick="document.getElementById('addAddressForm').classList.add('hidden')"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- List Address --}}
                @foreach ($companyProfiles as $profile)
                    <div class="bg-white shadow-lg rounded-lg p-6 relative group hover:shadow-xl transition-all duration-300">
                        <div id="address-display-{{ $profile->id }}">
                            <div class="font-semibold text-blue-700 text-lg">{{ $profile->nama_tempat }}</div>
                            <div class="text-gray-600 mt-2 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                                {{ $profile->lokasi }}
                            </div>
                        </div>

                        <form id="address-form-{{ $profile->id }}" class="hidden mt-4" method="POST"
                            action="{{ route('company-profile.update', $profile->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Place Name</label>
                                    <input type="text" name="nama_tempat"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $profile->nama_tempat }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <input type="text" name="lokasi"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        value="{{ $profile->lokasi }}">
                                </div>
                                <div class="flex gap-3 md:col-span-2 mt-2">
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-save mr-2"></i>Save
                                    </button>
                                    <button type="button" onclick="cancelAddressEdit({{ $profile->id }})"
                                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-times mr-2"></i>Cancel
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <button onclick="editAddress({{ $profile->id }})"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button type="button" onclick="confirmDelete({{ $profile->id }})"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </div>

                        {{-- Hidden Delete Form for Address --}}
                        <form id="delete-form-{{ $profile->id }}" method="POST"
                              action="{{ route('company-profile.destroy', $profile->id) }}" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal untuk Add Contact --}}
    <div id="contactModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 opacity-0"
            id="contactModalContent">
            <div class="bg-white text-gray-800 px-6 py-4 rounded-t-lg border-b border-gray-200">
                <h3 class="text-lg font-semibold">Add Contact Information</h3>
            </div>
            <form action="{{ route('contact.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-phone mr-2"></i>Nomor Telepon
                        </label>
                        <input type="text" name="notlp"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500"
                            placeholder="Masukkan nomor telepon">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input type="email" name="email"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500"
                            placeholder="Masukkan email">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                    <button type="button" onclick="closeContactModal()"
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal untuk Add Social Account --}}
    <div id="socialModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 opacity-0"
            id="socialModalContent">
            <div class="bg-white text-gray-800 px-6 py-4 rounded-t-lg border-b border-gray-200">
                <h3 class="text-lg font-semibold">Add Social Account</h3>
            </div>
            <form action="{{ route('social.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tag mr-2"></i>Platform
                        </label>
                        <select name="nama"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500"
                            required>
                            <option value="">Pilih Platform</option>
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                            <option value="youtube">YouTube</option>
                            <option value="linkedin">LinkedIn</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-link mr-2"></i>Link
                        </label>
                        <input type="url" name="link"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500"
                            placeholder="https://..." required>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                    <button type="button" onclick="closeSocialModal()"
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script --}}
    <script>
        // Check if SweetAlert2 is loaded
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 is not loaded. Please include the SweetAlert2 library.');
        }

        // Enhanced edit functions with error handling
        function editField(field) {
            const display = document.getElementById(`${field}-display`);
            const form = document.getElementById(`${field}-form`);

            if (!display || !form) {
                console.error(`Element with ID ${field}-display or ${field}-form not found`);
                return;
            }

            display.classList.add('hidden');
            form.classList.remove('hidden');

            const inputField = form.querySelector('input');
            if (inputField) {
                inputField.focus();
            }
        }

        function cancelEdit(field) {
            const display = document.getElementById(`${field}-display`);
            const form = document.getElementById(`${field}-form`);

            if (!display || !form) {
                console.error(`Element with ID ${field}-display or ${field}-form not found`);
                return;
            }

            display.classList.remove('hidden');
            form.classList.add('hidden');
        }

        function toggleSosmedEdit(id) {
            const display = document.getElementById(`sosmed-display-${id}`);
            const form = document.getElementById(`sosmed-form-${id}`);

            if (!display || !form) {
                console.error(`Elements for social media ${id} not found`);
                return;
            }

            if (display.classList.contains('hidden')) {
                display.classList.remove('hidden');
                form.classList.add('hidden');
            } else {
                display.classList.add('hidden');
                form.classList.remove('hidden');

                const inputField = form.querySelector('input');
                if (inputField) {
                    inputField.focus();
                }
            }
        }

        function cancelSosmedEdit(id) {
            const display = document.getElementById(`sosmed-display-${id}`);
            const form = document.getElementById(`sosmed-form-${id}`);

            if (!display || !form) {
                console.error(`Elements for social media ${id} not found`);
                return;
            }

            display.classList.remove('hidden');
            form.classList.add('hidden');
        }

        function editAddress(id) {
            const display = document.getElementById(`address-display-${id}`);
            const form = document.getElementById(`address-form-${id}`);

            if (!display || !form) {
                console.error(`Elements for address ${id} not found`);
                return;
            }

            display.classList.add('hidden');
            form.classList.remove('hidden');

            const inputField = form.querySelector('input');
            if (inputField) {
                inputField.focus();
            }
        }

        function cancelAddressEdit(id) {
            const display = document.getElementById(`address-display-${id}`);
            const form = document.getElementById(`address-form-${id}`);

            if (!display || !form) {
                console.error(`Elements for address ${id} not found`);
                return;
            }

            display.classList.remove('hidden');
            form.classList.add('hidden');
        }

        // Delete confirmation functions
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data alamat ini tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const deleteForm = document.getElementById(`delete-form-${id}`);
                    if (deleteForm) {
                        deleteForm.submit();
                    } else {
                        console.error(`Delete form for address ${id} not found`);
                        Swal.fire('Error!', 'Form tidak ditemukan.', 'error');
                    }
                }
            });
        }

        function confirmSosmedDelete(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Social media ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const deleteForm = document.getElementById(`delete-sosmed-form-${id}`);
                    if (deleteForm) {
                        deleteForm.submit();
                    } else {
                        console.error(`Delete form for social media ${id} not found`);
                        Swal.fire('Error!', 'Form tidak ditemukan.', 'error');
                    }
                }
            });
        }

        function confirmContactDelete(type, label) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: `Data ${label} ini akan dihapus!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const deleteForm = document.getElementById(`delete-${type}-form`);
                    if (deleteForm) {
                        const hiddenInput = deleteForm.querySelector(`input[name="${type}"]`);
                        if (hiddenInput) {
                            hiddenInput.value = '';
                        }
                        deleteForm.submit();
                    } else {
                        console.error(`Delete form for ${type} not found`);
                        Swal.fire('Error!', 'Form tidak ditemukan.', 'error');
                    }
                }
            });
        }

        // Modal functions
        function openContactModal() {
            const modal = document.getElementById('contactModal');
            const content = document.getElementById('contactModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeContactModal() {
            const modal = document.getElementById('contactModal');
            const content = document.getElementById('contactModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function openSocialModal() {
            const modal = document.getElementById('socialModal');
            const content = document.getElementById('socialModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeSocialModal() {
            const modal = document.getElementById('socialModal');
            const content = document.getElementById('socialModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('contactModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeContactModal();
            }
        });

        document.getElementById('socialModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSocialModal();
            }
        });
    </script>

    {{-- SweetAlert Success & Error --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>
    @endif
@endsection
