@extends('layouts.app')

@section('content')
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Data Kegiatan</h1>
            <div class="flex gap-2">
                <button id="bulkDeleteBtn" onclick="bulkDelete()"
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled>
                    Hapus Terpilih
                </button>
                <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Tambah Kegiatan
                </button>
            </div>
        </div>

        <!-- Search Input -->
        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Cari berdasarkan judul..."
                class="border px-3 py-2 w-full rounded text-sm" onkeyup="searchTable()">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-2 border"><input type="checkbox" id="selectAll"></th>
                        <th class="px-4 py-2 border">Gambar</th>
                        <th class="px-4 py-2 border">Judul</th>
                        <th class="px-4 py-2 border">Kategori</th>
                        <th class="px-4 py-2 border">Deskripsi</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody id="kegiatanTable">
                    @foreach ($kegiatans as $item)
                        <tr>
                            <td class="px-4 py-2 border">
                                <input type="checkbox" name="kegiatan_ids[]" value="{{ $item->id }}" class="rowCheckbox"
                                    onchange="updateBulkDeleteButton()">
                            </td>
                            <td class="px-4 py-2 border">
                                @if ($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                        class="w-24 h-24 object-cover object-center rounded shadow-md aspect-square">
                                @else
                                    <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2 border font-medium">{{ $item->title }}</td>
                            <td class="px-4 py-2 border">{{ $item->category->name ?? '-' }}</td>
                            <td class="px-4 py-2 border">
                                <div class="max-w-xs">
                                    {!! Str::limit($item->description ?? '-', 50) !!}
                                </div>
                            </td>
                            <td class="px-4 py-2 border space-x-1">
                                <button onclick="openEditModal(this)" data-item='@json($item)'
                                    class="text-blue-600 hover:text-blue-800 px-2 py-1 text-xs border border-blue-300 rounded hover:bg-blue-50">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <form id="bulkDeleteForm" method="POST" action="{{ route('kegiatan.bulkDelete') }}">
        @csrf
    </form>

    <!-- Modal Tambah -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Tambah Kegiatan</h2>
            <form id="addForm" action="{{ route('kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_kegiatan_id" required class="w-full border rounded p-2 text-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Deskripsi</label>
                        <textarea name="description" id="editorAddDescription" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                    </div>
                </div>

                <!-- Enhanced Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Upload Gambar <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="file" name="image" id="addImageInput" onchange="previewImage(this, 'addPreview')"
                            accept="image/png,image/jpg,image/jpeg" class="hidden" required />

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
                                <p class="text-sm text-gray-500">PNG, JPG, atau JPEG (MAX. 2MB) - Wajib</p>
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
            <h2 class="text-lg font-semibold">Edit Kegiatan</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="editTitle" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_kegiatan_id" id="editCategory" required
                            class="w-full border rounded p-2 text-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Deskripsi</label>
                        <textarea name="description" id="editorEditDescription" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                    </div>
                </div>

                <!-- Enhanced Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Ganti Gambar</label>
                    <div class="relative">
                        <input type="file" name="image" id="editImageInput"
                            onchange="previewImage(this, 'editPreview')" accept="image/png,image/jpg,image/jpeg"
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
                                <p class="text-sm text-gray-500">PNG, JPG, atau JPEG (MAX. 2MB) - Opsional</p>
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

    <!-- jQuery dan Select2 CSS/JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

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

        // Setup drag and drop functionality
        function setupDragAndDrop() {
            ['addUploadArea', 'editUploadArea'].forEach(id => {
                const element = document.getElementById(id);
                const inputId = id === 'addUploadArea' ? 'addImageInput' : 'editImageInput';
                const previewId = id === 'addUploadArea' ? 'addPreview' : 'editPreview';

                element.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    element.classList.add('border-blue-400', 'bg-blue-50');
                });

                element.addEventListener('dragleave', () => {
                    element.classList.remove('border-blue-400', 'bg-blue-50');
                });

                element.addEventListener('drop', (e) => {
                    e.preventDefault();
                    element.classList.remove('border-blue-400', 'bg-blue-50');

                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        document.getElementById(inputId).files = files;
                        previewImage(document.getElementById(inputId), previewId);
                    }
                });
            });
        }

        // Search functionality
        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#kegiatanTable tr");

            rows.forEach(row => {
                let title = row.cells[2]?.textContent?.toLowerCase() || '';
                let category = row.cells[3]?.textContent?.toLowerCase() || '';

                const shouldShow = title.includes(input) || category.includes(input);
                row.style.display = shouldShow ? "" : "none";
            });
        }

        // Debounce search input
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
                    title: 'Tidak ada yang dipilih',
                    text: '',
                    icon: 'warning'
                });
                return;
            }

            Swal.fire({
                title: `Hapus ${ids.length} kegiatan terpilih?`,
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
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
            const data = JSON.parse(button.getAttribute('data-item'));
            const form = document.getElementById('editForm');

            form.action = `/kegiatan/${data.id}`;
            document.getElementById('editTitle').value = data.title || '';
            document.getElementById('editCategory').value = data.category_kegiatan_id || '';

            // Set editor content
            if (editDescriptionEditor) {
                editDescriptionEditor.setData(data.description || '');
            }

            // Handle image preview
            const editPreview = document.getElementById('editPreview');
            const editUploadArea = document.getElementById('editUploadArea');

            if (data.image) {
                editPreview.innerHTML = `
                    <div class="relative inline-block">
                        <img src="/storage/${data.image}" class="h-32 w-32 rounded-lg shadow-md object-cover border" alt="Current image">
                        <button type="button" onclick="removeCurrentImage('edit')" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <input type="hidden" name="current_image" value="${data.image}">
                    </div>
                `;
                editUploadArea.style.display = 'none';
            } else {
                editPreview.innerHTML = '';
                editUploadArea.style.display = 'block';
            }

            document.getElementById('editModal').classList.remove('hidden');
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
                    Swal.fire('Error', 'File harus berupa gambar (PNG/JPG)', 'error');
                    input.value = '';
                    return;
                }

                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
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

        // Form submission handlers to ensure CKEditor data is synced
        document.querySelector('#addForm').addEventListener('submit', function(e) {
            if (addDescriptionEditor) {
                document.querySelector('#editorAddDescription').value = addDescriptionEditor.getData();
            }
        });

        document.querySelector('#editForm').addEventListener('submit', function(e) {
            if (editDescriptionEditor) {
                document.querySelector('#editorEditDescription').value = editDescriptionEditor.getData();
            }
        });

        // Handle form validation messages
        document.addEventListener('DOMContentLoaded', function() {
            const showAlert = (type, message) => {
                Swal.fire({
                    icon: type,
                    title: type === 'success' ? 'Berhasil!' : 'Error!',
                    text: message,
                    timer: 3000,
                    showConfirmButton: false
                });
            };

            // Check for Laravel session messages
            @if(session('success'))
                showAlert('success', '{{ session('success') }}');
            @endif

            @if(session('error'))
                showAlert('error', '{{ session('error') }}');
            @endif

            @if($errors->any())
                let errorMessages = [];
                @foreach($errors->all() as $error)
                    errorMessages.push('{{ $error }}');
                @endforeach
                showAlert('error', errorMessages.join('\n'));
            @endif
        });

        // Additional helper functions
        function resetFormValidation(formId) {
            const form = document.getElementById(formId);
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.classList.remove('border-red-500', 'border-green-500');
                const errorMsg = input.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        }

        function validateForm(formId) {
            const form = document.getElementById(formId);
            let isValid = true;

            // Reset previous validation
            resetFormValidation(formId);

            // Required field validation
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    showFieldError(field, 'Field ini wajib diisi');
                    isValid = false;
                } else {
                    field.classList.add('border-green-500');
                }
            });

            // Email validation
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    field.classList.add('border-red-500');
                    showFieldError(field, 'Format email tidak valid');
                    isValid = false;
                }
            });

            // URL validation
            const urlFields = form.querySelectorAll('input[type="url"]');
            urlFields.forEach(field => {
                if (field.value && !isValidURL(field.value)) {
                    field.classList.add('border-red-500');
                    showFieldError(field, 'Format URL tidak valid');
                    isValid = false;
                }
            });

            return isValid;
        }

        function showFieldError(field, message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-red-500 text-xs mt-1';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function isValidURL(url) {
            try {
                new URL(url);
                return true;
            } catch {
                return false;
            }
        }

        // Enhanced drag and drop visual feedback
        function enhanceDragDropFeedback() {
            const dropAreas = document.querySelectorAll('[id$="UploadArea"]');

            dropAreas.forEach(area => {
                area.addEventListener('dragenter', function(e) {
                    e.preventDefault();
                    this.style.transform = 'scale(1.02)';
                    this.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.3)';
                });

                area.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    if (!this.contains(e.relatedTarget)) {
                        this.style.transform = 'scale(1)';
                        this.style.boxShadow = 'none';
                    }
                });

                area.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.transform = 'scale(1)';
                    this.style.boxShadow = 'none';
                });
            });
        }

        // Call enhanced drag drop on page load
        document.addEventListener('DOMContentLoaded', function() {
            enhanceDragDropFeedback();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + N for new kegiatan
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                openAddModal();
            }

            // Escape key to close modals
            if (e.key === 'Escape') {
                const addModal = document.getElementById('addModal');
                const editModal = document.getElementById('editModal');

                if (!addModal.classList.contains('hidden')) {
                    closeAddModal();
                }
                if (!editModal.classList.contains('hidden')) {
                    closeEditModal();
                }
            }
        });
    </script>
@endsection
