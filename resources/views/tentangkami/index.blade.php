@extends('layouts.app')

@section('content')
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Tentang Kami</h1>
            <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Tambah Data
            </button>
        </div>

        <!-- Search Input -->
        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Cari berdasarkan judul..."
                class="border px-3 py-2 w-full rounded text-sm" onkeyup="searchTable()">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm rounded-xl overflow-hidden">
    <thead>
        <tr class="bg-gray-50 border-b">
            <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-700">Judul</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-700">Kategori</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-700">Gambar</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-700">Aksi</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-700">Tampilkan</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($tentangkami as $index => $item)
            <tr class="border-b hover:bg-gray-50 transition">
                <td class="px-4 py-3">{{ $index + 1 }}</td>

                <td class="px-4 py-3 font-medium text-gray-800">
                    {{ $item->title }}
                </td>

                <td class="px-4 py-3">
                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                        {{ $item->category->nama ?? 'Tidak ada kategori' }}
                    </span>
                </td>

                <td class="px-4 py-3">
                    @if ($item->image)
                        <img src="{{ asset($item->image) }}" 
                             class="w-16 h-16 rounded-lg object-cover border shadow-sm">
                    @else
                        <span class="text-gray-400 text-xs">Tidak ada gambar</span>
                    @endif
                </td>

                <td class="px-4 py-3 space-x-1">
                    <button onclick="showDetailModal(this)" data-detail='@json($item)'
                        class="px-3 py-1 text-xs bg-gray-50 text-gray-700 border border-gray-300 rounded hover:bg-gray-100">
                        Detail
                    </button>

                    <button onclick="openEditModal(this)" data-tentangkami='@json($item)'
                        class="px-3 py-1 text-xs bg-blue-50 text-blue-600 border border-blue-200 rounded hover:bg-blue-100">
                        Edit
                    </button>

                    <button onclick="confirmDelete({{ $item->id }})"
                        class="px-3 py-1 text-xs bg-red-50 text-red-600 border border-red-200 rounded hover:bg-red-100">
                        Hapus
                    </button>
                </td>

                <td class="px-4 py-3">
                    <label class="iphone-switch">
                        <input type="checkbox" onchange="toggleDisplay({{ $item->id }}, this)"
                            {{ $item->display_on_home ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
        @endforeach

        @if ($tentangkami->count() == 0)
            <tr>
                <td colspan="6" class="py-10 text-center text-gray-500">Tidak ada data tersedia</td>
            </tr>
        @endif
    </tbody>
</table>

        </div>
    </div>


    <!-- Modal Detail -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl p-6 overflow-y-auto max-h-screen space-y-4">

        <h2 class="text-lg font-semibold">Detail Tentang Kami</h2>

        <div class="space-y-4">

            <div>
                <h3 class="text-sm text-gray-500">Judul</h3>
                <p id="detailTitle" class="font-semibold text-gray-800"></p>
            </div>

            <div>
                <h3 class="text-sm text-gray-500">Kategori</h3>
                <span id="detailCategory" class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs"></span>
            </div>

            <div>
                <h3 class="text-sm text-gray-500">Deskripsi</h3>
                <div id="detailDescription" class="prose max-w-none text-gray-700"></div>
            </div>

            <div>
                <h3 class="text-sm text-gray-500">Gambar</h3>
                <img id="detailImage" onclick="openImageZoom(this.src)"
     class="w-full max-h-72 object-contain rounded-lg shadow-md border cursor-zoom-in bg-gray-100" />

            </div>

            <div>
                <h3 class="text-sm text-gray-500">Tampilkan di Halaman Utama</h3>
                <p id="detailDisplay" class="font-medium"></p>
            </div>

        </div>

        <div class="text-right">
            <button id="detailEditBtn"
                class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">
                Edit
            </button>
            <button onclick="closeDetailModal()" 
                class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">
                Tutup
            </button>
        </div>
    </div>
</div>

    <!-- Modal Tambah -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Tambah Data Tentang Kami</h2>
            <form id="addForm" action="{{ route('tentangkami.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">
                            <input type="checkbox" name="display_on_home" value="1" class="mr-2">
                            Tampilkan di Halaman Utama
                        </label>
                    </div>

                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full border rounded p-2 text-sm" />
                        <p class="error-text text-red-500 text-xs mt-1 hidden">Judul wajib diisi</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_tentangkami_id" id="addCategorySelect" required
                            class="w-full border rounded p-2 text-sm">
                            <option value="">Pilih Kategori</option>
                            @if($categories && $categories->count() > 0)
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nama }}</option>
                                @endforeach
                            @endif
                        </select>
                        <p class="error-text text-red-500 text-xs mt-1 hidden">Kategori wajib dipilih</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" id="editorAddDescription" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                        <p class="error-text text-red-500 text-xs mt-1 hidden">Deskripsi wajib diisi</p>
                    </div>
                </div>

                <!-- Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Upload Gambar <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="file" name="image" id="addImageInput" onchange="previewImage(this, 'addPreview')"
                            accept="image/png,image/jpg,image/jpeg,image/gif" required class="hidden" />
                        <p class="error-text text-red-500 text-xs mt-1 hidden">Gambar wajib diupload</p>

                        <div id="addUploadArea" onclick="document.getElementById('addImageInput').click()"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors duration-200">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <p class="text-gray-600 mb-2">Klik untuk upload atau drag and drop</p>
                                <p class="text-sm text-gray-500">PNG, JPG, JPEG, atau GIF (MAX. 2MB)</p>
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
            <h2 class="text-lg font-semibold">Edit Data Tentang Kami</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">
                            <input type="checkbox" name="display_on_home" id="editDisplayOnHome" value="1"
                                class="mr-2">
                            Tampilkan di Halaman Utama
                        </label>
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="editTitle" required
                            class="w-full border rounded p-2 text-sm" />
                        <p class="error-text text-red-500 text-xs mt-1 hidden">Judul wajib diisi</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_tentangkami_id" id="editCategorySelect" required
                            class="w-full border rounded p-2 text-sm">
                            <option value="">Pilih Kategori</option>
                            @if($categories && $categories->count() > 0)
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nama }}</option>
                                @endforeach
                            @endif
                        </select>
                        <p class="error-text text-red-500 text-xs mt-1 hidden">Kategori wajib dipilih</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" id="editorEditDescription" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                        <p class="error-text text-red-500 text-xs mt-1 hidden">Deskripsi wajib diisi</p>
                    </div>
                </div>

                <!-- Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Ganti Gambar</label>
                    <div class="relative">
                        <input type="file" name="image" id="editImageInput"
                            onchange="previewImage(this, 'editPreview')" accept="image/png,image/jpg,image/jpeg,image/gif"
                            class="hidden" />

                        <div id="editUploadArea" onclick="document.getElementById('editImageInput').click()"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors duration-200">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <p class="text-gray-600 mb-2">Klik untuk upload atau drag and drop</p>
                                <p class="text-sm text-gray-500">PNG, JPG, JPEG, atau GIF (MAX. 2MB) - Opsional</p>
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // Global variables for CKEditor instances
        let addDescriptionEditor = null;
        let editDescriptionEditor = null;

        // Initialize CKEditor when the page loads
        $(document).ready(function() {
            initializeCKEditor();
            setupDragAndDrop();
        });

        function initializeCKEditor() {
            // Enhanced configuration for CKEditor with more features including numbering
            const editorConfig = {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                        'numberedList', 'bulletedList', '|',
                        'outdent', 'indent', '|',
                        'alignment', '|',
                        'link', 'insertTable', '|',
                        'blockQuote', 'insertImage', '|',
                        'undo', 'redo', '|',
                        'sourceEditing'
                    ]
                },
                language: 'id',
                list: {
                    properties: {
                        styles: true,
                        startIndex: true,
                        reversed: true
                    }
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                },
                fontSize: {
                    options: [
                        9,
                        11,
                        13,
                        'default',
                        17,
                        19,
                        21
                    ]
                },
                alignment: {
                    options: [ 'left', 'right', 'center', 'justify' ]
                },
                image: {
                    toolbar: [
                        'imageTextAlternative',
                        'imageStyle:inline',
                        'imageStyle:block',
                        'imageStyle:side'
                    ]
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells'
                    ]
                }
            };

            // Initialize CKEditor for Add Modal
            ClassicEditor
                .create(document.querySelector('#editorAddDescription'), editorConfig)
                .then(editor => {
                    addDescriptionEditor = editor;
                    console.log('Add Description Editor initialized successfully');

                    // Sync with form on change
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#editorAddDescription').value = editor.getData();
                    });
                })
                .catch(error => {
                    console.error('Error initializing add description editor:', error);
                });

            // Initialize CKEditor for Edit Modal
            ClassicEditor
                .create(document.querySelector('#editorEditDescription'), editorConfig)
                .then(editor => {
                    editDescriptionEditor = editor;
                    console.log('Edit Description Editor initialized successfully');

                    // Sync with form on change
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#editorEditDescription').value = editor.getData();
                    });
                })
                .catch(error => {
                    console.error('Error initializing edit description editor:', error);
                });
        }

        function setupDragAndDrop() {
            setupDragAndDropForElement('addUploadArea', 'addImageInput');
            setupDragAndDropForElement('editUploadArea', 'editImageInput');
        }

        function setupDragAndDropForElement(uploadAreaId, inputId) {
            const uploadArea = document.getElementById(uploadAreaId);
            const fileInput = document.getElementById(inputId);

            if (!uploadArea || !fileInput) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev =>
                uploadArea.addEventListener(ev, e => {
                    e.preventDefault();
                    e.stopPropagation();
                })
            );

            ['dragenter', 'dragover'].forEach(ev =>
                uploadArea.addEventListener(ev, () => uploadArea.classList.add('border-blue-400', 'bg-blue-50'))
            );

            ['dragleave', 'drop'].forEach(ev =>
                uploadArea.addEventListener(ev, () => uploadArea.classList.remove('border-blue-400', 'bg-blue-50'))
            );

            uploadArea.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    previewImage(fileInput, uploadAreaId === 'addUploadArea' ? 'addPreview' : 'editPreview');
                }
            });
        }

        function openAddModal() {
            console.log('Opening add modal');

            // Reset form
            document.getElementById('addForm').reset();
            document.getElementById('addPreview').innerHTML = '';
            document.getElementById('addUploadArea').style.display = 'block';

            // Reset button state
            $('#addSubmitBtn').text('Simpan').prop('disabled', false);

            // Reset error states
            $('#addForm .error-text').addClass('hidden');
            $('#addForm [required]').removeClass('border-red-500');

            // Reset editor content
            if (addDescriptionEditor) {
                addDescriptionEditor.setData('');
            }

            // Show modal
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(button) {
            console.log('Opening edit modal');

            const tentangkami = JSON.parse(button.getAttribute('data-tentangkami'));
            const form = document.getElementById('editForm');

            // Set form action and values
            form.action = `/tentangkami/${tentangkami.id}`;
            document.getElementById('editTitle').value = tentangkami.title || '';
            document.getElementById('editCategorySelect').value = tentangkami.category_tentangkami_id || '';
            document.getElementById('editDisplayOnHome').checked = tentangkami.display_on_home == 1;

            // Reset button state
            $('#editSubmitBtn').text('Simpan').prop('disabled', false);

            // Handle image preview
            const editPreview = document.getElementById('editPreview');
            const editUploadArea = document.getElementById('editUploadArea');

            if (tentangkami.image) {
                const imageUrl = tentangkami.image.startsWith('http') ? tentangkami.image :
                    `{{ asset('') }}${tentangkami.image}`;
                editPreview.innerHTML = `
                    <div class="relative inline-block">
                        <img src="${imageUrl}" class="h-32 w-32 rounded-lg shadow-md object-cover border">
                        <button type="button" onclick="removeCurrentImage('edit')" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>`;
                editUploadArea.style.display = 'none';
            } else {
                editPreview.innerHTML = '';
                editUploadArea.style.display = 'block';
            }

            // Reset error states
            $('#editForm .error-text').addClass('hidden');
            $('#editForm [required]').removeClass('border-red-500');

            // Show modal
            document.getElementById('editModal').classList.remove('hidden');

            // Set editor content
            if (editDescriptionEditor) {
                editDescriptionEditor.setData(tentangkami.description || '');
            }
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'File harus berupa gambar',
                        confirmButtonColor: '#3b82f6'
                    });
                    input.value = '';
                    return;
                }

                // Validate file size (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ukuran file maksimal 2MB',
                        confirmButtonColor: '#3b82f6'
                    });
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="relative inline-block">
                            <img src="${e.target.result}" class="h-32 w-32 rounded-lg shadow-md object-cover border">
                            <button type="button" onclick="removeCurrentImage('${previewId === 'addPreview' ? 'add' : 'edit'}')" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>`;
                    uploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                uploadArea.style.display = 'block';
            }
        }

        function removeCurrentImage(modalType) {
            const previewId = modalType === 'edit' ? 'editPreview' : 'addPreview';
            const uploadAreaId = modalType === 'edit' ? 'editUploadArea' : 'addUploadArea';
            const inputId = modalType === 'edit' ? 'editImageInput' : 'addImageInput';

            document.getElementById(previewId).innerHTML = '';
            document.getElementById(uploadAreaId).style.display = 'block';
            document.getElementById(inputId).value = '';
        }

        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#tentangkamiTable tr");

            rows.forEach(row => {
                let title = row.cells[1]?.textContent?.toLowerCase() || '';
                let category = row.cells[2]?.textContent?.toLowerCase() || '';
                row.style.display = (title.includes(input) || category.includes(input)) ? "" : "none";
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/tentangkami/${id}`;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Form submission handlers to ensure CKEditor data is synced
        document.querySelector('#addForm').addEventListener('submit', function(e) {
            if (addDescriptionEditor) {
                // Sync CKEditor content to textarea before submission
                document.querySelector('#editorAddDescription').value = addDescriptionEditor.getData();
            }
        });

        document.querySelector('#editForm').addEventListener('submit', function(e) {
            if (editDescriptionEditor) {
                // Sync CKEditor content to textarea before submission
                document.querySelector('#editorEditDescription').value = editDescriptionEditor.getData();
            }
        });

        // Session messages
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3b82f6',
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonColor: '#3b82f6'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach',
                confirmButtonColor: '#3b82f6'
            });
        @endif

        function toggleDisplay(id, checkbox) {
    $.ajax({
        url: `/tentangkami/toggle/${id}`,
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            display_on_home: checkbox.checked ? 1 : 0
        },
        success: function(res) {
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: res.message,
                showConfirmButton: false,
                timer: 2000
            });
        },
        error: function() {
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Tidak dapat mengubah status.",
            });
            checkbox.checked = !checkbox.checked; // revert
        }
    });
}
    </script>

    <script>
    //modal detail
        function showDetailModal(button) {
    const data = JSON.parse(button.getAttribute("data-detail"));

    document.getElementById("detailTitle").textContent = data.title;
    document.getElementById("detailCategory").textContent = data.category?.nama ?? "Tidak ada kategori";
    document.getElementById("detailDescription").innerHTML = data.description ?? "-";

    if (data.image) {
        document.getElementById("detailImage").src = data.image.startsWith("http")
            ? data.image
            : `{{ asset('') }}` + data.image;
    } else {
        document.getElementById("detailImage").src = "";
    }

    document.getElementById("detailDisplay").textContent =
        data.display_on_home ? "Ya, tampil di halaman utama" : "Tidak ditampilkan";

        // === TOMBOL EDIT ===
    document.getElementById("detailEditBtn").onclick = function () {

        closeDetailModal();

        // fake button untuk buka modal edit
        let fakeBtn = document.createElement("button");
        fakeBtn.setAttribute("data-tentangkami", JSON.stringify(data));

        openEditModal(fakeBtn);
    };

    document.getElementById("detailModal").classList.remove("hidden");
}

function closeDetailModal() {
    document.getElementById("detailModal").classList.add("hidden");
}

    </script>
    <style>
.iphone-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
}

.iphone-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

/* ON */
input:checked + .slider {
    background-color: #008000; /* iPhone green */
}

input:checked + .slider:before {
    transform: translateX(22px);
}

table thead th {
    border-bottom: 2px solid #e5e7eb;
}

table tbody tr td {
    vertical-align: middle;
}

</style>

@endsection
