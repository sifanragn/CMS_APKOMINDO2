@extends('layouts.app')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Pembicara</h1>
        <div class="flex gap-2">
            <button id="bulkDeleteBtn" onclick="bulkDelete()"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                disabled>
                Hapus Terpilih
            </button>
            <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Tambah Speaker
            </button>
        </div>
    </div>

    <!-- Search Input -->
    <div class="mb-4">
        <input type="text" id="searchInput" placeholder="Cari berdasarkan nama atau jabatan..."
            class="border px-3 py-2 w-full rounded text-sm" onkeyup="searchTable()">
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-2 border"><input type="checkbox" id="selectAll"></th>
                    <th class="px-4 py-2 border">Foto</th>
                    <th class="px-4 py-2 border">Nama</th>
                    <th class="px-4 py-2 border">Jabatan</th>
                    <th class="px-4 py-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody id="speakerTable">
                @foreach ($speakers as $item)
                <tr>
                    <td class="px-4 py-2 border">
                        <input type="checkbox" name="speaker_ids[]" value="{{ $item->id }}" class="rowCheckbox"
                            onchange="updateBulkDeleteButton()">
                    </td>
                    <td class="px-4 py-2 border">
                        @if ($item->photo)
                            <img src="{{ asset('storage/' . $item->photo) }}"
                                class="w-24 h-24 object-cover object-center rounded shadow-md aspect-square">
                        @else
                            <div class="w-24 h-24 bg-gray-200 rounded shadow-md flex items-center justify-center">
                                <span class="text-gray-400 text-xs">No Photo</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2 border">{{ $item->name }}</td>
                    <td class="px-4 py-2 border">{{ $item->title ?? '-' }}</td>
                    <td class="px-4 py-2 border space-x-1">
                        <button
    onclick='openEditModal({
        id: {{ $item->id }},
        title: @json($item->title),
        content: @json($item->content),
        category_id: {{ $item->category_id ?? 'null' }},
        display: {{ $item->display ? 1 : 0 }},
        image: @json($item->image)
    })'
    class="px-3 py-1 text-xs bg-blue-50 text-blue-600 rounded">
    Edit
</button>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Form untuk bulk delete -->
<form id="bulkDeleteForm" method="POST" action="{{ route('agenda-speakers.bulkDelete') }}">
    @csrf
</form>

<!-- Form untuk single delete -->
<form id="singleDeleteForm" method="POST">
    @csrf
    @method('DELETE')
</form>

