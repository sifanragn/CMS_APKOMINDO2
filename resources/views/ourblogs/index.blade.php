@extends('layouts.app')

@section('content')
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Our Blogs</h1>
            <div class="flex gap-2">
                <button id="bulkDeleteBtn" onclick="bulkDelete()"
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled>
                    Hapus Terpilih
                </button>
                <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Tambah Berita
                </button>
            </div>
        </div>

        <!-- Search Input -->
        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Cari berdasarkan judul atau kategori..."
                class="border px-3 py-2 w-full rounded text-sm" onkeyup="searchTable()">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th class="px-4 py-3">Gambar</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Waktu Baca</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody id="blogTable">
@foreach ($ourblogs as $item)
<tr class="border-b hover:bg-gray-50 transition align-middle">

    {{-- Checkbox --}}
    <td class="px-4 py-3">
        <input type="checkbox"
            value="{{ $item->id }}"
            class="rowCheckbox"
            onchange="updateBulkDeleteButton()">
    </td>

    {{-- Gambar --}}
    <td class="px-4 py-3">
        @if ($item->image)
            <img
                src="{{ asset('storage/' . $item->image) }}"
                class="w-20 h-20 rounded-lg object-cover border shadow-sm"
                alt="{{ $item->title }}"
            >
        @else
            <span class="text-gray-400 text-xs">Tidak ada</span>
        @endif
    </td>

    {{-- Judul --}}
    <td class="px-4 py-3 font-medium text-gray-800">
        {{ $item->title }}
    </td>

    {{-- Deskripsi --}}
    <td class="px-4 py-3 text-gray-600 max-w-xs">
        {!! \Illuminate\Support\Str::limit(strip_tags($item->description), 80) !!}
    </td>

    {{-- Tanggal --}}
    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
        {{ \Carbon\Carbon::parse($item->pub_date)->format('d M Y') }}
    </td>

    {{-- Kategori --}}
    <td class="px-4 py-3">
        <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700">
            {{ $item->category->name ?? '-' }}
        </span>
    </td>

    {{-- Waktu baca --}}
    <td class="px-4 py-3 text-gray-700">
        {{ $item->waktu_baca ?? '-' }}
    </td>

    {{-- Aksi --}}
    <td class="px-4 py-3 text-center space-x-1 whitespace-nowrap">
        <a href="{{ route('ourblogs.show', $item->id) }}"
            class="px-3 py-1 text-xs bg-green-50 text-green-700 border border-green-200 rounded hover:bg-green-100">
            Detail
        </a>

        <button
            onclick="openEditModal(this)"
            data-item='@json($item)'
            class="px-3 py-1 text-xs bg-blue-50 text-blue-700 border border-blue-200 rounded hover:bg-blue-100">
            Edit
        </button>
    </td>

