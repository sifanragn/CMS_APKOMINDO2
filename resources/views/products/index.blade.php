@extends('layouts.app')

@section('content')
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Produk Digital</h1>
            <div class="flex gap-2">
                <button id="bulkDeleteBtn" onclick="bulkDelete()"
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled>
                    Hapus Terpilih
                </button>
                <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Tambah Produk
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
                        <th class="px-4 py-2 border">Harga</th>
                        <th class="px-4 py-2 border">Diskon</th>
                        <th class="px-4 py-2 border">Disusun</th>
                        <th class="px-4 py-2 border">No. Telepon</th>
                        <th class="px-4 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody id="productTable">
                    @foreach ($products as $item)
                        <tr>
                            <td class="px-4 py-2 border">
                                <input type="checkbox" name="product_ids[]" value="{{ $item->id }}" class="rowCheckbox"
                                    onchange="updateBulkDeleteButton()">
                            </td>
                            <td class="px-4 py-2 border">
                                @if ($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                        class="w-24 h-24 object-cover object-center rounded shadow-md aspect-square">
                                @endif
                            </td>
                            <td class="px-4 py-2 border">{{ $item->title }}</td>
                            <td class="px-4 py-2 border">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                    {{ $item->category ? $item->category->name : 'Tidak ada kategori' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 border">
                                @if ($item->discount)
                                    <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                        {{ $item->discount }}%
                                    </span>
                                @else
                                    <span class="text-gray-400">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 border">{{ $item->disusun }}</td>
                            <td class="px-4 py-2 border">
                                @if ($item->notlp)
                                    <a href="tel:{{ $item->notlp }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $item->notlp }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 border space-x-1">
                                <a href="{{ route('products.show', $item->id) }}"
                                    class="text-green-600 hover:text-green-800 px-2 py-1 text-xs border border-green-300 rounded hover:bg-green-50 inline-block">Detail</a>
                                <button onclick="openEditModal(this)" data-product='@json($item)'
                                    class="text-blue-600 hover:text-blue-800 px-2 py-1 text-xs border border-blue-300 rounded hover:bg-blue-50">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <form id="bulkDeleteForm" method="POST" action="{{ route('products.bulkDelete') }}">
        @csrf
    </form>

    <!-- Modal Tambah -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-4xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Tambah Produk</h2>
            <form id="addForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_store_id" required class="w-full border rounded p-2 text-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Harga <span class="text-red-500">*</span></label>
                        <input type="number" name="price" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Diskon (%)</label>
                        <input type="number" name="discount" class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Disusun Oleh <span class="text-red-500">*</span></label>
                        <input type="text" name="disusun" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Jumlah Modul <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah_modul" required min="1"
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Bahasa <span class="text-red-500">*</span></label>
                        <input type="text" name="bahasa" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Nomor Telepon</label>
                        <input type="text" name="notlp" placeholder="08123456789"
                            class="w-full border rounded p-2 text-sm" pattern="[0-9]*" inputmode="numeric"
                            oninput="validatePhoneInput(this)" maxlength="20" />
                        <small class="text-gray-500">Maksimal 20 digit angka</small>
                    </div>
                    <div class="col-span-3">
                        <label class="block mb-1 font-medium">Deskripsi</label>
                        <textarea name="description" id="editorAddDescription" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                    </div>
                </div>

                <!-- Enhanced Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Upload Gambar <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="file" name="image" id="addImageInput"
                            onchange="previewImage(this, 'addPreview')" accept="image/png,image/jpg,image/jpeg"
                            class="hidden" required />

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
                                <p class="text-sm text-gray-500">PNG, JPG, atau GIF (MAX. 2MB) - Wajib</p>
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
        <div class="bg-white rounded-lg w-full max-w-4xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Edit Produk</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="editTitle" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_store_id" id="editCategory" required
                            class="w-full border rounded p-2 text-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Harga <span class="text-red-500">*</span></label>
                        <input type="number" name="price" id="editPrice" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Diskon (%)</label>
                        <input type="number" name="discount" id="editDiscount"
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Disusun Oleh <span class="text-red-500">*</span></label>
                        <input type="text" name="disusun" id="editDisusun" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Jumlah Modul <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah_modul" id="editJumlahModul" required min="1"
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Bahasa <span class="text-red-500">*</span></label>
                        <input type="text" name="bahasa" id="editBahasa" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Nomor Telepon</label>
                        <input type="text" name="notlp" id="editNotlp" placeholder="08123456789"
                            class="w-full border rounded p-2 text-sm" pattern="[0-9]*" inputmode="numeric"
                            oninput="validatePhoneInput(this)" maxlength="20" />
                        <small class="text-gray-500">Maksimal 20 digit angka</small>
                    </div>
                    <div class="col-span-3">
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

            // Debug: Log categories data
            console.log('Categories loaded:', @json($categories));
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
                    options: [{
                            model: 'paragraph',
                            title: 'Paragraph',
                            class: 'ck-heading_paragraph'
                        },
                        {
                            model: 'heading1',
                            view: 'h1',
                            title: 'Heading 1',
                            class: 'ck-heading_heading1'
                        },
                        {
                            model: 'heading2',
                            view: 'h2',
                            title: 'Heading 2',
                            class: 'ck-heading_heading2'
                        },
                        {
                            model: 'heading3',
                            view: 'h3',
                            title: 'Heading 3',
                            class: 'ck-heading_heading3'
                        }
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
                    options: ['left', 'right', 'center', 'justify']
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

        // Enhanced phone number validation function
        function validatePhoneInput(input) {
            // Remove all non-numeric characters
            let value = input.value.replace(/[^0-9]/g, '');

            // Limit to 20 characters as per database schema
            if (value.length > 20) {
                value = value.substring(0, 20);
            }

            // Update input value
            input.value = value;
        }

        // Show flash messages using SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                showSuccessAlert("{{ session('success') }}");
            @endif

            @if (session('error'))
                showErrorAlert("{{ session('error') }}");
            @endif

            @if ($errors->any())
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += '{{ $error }}\n';
                @endforeach
                showErrorAlert(errorMessages);
            @endif

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

            // Setup phone input validation for both modals
            const addPhoneInput = document.querySelector('#addModal input[name="notlp"]');
            const editPhoneInput = document.querySelector('#editNotlp');

            [addPhoneInput, editPhoneInput].forEach(input => {
                if (input) {
                    // Handle paste events
                    input.addEventListener('paste', function(e) {
                        e.preventDefault();
                        let paste = (e.clipboardData || window.clipboardData).getData('text');
                        let numbersOnly = paste.replace(/[^0-9]/g, '');
                        if (numbersOnly.length > 20) {
                            numbersOnly = numbersOnly.substring(0, 20);
                        }
                        this.value = numbersOnly;
                    });

                    // Handle keypress to prevent non-numeric input
                    input.addEventListener('keypress', function(e) {
                        // Allow only numeric keys, backspace, delete, tab, escape, enter
                        if (!/[0-9]/.test(e.key) &&
                            !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key)) {
                            e.preventDefault();
                        }
                    });
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
                html: message.replace(/\n/g, '<br>'),
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

        // Search functionality
        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#productTable tr");

            rows.forEach(row => {
                let title = row.cells[2]?.textContent?.toLowerCase() || '';
                const shouldShow = title.includes(input);
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
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal satu produk untuk dihapus!',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: `Hapus ${ids.length} produk terpilih?`,
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
                    showLoadingAlert('Menghapus produk...');

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
            const data = JSON.parse(button.getAttribute('data-product'));
            const form = document.getElementById('editForm');

            // Debug: Log the product data and categories
            console.log('Product data:', data);
            console.log('Category ID from product:', data.category_store_id);

            form.action = `/products/${data.id}`;
            document.getElementById('editTitle').value = data.title || '';
            document.getElementById('editPrice').value = data.price || '';
            document.getElementById('editDiscount').value = data.discount || '';
            document.getElementById('editDisusun').value = data.disusun || '';
            document.getElementById('editJumlahModul').value = data.jumlah_modul || '';
            document.getElementById('editBahasa').value = data.bahasa || '';
            document.getElementById('editNotlp').value = data.notlp || '';

            // Set category dengan debugging
            const categorySelect = document.getElementById('editCategory');
            console.log('Available options:', categorySelect.options);
            categorySelect.value = data.category_store_id || '';
            console.log('Set category value to:', categorySelect.value);

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
                    uploadArea.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                uploadArea.style.display = 'block';
            }
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

        // Enhanced Form Submissions with SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Add Form
            document.getElementById('addForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = document.getElementById('addSubmitBtn');
                const originalText = submitBtn.textContent;

                // Validate required fields
                if (!formData.get('title') || !formData.get('price') || !formData.get('disusun') ||
                    !formData.get('jumlah_modul') || !formData.get('bahasa') || !formData.get('image') ||
                    !formData.get('category_store_id')) {
                    showErrorAlert('Harap isi semua field yang wajib diisi!');
                    return;
                }

                // Validate phone number length if provided
                const phoneNumber = formData.get('notlp');
                if (phoneNumber && phoneNumber.length > 20) {
                    showErrorAlert('Nomor telepon maksimal 20 digit!');
                    return;
                }

                submitBtn.textContent = 'Menyimpan...';
                submitBtn.disabled = true;

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content') || '{{ csrf_token() }}'
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
                                    data: {
                                        message: 'Produk berhasil ditambahkan!'
                                    },
                                    status: response.status
                                };
                            } else {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                        }
                    })
                    .then(result => {
                        if (result.success) {
                            closeAddModal();
                            showSuccessAlert(result.data.message || 'Produk berhasil ditambahkan!');
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
                                showErrorAlert(errorMessage);
                            } else {
                                showErrorAlert(result.data.message || 'Gagal menambahkan produk!');
                            }
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
                if (!formData.get('title') || !formData.get('price') || !formData.get('disusun') ||
                    !formData.get('jumlah_modul') || !formData.get('bahasa') || !formData.get(
                        'category_store_id')) {
                    showErrorAlert('Harap isi semua field yang wajib diisi!');
                    return;
                }

                // Validate phone number length if provided
                const phoneNumber = formData.get('notlp');
                if (phoneNumber && phoneNumber.length > 20) {
                    showErrorAlert('Nomor telepon maksimal 20 digit!');
                    return;
                }

                submitBtn.textContent = 'Menyimpan...';
                submitBtn.disabled = true;

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content') || '{{ csrf_token() }}'
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
                                    data: {
                                        message: 'Produk berhasil diperbarui!'
                                    },
                                    status: response.status
                                };
                            } else {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                        }
                    })
                    .then(result => {
                        if (result.success) {
                            closeEditModal();
                            showSuccessAlert(result.data.message || 'Produk berhasil diperbarui!');
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
                                showErrorAlert(errorMessage);
                            } else {
                                showErrorAlert(result.data.message || 'Gagal memperbarui produk!');
                            }
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
