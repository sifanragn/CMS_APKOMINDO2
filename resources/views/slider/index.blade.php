@extends('layouts.app')

@section('content')
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Slider</h1>
            <div class="flex gap-2">
                <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Tambah Slider
                </button>
            </div>
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
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Gambar</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Judul</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Subtitle</th>
                <th class="px-4 py-3 text-center font-semibold text-gray-700">Tampilkan</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-700">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($sliders as $item)
                <tr class="border-b hover:bg-gray-50 transition">

                    {{-- IMAGE --}}
                    <td class="px-4 py-3">
                        @if ($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}"
                                 class="w-16 h-16 rounded-lg object-cover border shadow-sm">
                        @else
                            <span class="text-gray-400 text-xs">Tidak ada gambar</span>
                        @endif
                    </td>

                    {{-- TITLE --}}
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $item->title ?? '-' }}
                    </td>

                    {{-- SUBTITLE --}}
                    <td class="px-4 py-3">
                        <div class="max-w-xs truncate" title="{{ strip_tags($item->subtitle) }}">
                            {!! Str::limit($item->subtitle, 50) !!}
                        </div>
                    </td>

                    {{-- SWITCH --}}
                    <td class="px-4 py-3 text-center">
                        <label class="iphone-switch">
                            <input type="checkbox"
                                   onchange="toggleSliderDisplay({{ $item->id }}, this)"
                                   {{ $item->display_on_home ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </td>

                    {{-- ACTION --}}
                    <td class="px-4 py-3 space-x-1">
                        <button onclick="openEditModal(this)" data-slider='@json($item)'
                            class="px-3 py-1 text-xs bg-blue-50 text-blue-600 border border-blue-200 rounded hover:bg-blue-100">
                            Edit
                        </button>

                        <button onclick="confirmDelete({{ $item->id }})"
                            class="px-3 py-1 text-xs bg-red-50 text-red-600 border border-red-200 rounded hover:bg-red-100">
                            Hapus
                        </button>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-500">
                        Belum ada data slider
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

    </div>

    <!-- Form tersembunyi untuk delete -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Modal Tambah -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Tambah Slider</h2>
            <form action="{{ route('slider.store') }}" method="POST" enctype="multipart/form-data" id="addForm">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="display_on_home" class="mr-2" value="1"
                                {{ old('display_on_home') ? 'checked' : '' }} />
                            <span class="font-medium">Tampilkan di Homepage</span>
                        </label>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Judul</label>
                        <input type="text" name="title" class="w-full border rounded p-2 text-sm"
                            value="{{ old('title') }}" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">YouTube ID</label>
                        <input type="text" name="youtube_id" class="w-full border rounded p-2 text-sm"
                            placeholder="Contoh: dQw4w9WgXcQ" value="{{ old('youtube_id') }}" />
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Subtitle</label>
                        <textarea name="subtitle" id="editorAddSubtitle" rows="4" class="w-full border rounded p-2 text-sm">{{ old('subtitle') }}</textarea>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Button Text</label>
                        <input type="text" name="button_text" class="w-full border rounded p-2 text-sm"
                            placeholder="Contoh: Selengkapnya" value="{{ old('button_text') }}" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">URL Link</label>
                        <input type="url" name="url_link" class="w-full border rounded p-2 text-sm"
                            placeholder="https://example.com" value="{{ old('url_link') }}" />
                    </div>
                </div>

                <!-- Enhanced Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Upload Gambar</label>
                    <div class="relative">
                        <input type="file" name="image" id="addImageInput" onchange="previewImage(this, 'addPreview')"
                            accept="image/png,image/jpg,image/jpeg,image/webp" class="hidden" />

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
                                <p class="text-sm text-gray-500">PNG, JPG, JPEG, atau WEBP (MAX. 2MB)</p>
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
            <h2 class="text-lg font-semibold">Edit Slider</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="display_on_home" id="editDisplayOnHome" class="mr-2"
                                value="1" />
                            <span class="font-medium">Tampilkan di Homepage</span>
                        </label>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Judul</label>
                        <input type="text" name="title" id="editTitle" class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">YouTube ID</label>
                        <input type="text" name="youtube_id" id="editYoutubeId"
                            class="w-full border rounded p-2 text-sm" placeholder="Contoh: dQw4w9WgXcQ" />
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Subtitle</label>
                        <textarea name="subtitle" id="editorEditSubtitle" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Button Text</label>
                        <input type="text" name="button_text" id="editButtonText"
                            class="w-full border rounded p-2 text-sm" placeholder="Contoh: Selengkapnya" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">URL Link</label>
                        <input type="url" name="url_link" id="editUrlLink" class="w-full border rounded p-2 text-sm"
                            placeholder="https://example.com" />
                    </div>
                </div>

                <!-- Enhanced Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Ganti Gambar</label>
                    <div class="relative">
                        <input type="file" name="image" id="editImageInput"
                            onchange="previewImage(this, 'editPreview')"
                            accept="image/png,image/jpg,image/jpeg,image/webp" class="hidden" />

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
                                <p class="text-sm text-gray-500">PNG, JPG, JPEG, atau WEBP (MAX. 2MB)</p>
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
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

    <script>
        // Global variables for CKEditor instances
        let addSubtitleEditor = null;
        let editSubtitleEditor = null;

        // SweetAlert helper functions
        function showAlert(type, message) {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Berhasil!' : type === 'error' ? 'Error!' : 'Info',
                text: message,
                timer: 3000,
                showConfirmButton: false
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
                .create(document.querySelector('#editorAddSubtitle'), editorConfig)
                .then(editor => {
                    addSubtitleEditor = editor;
                    console.log('Add Subtitle Editor initialized successfully');

                    // Sync with form on change
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#editorAddSubtitle').value = editor.getData();
                    });
                })
                .catch(error => {
                    console.error('Error initializing add subtitle editor:', error);
                });

            // Initialize CKEditor for Edit Modal
            ClassicEditor
                .create(document.querySelector('#editorEditSubtitle'), editorConfig)
                .then(editor => {
                    editSubtitleEditor = editor;
                    console.log('Edit Subtitle Editor initialized successfully');

                    // Sync with form on change
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#editorEditSubtitle').value = editor.getData();
                    });
                })
                .catch(error => {
                    console.error('Error initializing edit subtitle editor:', error);
                });

            // Handle flash messages
            @if(session('success'))
                showAlert('success', "{{ session('success') }}");
            @endif

            @if(session('error'))
                showAlert('error', "{{ session('error') }}");
            @endif

            @if($errors->any())
                let errorMessages = [];
                @foreach($errors->all() as $error)
                    errorMessages.push('{{ addslashes($error) }}');
                @endforeach
                showAlert('error', errorMessages.join('\n'));
            @endif

            // Form submission handlers
            const addForm = document.getElementById('addForm');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleFormSubmission('add');
                });
            }

            const editForm = document.getElementById('editForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleFormSubmission('edit');
                });
            }
        });

        // Handle form submission with proper AJAX response handling
        function handleFormSubmission(type) {
            const isAdd = type === 'add';
            const form = document.getElementById(isAdd ? 'addForm' : 'editForm');
            const subtitleEditor = isAdd ? addSubtitleEditor : editSubtitleEditor;
            const submitBtn = document.getElementById(isAdd ? 'addSubmitBtn' : 'editSubmitBtn');

            if (!form || !submitBtn) {
                console.error('Form or submit button not found');
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Menyimpan data...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Menyimpan...';

            try {
                // Create FormData
                const formData = new FormData(form);

                // Add CKEditor data for subtitle
                if (subtitleEditor) {
                    formData.set('subtitle', subtitleEditor.getData());
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
                    return response.json().then(data => {
                        return {
                            ok: response.ok,
                            status: response.status,
                            data: data
                        };
                    });
                })
                .then(result => {
                    Swal.close();

                    if (result.ok && result.data.success) {
                        // Success
                        if (isAdd) {
                            closeAddModal();
                        } else {
                            closeEditModal();
                        }

                        showAlert('success', result.data.message);

                        // Reload page after delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Error
                        let errorMessage = result.data.message || 'Terjadi kesalahan saat menyimpan data';

                        // Handle validation errors
                        if (result.data.errors) {
                            const errors = Object.values(result.data.errors).flat();
                            errorMessage = errors.join('\n');
                        }

                        showAlert('error', errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.close();
                    showAlert('error', 'Terjadi kesalahan saat menyimpan data');
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Simpan';
                });

            } catch (error) {
                console.error('Form preparation error:', error);
                Swal.close();
                showAlert('error', 'Terjadi kesalahan saat memproses data');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan';
            }
        }

        function openAddModal() {
            // Reset form
            document.getElementById('addForm').reset();
            document.getElementById('addPreview').innerHTML = '';
            document.getElementById('addUploadArea').style.display = 'block';

            // Reset CKEditor content
            if (addSubtitleEditor) {
                addSubtitleEditor.setData('');
            }

            // Show modal
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(button) {
            const slider = JSON.parse(button.getAttribute('data-slider'));

            // Set form action
            const form = document.getElementById('editForm');
            form.action = `/slider/${slider.id}`;

            // Populate form fields
            document.getElementById('editTitle').value = slider.title || '';
            document.getElementById('editYoutubeId').value = slider.youtube_id || '';
            document.getElementById('editButtonText').value = slider.button_text || '';
            document.getElementById('editUrlLink').value = slider.url_link || '';
            document.getElementById('editDisplayOnHome').checked = slider.display_on_home || false;

            // Set subtitle content in CKEditor
            if (editSubtitleEditor) {
                editSubtitleEditor.setData(slider.subtitle || '');
            }

            // Handle image preview
            const editPreview = document.getElementById('editPreview');
            const editUploadArea = document.getElementById('editUploadArea');

            if (slider.image) {
                editPreview.innerHTML = `
                    <div class="relative inline-block">
                        <img src="/storage/${slider.image}" class="h-32 w-32 rounded-lg shadow-md object-cover border" alt="Current image">
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

            // Show modal
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

        function setupDragAndDrop() {
            setupDragAndDropForElement('addUploadArea', 'addImageInput');
            setupDragAndDropForElement('editUploadArea', 'editImageInput');
        }

        function setupDragAndDropForElement(uploadAreaId, inputId) {
            const uploadArea = document.getElementById(uploadAreaId);
            const fileInput = document.getElementById(inputId);

            if (!uploadArea || !fileInput) return;

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
            let rows = document.querySelectorAll("#sliderTable tr");

            rows.forEach(row => {
                let title = row.cells[1]?.textContent?.toLowerCase() || '';
                const shouldShow = title.includes(input);
                row.style.display = shouldShow ? "" : "none";
            });
        }

        // Fixed confirmDelete function
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Slider?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus data...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Create form data
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'DELETE');

                    // Submit delete request
                    fetch(`/slider/${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        return response.json().then(data => {
                            return {
                                ok: response.ok,
                                status: response.status,
                                data: data
                            };
                        });
                    })
                    .then(result => {
                        Swal.close();

                        if (result.ok && result.data.success) {
                            // Success
                            showAlert('success', result.data.message);

                            // Reload page after delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            // Error
                            showAlert('error', result.data.message || 'Terjadi kesalahan saat menghapus data');
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        Swal.close();
                        showAlert('error', 'Terjadi kesalahan saat menghapus data');
                    });
                }
            });
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
                    showAlert('error', 'File harus berupa gambar (PNG/JPG/JPEG/WEBP)');
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

        function toggleSliderDisplay(id, checkbox) {
    fetch(`/slider/toggle/${id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            display_on_home: checkbox.checked ? 1 : 0
        })
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: data.message,
            showConfirmButton: false,
            timer: 2000
        });
    })
    .catch(() => {
        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Tidak dapat mengubah status slider."
        });
        checkbox.checked = !checkbox.checked; // undo
    });
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

.iphone-switch .slider {
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

.iphone-switch .slider:before {
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

.iphone-switch input:checked + .slider {
    background-color: #59B5F7;
}

.iphone-switch input:checked + .slider:before {
    transform: translateX(22px);
}
</style>
@endsection