</tr>
@endforeach
</tbody>

            </table>
        </div>
    </div>

    <form id="bulkDeleteForm" method="POST" action="{{ route('ourblogs.bulkDelete') }}">
        @csrf
    </form>

    <!-- Modal Tambah -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Tambah Blog</h2>
            <form action="{{ route('ourblogs.store') }}" method="POST" enctype="multipart/form-data" id="addBlogForm">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Judul</label>
                        <input type="text" name="title" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tanggal Publikasi</label>
                        <input type="date" name="pub_date" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Kategori</label>
                        <select name="category_id" required class="w-full border rounded p-2 text-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Waktu Baca</label>
                        <input type="text" name="waktu_baca" placeholder="contoh: 5 menit" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Deskripsi</label>
                        <textarea name="description" id="editorAddDescription" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                    </div>
                </div>

                <!-- Enhanced Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Upload Gambar</label>
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
                                <p class="text-sm text-gray-500">PNG, JPG, atau GIF (MAX. 2MB)</p>
                            </div>
                        </div>

                        <div id="addPreview" class="mt-4"></div>
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

    <!-- Modal Edit -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Edit Blog</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Judul</label>
                        <input type="text" name="title" id="editTitle" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tanggal Publikasi</label>
                        <input type="date" name="pub_date" id="editPubDate" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Kategori</label>
                        <select name="category_id" id="editCategory" required class="w-full border rounded p-2 text-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Waktu Baca</label>
                        <input type="text" name="waktu_baca" id="editWaktuBaca" placeholder="contoh: 5 menit" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div class="col-span-2">
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
                                <p class="text-sm text-gray-500">PNG, JPG, atau GIF (MAX. 2MB) - Opsional</p>
                            </div>
                        </div>

                        <div id="editPreview" class="mt-4"></div>
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

    <script>
        // Global variables for CKEditor instances
        let addDescriptionEditor = null;
        let editDescriptionEditor = null;

        // SweetAlert helper functions - UPDATED TO MATCH YOUR STYLE
        function showAlert(type, message) {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Berhasil!' : 'Error!',
                text: message,
                timer: 3000,
                showConfirmButton: false
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

        // Initialize CKEditor when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            setupDragAndDrop();

            // Enhanced configuration for CKEditor with more features
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

            // Handle Add Form Submission
            const addForm = document.getElementById('addBlogForm');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleFormSubmission('add');
                });
            }

            // Handle Edit Form Submission
            const editForm = document.getElementById('editForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleFormSubmission('edit');
                });
            }

            // Show flash messages using SweetAlert - UPDATED
            @if(session('success'))
                showAlert('success', "{{ session('success') }}");
            @endif

            @if(session('error'))
                showAlert('error', "{{ session('error') }}");
            @endif

            @if($errors->any()))
                let errorMessages = [];
                @foreach($errors->all() as $error)
                    errorMessages.push('{{ $error }}');
                @endforeach
                showAlert('error', errorMessages.join('\n'));
            @endif
        });

        // Handle form submission with CKEditor data
        function handleFormSubmission(type) {
            const isAdd = type === 'add';
            const form = document.getElementById(isAdd ? 'addBlogForm' : 'editForm');
            const descriptionEditor = isAdd ? addDescriptionEditor : editDescriptionEditor;

            // Show loading
            showLoadingAlert('Menyimpan data...');

            try {
                // Create FormData
                const formData = new FormData(form);

                // Add CKEditor data for description
                if (descriptionEditor) {
                    formData.set('description', descriptionEditor.getData());
                }

                // Validate required fields
                if (descriptionEditor && !descriptionEditor.getData().trim()) {
                    showAlert('error', 'Deskripsi harus diisi');
                    return;
                }

                // Submit using fetch
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    const contentType = response.headers.get('content-type');

                    if (contentType && contentType.includes('application/json')) {
                        return response.json().then(data => ({
                            success: response.ok,
                            data: data,
                            status: response.status
                        }));
                    } else {
                        if (response.ok || response.redirected) {
                            return {
                                success: true,
                                data: { message: isAdd ? 'Blog berhasil ditambahkan!' : 'Blog berhasil diperbarui!' },
                                status: response.status
                            };
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                    }
                })
                .then(result => {
                    if (result.success) {
                        if (isAdd) {
                            closeAddModal();
                        } else {
                            closeEditModal();
                        }
                        showAlert('success', result.data.message || (isAdd ? 'Blog berhasil ditambahkan!' : 'Blog berhasil diperbarui!'));
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        if (result.data.errors) {
                            let errorMessage = '';
                            Object.values(result.data.errors).forEach(errorArray => {
                                errorArray.forEach(error => {
                                    errorMessage += error + '<br>';
                                });
                            });
                            showAlert('error', errorMessage);
                        } else {
                            showAlert('error', result.data.message || 'Gagal menyimpan data blog!');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
                });

            } catch (error) {
                console.error('Form preparation error:', error);
                showAlert('error', 'Terjadi kesalahan saat memproses data');
            }
        }

        function openAddModal() {
            // Reset form
            document.getElementById('addBlogForm').reset();
            document.getElementById('addPreview').innerHTML = '';
            document.getElementById('addUploadArea').style.display = 'block';

            // Reset CKEditor content
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

            form.action = `/ourblogs/${data.id}`;
            document.getElementById('editTitle').value = data.title;
            document.getElementById('editPubDate').value = data.pub_date.split(' ')[0];
            document.getElementById('editCategory').value = data.category_id;
            document.getElementById('editWaktuBaca').value = data.waktu_baca || '';

            // Set description content in CKEditor
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

        function updateBulkDeleteButton() {
            const checked = document.querySelectorAll('.rowCheckbox:checked');
            const btn = document.getElementById('bulkDeleteBtn');
            btn.disabled = checked.length === 0;
            btn.textContent = checked.length > 0 ? `Hapus Terpilih (${checked.length})` : 'Hapus Terpilih';
        }

        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.rowCheckbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkDeleteButton();
        });

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const uploadAreaId = previewId === 'addPreview' ? 'addUploadArea' : 'editUploadArea';
            const uploadArea = document.getElementById(uploadAreaId);

            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validate file type
                if (!file.type.match('image.*')) {
                    showAlert('error', 'File harus berupa gambar (PNG/JPG)');
                    input.value = '';
                    return;
                }

                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    showAlert('error', 'Ukuran file maksimal 2MB');
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

        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#blogTable tr");

            rows.forEach(row => {
                let title = row.cells[2]?.textContent?.toLowerCase() || '';
                let category = row.cells[5]?.textContent?.toLowerCase() || '';

                const shouldShow = title.includes(input) || category.includes(input);
                row.style.display = shouldShow ? "" : "none";
            });
        }

        function bulkDelete() {
            const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                showAlert('warning', 'Tidak ada yang dipilih');
                return;
            }

            Swal.fire({
                title: `Hapus ${ids.length} blog terpilih?`,
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
    </script>
@endsection