<!-- Modal Tambah -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
        <h2 class="text-lg font-semibold">Tambah Speaker</h2>
        <form id="addForm" action="{{ route('agenda-speakers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium">Nama</label>
                    <input type="text" name="name" required class="w-full border rounded p-2 text-sm" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Jabatan</label>
                    <input type="text" name="title" class="w-full border rounded p-2 text-sm" />
                </div>
            </div>

            <!-- Enhanced Image Upload Section -->
            <div class="mt-4">
                <label class="block mb-2 font-medium">Upload Foto</label>
                <div class="relative">
                    <input type="file" name="photo" id="addImageInput" onchange="previewImage(this, 'addPreview')"
                        accept="image/png,image/jpg,image/jpeg" class="hidden" />

                    <div id="addUploadArea" onclick="document.getElementById('addImageInput').click()"
                         class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors duration-200">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-600 mb-2">Klik untuk upload atau drag and drop</p>
                            <p class="text-sm text-gray-500">PNG, JPG, atau GIF (MAX. 2MB)</p>
                        </div>
                    </div>

                    <div id="addPreview" class="mt-4"></div>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="closeAddModal()"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                <button type="submit" id="addSubmitBtn"
                    class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
        <h2 class="text-lg font-semibold">Edit Speaker</h2>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium">Nama</label>
                    <input type="text" name="name" id="editName" required class="w-full border rounded p-2 text-sm" />
                </div>
                <div>
                    <label class="block mb-1 font-medium">Jabatan</label>
                    <input type="text" name="title" id="editTitle" class="w-full border rounded p-2 text-sm" />
                </div>
            </div>

            <!-- Enhanced Image Upload Section -->
            <div class="mt-4">
                <label class="block mb-2 font-medium">Ganti Foto</label>
                <div class="relative">
                    <input type="file" name="photo" id="editImageInput" onchange="previewImage(this, 'editPreview')"
                        accept="image/png,image/jpg,image/jpeg" class="hidden" />

                    <div id="editUploadArea" onclick="document.getElementById('editImageInput').click()"
                         class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors duration-200">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-600 mb-2">Klik untuk upload atau drag and drop</p>
                            <p class="text-sm text-gray-500">PNG, JPG, atau GIF (MAX. 2MB) - Opsional</p>
                        </div>
                    </div>

                    <div id="editPreview" class="mt-4"></div>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                <button type="submit" id="editSubmitBtn"
                    class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Show flash messages using SweetAlert
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showSuccessAlert("{{ session('success') }}");
        @endif

        @if(session('error'))
            showErrorAlert("{{ session('error') }}");
        @endif

        @if($errors->any()))
            let errorMessages = '';
            @foreach($errors->all() as $error)
                errorMessages += '{{ $error }}<br>';
            @endforeach
            showErrorAlert(errorMessages);
        @endif

        setupDragAndDrop();

        // Close modal when clicking outside
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });
    });

    // SweetAlert helper functions
    function showSuccessAlert(message) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            toast: true,
            position: 'top-end'
        });
    }

    function showErrorAlert(message) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33'
        });
    }

    function showLoadingAlert(message = 'Memproses...') {
        Swal.fire({
            title: message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Setup drag and drop functionality
    function setupDragAndDrop() {
        setupDragAndDropForElement('addUploadArea', 'addImageInput');
        setupDragAndDropForElement('editUploadArea', 'editImageInput');
    }

    function setupDragAndDropForElement(uploadAreaId, inputId) {
        const uploadArea = document.getElementById(uploadAreaId);
        const fileInput = document.getElementById(inputId);

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('border-blue-400', 'bg-blue-50');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
            }, false);
        });

        uploadArea.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const previewId = uploadAreaId === 'addUploadArea' ? 'addPreview' : 'editPreview';
                previewImage(fileInput, previewId);
            }
        }, false);
    }

    // Fungsi search untuk speaker
    function searchTable() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let rows = document.querySelectorAll("#speakerTable tr");

        rows.forEach(row => {
            let name = row.cells[2]?.textContent?.toLowerCase() || '';
            let title = row.cells[3]?.textContent?.toLowerCase() || '';

            const shouldShow = name.includes(input) || title.includes(input);
            row.style.display = shouldShow ? "" : "none";
        });
    }

    // Debounce untuk meningkatkan performa saat mengetik
    let searchTimer;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(searchTable, 300);
    });

    // Bulk Delete Function with Enhanced SweetAlert
    function bulkDelete() {
        const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);

        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal satu speaker untuk dihapus!',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        Swal.fire({
            title: `Hapus ${ids.length} speaker terpilih?`,
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showLoadingAlert('Menghapus speaker...');

                const form = document.getElementById('bulkDeleteForm');
                form.innerHTML = '@csrf';
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

    // Single Delete Function with Enhanced SweetAlert
    function deleteSingle(id, name) {
        Swal.fire({
            title: `Hapus speaker "${name}"?`,
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showLoadingAlert('Menghapus speaker...');

                const form = document.getElementById('singleDeleteForm');
                form.action = `/agenda-speakers/${id}`;
                form.submit();
            }
        });
    }

    // Update Bulk Delete Button
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

    // Modal Functions
    function openAddModal() {
        // Reset form
        document.getElementById('addForm').reset();
        document.getElementById('addPreview').innerHTML = '';
        document.getElementById('addUploadArea').style.display = 'block';

        // Show modal
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function openEditModal(id) {
        showLoadingAlert('Memuat data speaker...');

        fetch(`/agenda-speakers/${id}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Gagal mengambil data speaker');
                }
                return res.json();
            })
            .then(data => {
                Swal.close();

                const form = document.getElementById('editForm');
                form.action = `/agenda-speakers/${data.id}`;
                document.getElementById('editName').value = data.name;
                document.getElementById('editTitle').value = data.title || '';

                // Handle image preview
                const editPreview = document.getElementById('editPreview');
                const editUploadArea = document.getElementById('editUploadArea');

                if (data.photo) {
                    editPreview.innerHTML = `
                        <div class="relative inline-block">
                            <img src="/storage/${data.photo}" class="h-32 w-32 rounded-lg shadow-md object-cover border" alt="Current photo">
                            <button type="button" onclick="removeCurrentImage('edit')" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                    editUploadArea.style.display = 'none';
                } else {
                    editPreview.innerHTML = '';
                    editUploadArea.style.display = 'block';
                }

                document.getElementById('editModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching speaker data:', error);
                showErrorAlert('Gagal mengambil data speaker. Silakan coba lagi.');
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function removeCurrentImage(modalType) {
        const previewId = modalType === 'edit' ? 'editPreview' : 'addPreview';
        const uploadAreaId = modalType === 'edit' ? 'editUploadArea' : 'addUploadArea';
        const inputId = modalType === 'edit' ? 'editImageInput' : 'addImageInput';

        document.getElementById(previewId).innerHTML = '';
        document.getElementById(uploadAreaId).style.display = 'block';
        document.getElementById(inputId).value = '';
    }

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const uploadAreaId = previewId === 'addPreview' ? 'addUploadArea' : 'editUploadArea';
        const uploadArea = document.getElementById(uploadAreaId);

        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate file type
            if (!file.type.match('image.*')) {
                showErrorAlert('File harus berupa gambar (PNG/JPG/JPEG)!');
                input.value = '';
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                showErrorAlert('Ukuran file maksimal 2MB!');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="relative inline-block">
                        <img src="${e.target.result}" class="h-32 w-32 rounded-lg shadow-md object-cover border" alt="Preview">
                        <button type="button" onclick="removeCurrentImage('${previewId === 'addPreview' ? 'add' : 'edit'}')" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                `;
                uploadArea.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            uploadArea.style.display = 'block';
        }
    }

    // Enhanced Form Submissions with SweetAlert
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Add Form
        document.getElementById('addForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('addSubmitBtn');
            const originalText = submitBtn.textContent;

            // Validate required fields
            const name = formData.get('name');
            if (!name || name.trim() === '') {
                showErrorAlert('Nama speaker harus diisi!');
                return;
            }

            submitBtn.textContent = 'Menyimpan...';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeAddModal();
                    showSuccessAlert(data.message || 'Speaker berhasil ditambahkan!');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorAlert(data.message || 'Gagal menambahkan speaker!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorAlert('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });

        // Handle Edit Form
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('editSubmitBtn');
            const originalText = submitBtn.textContent;

            // Validate required fields
            const name = formData.get('name');
            if (!name || name.trim() === '') {
                showErrorAlert('Nama speaker harus diisi!');
                return;
            }

            submitBtn.textContent = 'Menyimpan...';
            submitBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeEditModal();
                    showSuccessAlert(data.message || 'Speaker berhasil diperbarui!');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorAlert(data.message || 'Gagal memperbarui speaker!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorAlert('Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    });
</script>
@endsection
