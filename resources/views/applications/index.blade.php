@extends('layouts.app')

@section('content')
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Lamaran Kerja</h1>
            <div class="flex gap-2">
                <button id="bulkDeleteBtn" onclick="bulkDelete()"
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled>
                    Hapus Terpilih
                </button>
                <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Tambah Lamaran
                </button>
            </div>
        </div>

        <!-- Search Input -->
        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Cari berdasarkan nama atau posisi..."
                class="border px-3 py-2 w-full rounded text-sm" onkeyup="searchTable()">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-2 border"><input type="checkbox" id="selectAll"></th>
                        <th class="px-4 py-2 border">Posisi</th>
                        <th class="px-4 py-2 border">Nama</th>
                        <th class="px-4 py-2 border">Email</th>
                        <th class="px-4 py-2 border">No. Telepon</th>
                        <th class="px-4 py-2 border">File CV</th>
                        <th class="px-4 py-2 border">Tanggal</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody id="applicationTable">
                    @foreach ($applications as $item)
                        <tr>
                            <td class="px-4 py-2 border">
                                <input type="checkbox" name="application_ids[]" value="{{ $item->id }}"
                                    class="rowCheckbox" onchange="updateBulkDeleteButton()">
                            </td>
                            <td class="px-4 py-2 border">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                    {{ $item->career->position_title ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border font-medium">{{ $item->nama }}</td>
                            <td class="px-4 py-2 border">{{ $item->email }}</td>
                            <td class="px-4 py-2 border">{{ $item->no_telepon }}</td>
                            <td class="px-4 py-2 border">
                                @if ($item->file)
                                    <a href="{{ route('applications.download', $item->id) }}"
                                        class="text-blue-600 hover:text-blue-800 text-xs underline">
                                        Download CV
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 border">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-2 border space-x-1">
                                <button onclick="openShowModal({{ $item->id }})"
                                    class="text-green-600 hover:text-green-800 px-2 py-1 text-xs border border-green-300 rounded hover:bg-green-50 inline-block">Detail</button>
                                <button onclick="openEditModal({{ $item->id }})"
                                    class="text-blue-600 hover:text-blue-800 px-2 py-1 text-xs border border-blue-300 rounded hover:bg-blue-50">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $applications->links() }}
        </div>
    </div>

    <script>
        window.applications = @json($applications->items());
        window.careers = @json($careers ?? []);
    </script>

    <form id="bulkDeleteForm" method="POST" action="{{ route('applications.bulkDelete') }}">
        @csrf
    </form>

    <!-- Modal Tambah -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Tambah Lamaran</h2>
            <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Posisi Lamaran</label>
                        <select name="career_id" id="addCareerSelect" required class="w-full border rounded p-2 text-sm">
                            <option value="">Pilih Posisi...</option>
                            @if (isset($careers))
                                @foreach ($careers as $career)
                                    <option value="{{ $career->id }}">{{ $career->position_title }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Nama Lengkap</label>
                        <input type="text" name="nama" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Email</label>
                        <input type="email" name="email" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">No. Telepon</label>
                        <input type="text" name="no_telepon" required class="w-full border rounded p-2 text-sm"
                            pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Upload CV</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="dropZone">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Drop Your CV files or Browse</p>
                                <p class="text-xs text-gray-500 mt-1">Supported format: PDF</p>
                            </div>
                            <input type="file" name="file" id="fileInput" accept=".pdf" class="hidden" />
                            <label for="fileInput"
                                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer">
                                Browse Files
                            </label>
                            <div id="fileNameDisplay" class="mt-2 text-sm text-gray-600"></div>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Cover Letter</label>
                        <textarea name="cover_letter" id="addCoverLetter" rows="4" class="w-full border rounded p-2 text-sm"
                            placeholder="Tuliskan cover letter Anda..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="showModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold">Detail Lamaran</h2>
                <button onclick="closeShowModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="showContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Edit Lamaran</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editId">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Posisi Lamaran</label>
                        <select name="career_id" id="editCareerSelect" required
                            class="w-full border rounded p-2 text-sm">
                            <option value="">Pilih Posisi...</option>
                            @if (isset($careers))
                                @foreach ($careers as $career)
                                    <option value="{{ $career->id }}">{{ $career->position_title }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Nama Lengkap</label>
                        <input type="text" name="nama" id="editNama" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Email</label>
                        <input type="email" name="email" id="editEmail" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">No. Telepon</label>
                        <input type="text" name="no_telepon" id="editNoTelepon" required
                            class="w-full border rounded p-2 text-sm" pattern="[0-9]*"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Ganti CV (Opsional)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center" id="editDropZone">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mt-1 text-sm text-gray-600">Drop Your CV files or Browse</p>
                                <p class="text-xs text-gray-500 mt-1">Format: PDF (Max: 2MB)</p>
                            </div>
                            <input type="file" name="file" id="editFileInput" accept=".pdf" class="hidden" />
                            <label for="editFileInput"
                                class="mt-3 inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer text-sm">
                                Browse Files
                            </label>
                            <div id="editFileNameDisplay" class="mt-1 text-sm text-blue-600"></div>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Cover Letter</label>
                        <textarea name="cover_letter" id="editCoverLetter" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery dan SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Handle success/error messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if ($errors->any())
                let errorMessages = [];
                @foreach ($errors->all() as $error)
                    errorMessages.push('{{ $error }}');
                @endforeach
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessages.join('\n')
                });
            @endif
        });

        // Search function
        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#applicationTable tr");

            rows.forEach(row => {
                let position = row.cells[1]?.textContent?.toLowerCase() || '';
                let name = row.cells[2]?.textContent?.toLowerCase() || '';
                let email = row.cells[3]?.textContent?.toLowerCase() || '';

                const shouldShow = position.includes(input) || name.includes(input) || email.includes(input);
                row.style.display = shouldShow ? "" : "none";
            });
        }

        // Debounce for better performance
        let searchTimer;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(searchTable, 300);
        });

        function openAddModal() {
            document.querySelector('#addModal form').reset();
            document.getElementById('fileNameDisplay').textContent = '';
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openShowModal(id) {
            fetch(`/applications/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const application = data.data;
                        document.getElementById('showContent').innerHTML = `
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Posisi</label>
                                    <p class="text-sm bg-blue-100 text-blue-800 px-3 py-2 rounded-full inline-block">
                                        ${application.career?.position_title || 'N/A'}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded">${application.nama}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded">${application.email}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded">${application.no_telepon}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">File CV</label>
                                    ${application.file
                                        ? `<a href="/applications/${application.id}/download"
                                                 class="text-blue-600 hover:text-blue-800 text-sm underline">
                                                 Download CV
                                               </a>`
                                        : '<p class="text-sm text-gray-500">Tidak ada file</p>'
                                    }
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Letter</label>
                                    <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded min-h-[100px]">
                                        ${application.cover_letter || 'Tidak ada cover letter'}
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lamaran</label>
                                    <p class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded">
                                        ${new Date(application.created_at).toLocaleDateString('id-ID', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        })}
                                    </p>
                                </div>
                            </div>
                        `;
                        document.getElementById('showModal').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memuat data lamaran', 'error');
                });
        }

        function closeShowModal() {
            document.getElementById('showModal').classList.add('hidden');
        }

        function openEditModal(id) {
            fetch(`/applications/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const application = data.data;

                        // Set form action
                        document.getElementById('editForm').action = `/applications/${application.id}`;

                        // Fill form fields
                        document.getElementById('editId').value = application.id;
                        document.getElementById('editCareerSelect').value = application.career_id;
                        document.getElementById('editNama').value = application.nama;
                        document.getElementById('editEmail').value = application.email;
                        document.getElementById('editNoTelepon').value = application.no_telepon;
                        document.getElementById('editCoverLetter').value = application.cover_letter || '';

                        // Reset file input
                        document.getElementById('editFileInput').value = '';
                        document.getElementById('editFileNameDisplay').textContent = '';

                        document.getElementById('editModal').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memuat data lamaran', 'error');
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // FIXED BULK DELETE FUNCTION
        function bulkDelete() {
            const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                Swal.fire('Tidak ada yang dipilih', '', 'warning');
                return;
            }

            Swal.fire({
                title: `Hapus ${ids.length} lamaran terpilih?`,
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gunakan form yang sudah ada
                    const form = document.getElementById('bulkDeleteForm');
                    
                    // Clear form dan tambahkan CSRF
                    form.innerHTML = '@csrf';
                    
                    // Tambahkan IDs yang dipilih
                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    
                    form.submit();
                }
            });
        }

        function updateBulkDeleteButton() {
            const checked = document.querySelectorAll('.rowCheckbox:checked');
            const btn = document.getElementById('bulkDeleteBtn');
            btn.disabled = checked.length === 0;
            btn.textContent = checked.length > 0 ? `Hapus Terpilih (${checked.length})` : 'Hapus Terpilih';
        }

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.rowCheckbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkDeleteButton();
        });

        // File input handling for the new UI
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            if (this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                fileNameDisplay.textContent = '';
            }
        });

        document.getElementById('editFileInput').addEventListener('change', function(e) {
            const fileNameDisplay = document.getElementById('editFileNameDisplay');
            if (this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
            } else {
                fileNameDisplay.textContent = '';
            }
        });

        // Drag and drop functionality
        const dropZone = document.getElementById('dropZone');
        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                dropZone.classList.add('border-blue-500');
            }

            function unhighlight() {
                dropZone.classList.remove('border-blue-500');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                const fileInput = document.getElementById('fileInput');
                fileInput.files = files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        }

        // Edit modal drag and drop
        const editDropZone = document.getElementById('editDropZone');
        if (editDropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                editDropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                editDropZone.addEventListener(eventName, highlightEdit, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                editDropZone.addEventListener(eventName, unhighlightEdit, false);
            });

            function highlightEdit() {
                editDropZone.classList.add('border-blue-500');
            }

            function unhighlightEdit() {
                editDropZone.classList.remove('border-blue-500');
            }

            editDropZone.addEventListener('drop', handleEditDrop, false);

            function handleEditDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                const fileInput = document.getElementById('editFileInput');
                fileInput.files = files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        }

        // Phone number validation
        document.querySelectorAll('input[name="no_telepon"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });

        // File validation (only accept PDF)
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Check file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
                        this.value = '';
                        if (this.id === 'fileInput') {
                            document.getElementById('fileNameDisplay').textContent = '';
                        } else {
                            document.getElementById('editFileNameDisplay').textContent = '';
                        }
                        return;
                    }

                    // Check file type (only PDF)
                    if (file.type !== 'application/pdf') {
                        Swal.fire('Error', 'File harus berupa PDF', 'error');
                        this.value = '';
                        if (this.id === 'fileInput') {
                            document.getElementById('fileNameDisplay').textContent = '';
                        } else {
                            document.getElementById('editFileNameDisplay').textContent = '';
                        }
                        return;
                    }
                }
            });
        });

        // Form validation
        document.querySelector('#addModal form').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                e.preventDefault();
                Swal.fire('Error', 'Mohon lengkapi semua field yang wajib diisi', 'error');
            }
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            if (!isValid) {
                e.preventDefault();
                Swal.fire('Error', 'Mohon lengkapi semua field yang wajib diisi', 'error');
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + N for new application
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                openAddModal();
            }

            // Escape key to close modals
            if (e.key === 'Escape') {
                const addModal = document.getElementById('addModal');
                const editModal = document.getElementById('editModal');
                const showModal = document.getElementById('showModal');

                if (!addModal.classList.contains('hidden')) {
                    closeAddModal();
                }
                if (!editModal.classList.contains('hidden')) {
                    closeEditModal();
                }
                if (!showModal.classList.contains('hidden')) {
                    closeShowModal();
                }
            }
        });
    </script>
@endsection