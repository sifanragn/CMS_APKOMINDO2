@extends('layouts.app')

@section('content')
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Event</h1>
            <div class="flex gap-2">
                <button id="bulkDeleteBtn" onclick="bulkDelete()"
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled>
                    Hapus Terpilih
                </button>
                <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Tambah Event
                </button>
            </div>
        </div>

        <!-- Search Input -->
        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Cari berdasarkan judul..."
                class="border px-3 py-2 w-full rounded text-sm" onkeyup="searchTable()">
        </div>

        {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm rounded-xl overflow-hidden">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Judul</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Gambar</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Tanggal</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Lokasi</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Pembicara</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>

            <tbody id="agendaTable">
                @foreach ($agendas as $index => $item)
                    <tr class="border-b hover:bg-gray-50 transition">

                        <td class="px-4 py-3">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $item->title }}
                        </td>
                        
                        <td class="px-4 py-3">
                        @if ($item->image)
                            <img
                                src="{{ asset('storage/' . $item->image) }}"
                                class="w-16 h-16 rounded-lg object-cover border shadow-sm"
                                alt="{{ $item->title }}"
                            >
                        @else
                            <span class="text-gray-400 text-xs">Tidak ada gambar</span>
                        @endif
                    </td>


                        <td class="px-4 py-3 text-gray-700">
                            {{ \Carbon\Carbon::parse($item->start_datetime)->format('d M Y H:i') }}
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            {{ $item->location }}
                        </td>

                        <td class="px-4 py-3">
                            @if ($item->speakers && $item->speakers->count())
                                @foreach ($item->speakers as $speaker)
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs mr-1">
                                        {{ $speaker->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-gray-400 text-xs">Tidak ada</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $item->status === 'Open'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700' }}">
                                {{ $item->status }}
                            </span>
                        </td>

                        <td class="px-4 py-3 space-x-1">
                            <a href="{{ route('agenda.show', $item->id) }}"
                                class="px-3 py-1 text-xs bg-gray-50 text-gray-700 border border-gray-300 rounded hover:bg-gray-100">
                                Detail
                            </a>

                            <button onclick="openEditModal({{ $item->id }})"
                                class="px-3 py-1 text-xs bg-blue-50 text-blue-600 border border-blue-200 rounded hover:bg-blue-100">
                                Edit
                            </button>
                        </td>
                    </tr>
                @endforeach

                @if ($agendas->count() == 0)
                    <tr>
                        <td colspan="7" class="py-10 text-center text-gray-500">
                            Tidak ada agenda tersedia
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        </div>
    </div>

    <script>
        window.agendas = @json($agendas);
    </script>

    <form id="bulkDeleteForm" method="POST" action="{{ route('agenda.bulkDelete') }}">
        @csrf
    </form>

    <!-- Modal Tambah -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Tambah Agenda</h2>
            <form action="{{ route('agenda.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Judul</label>
                        <input type="text" name="title" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Penyelenggara</label>
                        <input type="text" name="event_organizer" class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tanggal Mulai</label>
                        <input type="datetime-local" name="start_datetime" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tanggal Selesai</label>
                        <input type="datetime-local" name="end_datetime" class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Lokasi</label>
                        <input type="text" name="location" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Deskripsi</label>
                        <textarea name="description" id="editorAddDescription" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tautan Registrasi</label>
                        <input type="url" name="register_link" class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tautan YouTube</label>
                        <input type="url" name="youtube_link" class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tipe</label>
                        <input type="text" name="type" class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Status</label>
                        <select name="status" class="w-full border rounded p-2 text-sm">
                            <option value="Open">Open</option>
                            <option value="Soldout">Soldout</option>
                        </select>
                    </div>
                </div>

                <!-- Enhanced Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Upload Gambar</label>
                    <div class="relative">
                        <input type="file" name="image" id="addImageInput" onchange="previewImage(this, 'addPreview')"
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

                <div class="mt-4">
                    <label class="block mb-1 font-medium">Pembicara</label>
                    <select id="addSpeakers" name="speaker_ids[]" multiple="multiple" class="w-full">
                        @foreach ($speakers as $speaker)
                            <option value="{{ $speaker->id }}">{{ $speaker->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-gray-500">Pilih satu atau lebih pembicara</small>
                </div>

                {{-- FOTO TAMBAHAN --}}
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-medium">Foto Tambahan</label>
                        <button type="button"
                            onclick="addExtraImageAdd()"
                            class="text-sm bg-blue-500 text-white px-3 py-1 rounded">
                            + Tambah Foto
                        </button>
                    </div>

                    <div id="extraImagesWrapperAdd" class="space-y-3"></div>
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
            <h2 class="text-lg font-semibold">Edit Agenda</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editId">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Judul</label>
                        <input type="text" name="title" id="editTitle" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Penyelenggara</label>
                        <input type="text" name="event_organizer" id="editEventOrganizer"
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tanggal Mulai</label>
                        <input type="datetime-local" name="start_datetime" id="editStartDatetime" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tanggal Selesai</label>
                        <input type="datetime-local" name="end_datetime" id="editEndDatetime"
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Lokasi</label>
                        <input type="text" name="location" id="editLocation" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1 font-medium">Deskripsi</label>
                        <textarea name="description" id="editorEditDescription" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tautan Registrasi</label>
                        <input type="url" name="register_link" id="editRegisterLink"
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tautan YouTube</label>
                        <input type="url" name="youtube_link" id="editYoutubeLink"
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Tipe</label>
                        <input type="text" name="type" id="editType" class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Status</label>
                        <select name="status" id="editStatus" class="w-full border rounded p-2 text-sm">
                            <option value="Open">Open</option>
                            <option value="Soldout">Soldout</option>
                        </select>
                    </div>
                </div>

                <!-- Enhanced Image Upload Section -->
                <div class="mt-4">
                    <label class="block mb-2 font-medium">Ganti Gambar</label>
                    <div class="relative">
                        <input type="file" name="image" id="editImageInput" onchange="previewImage(this, 'editPreview')"
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

                <div class="mt-4">
                    <label class="block mb-1 font-medium">Pembicara</label>
                    <select id="editSpeakers" name="speaker_ids[]" multiple="multiple" class="w-full">
                        @foreach ($speakers as $speaker)
                            <option value="{{ $speaker->id }}">{{ $speaker->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-gray-500">Pilih satu atau lebih pembicara</small>
                </div>
                {{-- FOTO TAMBAHAN (EDIT) --}}
<div class="mt-6">
    <div class="flex justify-between items-center mb-2">
        <label class="font-medium">Foto Tambahan</label>
        <button type="button"
            onclick="addExtraImageEdit()"
            class="text-sm bg-blue-500 text-white px-3 py-1 rounded">
            + Tambah Foto
        </button>
    </div>

    {{-- FOTO LAMA (DALAM BENTUK FORM) --}}
    <div id="existingExtraImages" class="space-y-4"></div>

    {{-- FOTO BARU --}}
    <div id="extraImagesWrapperEdit" class="space-y-4"></div>
</div>


                <div id="deletedExtraWrapper"></div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                    <button type="submit"
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

    window.extraEditors = {};

        // Global variables for CKEditor instances
        let addDescriptionEditor = null;
        let editDescriptionEditor = null;

        // Initialize CKEditor when the page loads
        $(document).ready(function() {
            initializeSelect2();
            setupDragAndDrop();
            initializeCKEditor();
        });

        function setupExtraDropzone(id) {
    const zone = document.getElementById(id);
    const input = zone.querySelector('input');
    const img = zone.querySelector('img');

    zone.addEventListener('click', () => input.click());

    input.addEventListener('change', () => {
        if (!input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    });

    ['dragenter', 'dragover'].forEach(evt =>
        zone.addEventListener(evt, e => {
            e.preventDefault();
            zone.classList.add('bg-blue-50');
        })
    );

    ['dragleave', 'drop'].forEach(evt =>
        zone.addEventListener(evt, e => {
            e.preventDefault();
            zone.classList.remove('bg-blue-50');
        })
    );

    zone.addEventListener('drop', e => {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
    });
}

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

        function initializeSelect2() {
            // Destroy existing instances
            if ($('#addSpeakers').hasClass("select2-hidden-accessible")) {
                $('#addSpeakers').select2('destroy');
            }
            if ($('#editSpeakers').hasClass("select2-hidden-accessible")) {
                $('#editSpeakers').select2('destroy');
            }

            // Initialize fresh instances
            $('#addSpeakers').select2({
                placeholder: "Pilih pembicara...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addModal')
            });

            $('#editSpeakers').select2({
                placeholder: "Pilih pembicara...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#editModal')
            });
        }

        // Setup drag and drop functionality
        function setupDragAndDrop() {
            setupDragAndDropForElement('addUploadArea', 'addImageInput');
            setupDragAndDropForElement('editUploadArea', 'editImageInput');
        }

        function bindClickUpload(areaId, inputId, previewId) {
            const area = document.getElementById(areaId);
            const input = document.getElementById(inputId);

            area.addEventListener('click', () => {
                input.click();
            });

            input.addEventListener('change', () => {
                previewImage(input, previewId);
            });
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

        // Fungsi search untuk agenda
        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#agendaTable tr");

            rows.forEach(row => {
                let title = row.cells[2]?.textContent?.toLowerCase() || ''; // Kolom judul (indeks 2)
                let organizer = row.cells[1]?.textContent?.toLowerCase() || ''; // Kolom penyelenggara (indeks 1)

                const shouldShow = title.includes(input) || organizer.includes(input);
                row.style.display = shouldShow ? "" : "none";
            });
        }

        // Debounce untuk meningkatkan performa saat mengetik
        let searchTimer;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(searchTable, 300);
        });

        function openAddModal() {
            // Reset form
            document.querySelector('#addModal form').reset();
            document.getElementById('addPreview').innerHTML = '';

            // Show upload area and hide preview
            document.getElementById('addUploadArea').style.display = 'block';

            // Reset editor content
            if (addDescriptionEditor) {
                addDescriptionEditor.setData('');
            }

            // Reset select2
            $('#addSpeakers').val(null).trigger('change');

            // Show modal
            document.getElementById('addModal').classList.remove('hidden');

            // Reinitialize select2 if needed
            if (!$('#addSpeakers').hasClass("select2-hidden-accessible")) {
                $('#addSpeakers').select2({
                    placeholder: "Pilih pembicara...",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#addModal')
                });
            }
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(id) {
            const agendaData = window.agendas?.find(agenda => agenda.id == id);

            if (!agendaData) {
                Swal.fire('Error', 'Data agenda tidak ditemukan', 'error');
                return;
            }

            // Set form action
            const form = document.getElementById('editForm');
            form.action = `/agenda/${agendaData.id}`;

            // Populate form fields
            document.getElementById('editId').value = agendaData.id || '';
            document.getElementById('editTitle').value = agendaData.title || '';
            document.getElementById('editEventOrganizer').value = agendaData.event_organizer || '';
            document.getElementById('editLocation').value = agendaData.location || '';
            document.getElementById('editRegisterLink').value = agendaData.register_link || '';
            document.getElementById('editYoutubeLink').value = agendaData.youtube_link || '';
            document.getElementById('editType').value = agendaData.type || '';
            document.getElementById('editStatus').value = agendaData.status || 'Open';

            // Set editor content
            if (editDescriptionEditor) {
                const description = agendaData.description || '';
                editDescriptionEditor.setData(description);
            }

            // Handle datetime
            try {
                if (agendaData.start_datetime) {
                    const startDate = new Date(agendaData.start_datetime);
                    if (!isNaN(startDate.getTime())) {
                        document.getElementById('editStartDatetime').value = formatDateTimeLocal(startDate);
                    }
                }

                if (agendaData.end_datetime) {
                    const endDate = new Date(agendaData.end_datetime);
                    if (!isNaN(endDate.getTime())) {
                        document.getElementById('editEndDatetime').value = formatDateTimeLocal(endDate);
                    }
                }
            } catch (error) {
                console.error('Error parsing dates:', error);
            }

            // =======================
            // FOTO UTAMA (DROPZONE)
            // =======================
            const editUploadArea = document.getElementById('editUploadArea');

            // reset isi dropzone
            editUploadArea.innerHTML = `
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-gray-600 mb-1">Klik atau drag foto ke sini</p>
                    <p class="text-sm text-gray-500">PNG, JPG (MAX 2MB)</p>
                </div>
            `;

            // kalau ada foto lama, tampilkan preview di dalam dropzone
            if (agendaData.image) {
                editUploadArea.innerHTML = `
                    <div class="relative inline-block">
                        <img src="/storage/${agendaData.image}"
                            class="h-40 object-contain rounded-lg mx-auto mb-2" />

                        <button type="button"
                            onclick="removeCurrentImage('edit')"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                            ✕
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Klik atau drop untuk ganti foto</p>
                `;
            }

            // =======================
            // EXTRA IMAGES (EDIT)
            // =======================
            const existingWrapper = document.getElementById('existingExtraImages');
            existingWrapper.innerHTML = '';

            if (agendaData.extra_images && agendaData.extra_images.length) {
                agendaData.extra_images.forEach(img => {
                    const div = document.createElement('div');
                    div.className = 'border rounded-lg p-4 bg-gray-50 relative';

                    div.innerHTML = `
                        <button type="button"
                            onclick="removeExtraEdit(this, ${img.id})"
                            class="absolute top-2 right-2 text-red-500 font-bold">✕</button>

                        <div onclick="this.querySelector('input').click()"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer mb-3 hover:bg-blue-50">

                            <input type="file"
                                name="extra_images[${img.id}]"
                                accept="image/*"
                                class="hidden">

                            <img src="/storage/${img.image}"
                                class="mx-auto h-32 object-contain rounded mb-2">

                            <p class="text-xs text-gray-500">Klik untuk ganti foto</p>
                        </div>

                        <div class="mb-2">
                            <label class="text-xs font-medium text-gray-600">Judul Foto</label>
                            <input type="text"
                                name="extra_titles[${img.id}]"
                                value="${img.title ?? ''}"
                                class="w-full border rounded p-2 text-sm">
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-600">Subtitle Foto</label>
                            <textarea
                            name="extra_subtitles[${img.id}]"
                            rows="2"
                            class="extra-subtitle-editor w-full border rounded p-2 text-sm">${img.subtitle ?? ''}
                            </textarea>

                        </div>
                    `;

                    existingWrapper.appendChild(div);
                });
            }

            // speakers
            if (agendaData.speakers && Array.isArray(agendaData.speakers) && agendaData.speakers.length > 0) {
                const speakerIds = agendaData.speakers.map(speaker => speaker.id.toString());
                $('#editSpeakers').val(speakerIds);
            } else {
                $('#editSpeakers').val([]);
            }
            $('#editSpeakers').trigger('change');

            // show modal
            document.getElementById('editModal').classList.remove('hidden');

            // 🔥 INIT CKEDITOR UNTUK SUBTITLE EXISTING
            setTimeout(() => {
                initExtraSubtitleEditors();
            }, 100);

            }


        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function removeCurrentImage(type) {
            const uploadAreaId = type === 'add' ? 'addUploadArea' : 'editUploadArea';
            const inputId = type === 'add' ? 'addImageInput' : 'editImageInput';

            const uploadArea = document.getElementById(uploadAreaId);
            const input = document.getElementById(inputId);

            input.value = '';

            uploadArea.innerHTML = `
                <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-gray-600 mb-1">Klik untuk upload atau drag and drop</p>
                    <p class="text-sm text-gray-500">PNG, JPG (MAX 2MB)</p>
                </div>
            `;
        }


        function bulkDelete() {
            const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) {
                Swal.fire('Tidak ada yang dipilih', '', 'warning');
                return;
            }

            Swal.fire({
                title: `Hapus ${ids.length} agenda terpilih?`,
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

        function updateBulkDeleteButton() {
            const checked = document.querySelectorAll('.rowCheckbox:checked');
            const btn = document.getElementById('bulkDeleteBtn');
            btn.disabled = checked.length === 0;
            btn.textContent = checked.length > 0 ? `Hapus Terpilih (${checked.length})` : 'Hapus Terpilih';
        }

        // Select all functionality

        function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const uploadAreaId = previewId === 'addPreview' ? 'addUploadArea' : 'editUploadArea';
    const uploadArea = document.getElementById(uploadAreaId);

    if (!input.files || !input.files[0]) return;

    const file = input.files[0];

    // Validasi tipe
    if (!file.type.startsWith('image/')) {
        Swal.fire('Error', 'File harus berupa gambar', 'error');
        input.value = '';
        return;
    }

    // Validasi size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {

        // === PREVIEW DI DALAM DROPZONE ===
        uploadArea.innerHTML = `
            <div class="relative inline-block">
                <img src="${e.target.result}"
                     class="h-40 object-contain rounded-lg mx-auto mb-2" />

                <button type="button"
                    onclick="removeCurrentImage('${previewId === 'addPreview' ? 'add' : 'edit'}')"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                    ✕
                </button>
            </div>
            <p class="text-xs text-gray-500 mt-2">Klik atau drop untuk ganti foto</p>
        `;
    };

    reader.readAsDataURL(file);
}

        // Helper function to format datetime for datetime-local input
        function formatDateTimeLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        // Form submission handlers to ensure CKEditor data is synced
        document.querySelector('#addModal form').addEventListener('submit', function(e) {
            if (addDescriptionEditor) {
                // Sync CKEditor content to textarea before submission
                document.querySelector('#editorAddDescription').value = addDescriptionEditor.getData();
                console.log('Add form data synced:', addDescriptionEditor.getData());
            }
        });

        document.querySelector('#editForm').addEventListener('submit', function(e) {
            if (editDescriptionEditor) {
                // Sync CKEditor content to textarea before submission
                document.querySelector('#editorEditDescription').value = editDescriptionEditor.getData();
                console.log('Edit form data synced:', editDescriptionEditor.getData());
            }
        });

        // Additional event listeners for better user experience
        document.addEventListener('DOMContentLoaded', function() {
            // Handle form validation messages
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
            // Ctrl + N for new agenda
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

    <script>
let extraAddIndex = 0;

function addExtraImageAdd() {
    const wrapper = document.getElementById('extraImagesWrapperAdd');
    const index = extraAddIndex++;

    const div = document.createElement('div');
    div.className = 'border rounded-lg p-4 bg-gray-50 relative';

    div.innerHTML = `
        <button type="button"
            onclick="this.parentElement.remove()"
            class="absolute top-2 right-2 text-red-500 font-bold">✕</button>

        <div id="extraDropAdd-${index}"
            class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer mb-3 hover:bg-blue-50">
            <input type="file"
                name="extra_images[${index}]"
                accept="image/*"
                class="hidden">

            <p class="text-sm text-gray-500">Klik atau drag foto ke sini</p>
            <img class="hidden mx-auto h-32 mt-2 rounded object-contain">
        </div>

        <input type="text"
            name="extra_titles[${index}]"
            placeholder="Judul foto"
            class="w-full border rounded p-2 text-sm mb-2">

        <textarea
    name="extra_subtitles[${index}]"
    rows="2"
    class="extra-subtitle-editor w-full border rounded p-2 text-sm"
    placeholder="Subtitle foto"></textarea>



    `;

    wrapper.appendChild(div);

    setupExtraDropzone(`extraDropAdd-${index}`);
    initExtraSubtitleEditors();
}

</script>
<script>
function removeExtraEdit(button, id) {
    button.parentElement.remove();

    const wrapper = document.getElementById('deletedExtraWrapper');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'delete_extra_ids[]';
    input.value = id;
    wrapper.appendChild(input);
}
</script>
<script>

let extraEditIndex = 0;

function addExtraImageEdit() {
    const wrapper = document.getElementById('extraImagesWrapperEdit');
    const index = extraEditIndex++;

    const div = document.createElement('div');
    div.className = 'border rounded-lg p-4 bg-gray-50 relative';

    div.innerHTML = `
        <button type="button"
            onclick="this.parentElement.remove()"
            class="absolute top-2 right-2 text-red-500 font-bold">✕</button>

        <div id="extraDropEdit-${index}"
            class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer mb-3 hover:bg-blue-50 transition">

            <input type="file"
                name="extra_images_new[${index}]"
                accept="image/*"
                class="hidden">

            <img class="hidden mx-auto h-32 object-contain rounded mb-2">

            <p class="text-xs text-gray-500">Klik atau drag foto ke sini</p>
        </div>

        <input type="text"
            name="extra_titles_new[${index}]"
            placeholder="Judul foto"
            class="w-full border rounded p-2 text-sm mb-2">

        <textarea
            name="extra_subtitles_new[${index}]"
            class="extra-subtitle-editor w-full border rounded p-2 text-sm"
            rows="2"
            placeholder="Subtitle foto"></textarea>
    `;

    wrapper.appendChild(div);

    setupExtraDropzone(`extraDropEdit-${index}`);

    // 🔥 INIT CKEDITOR
    initExtraSubtitleEditors();
}


</script>
<script>
    document.addEventListener('change', function (e) {
    // hanya untuk extra image edit
    if (!e.target.matches('input[type="file"][name^="extra_images"]')) return;

    const input = e.target;
    const wrapper = input.closest('div');
    const img = wrapper.querySelector('img');

    if (!input.files || !input.files[0] || !img) return;

    const reader = new FileReader();
    reader.onload = function (ev) {
        img.src = ev.target.result; // 🔥 GANTI PREVIEW
    };
    reader.readAsDataURL(input.files[0]);
});

</script>
<script>
function initExtraSubtitleEditors() {
    document.querySelectorAll('.extra-subtitle-editor').forEach((el, index) => {

        // kasih ID unik kalau belum ada
        if (!el.id) {
            el.id = 'extra_subtitle_' + index + '_' + Date.now();
        }

        // cegah double init
        if (window.extraEditors[el.id]) return;

        ClassicEditor.create(el, {
            toolbar: [
                'bold', 'italic', 'underline',
                '|',
                'bulletedList', 'numberedList',
                '|',
                'link',
                '|',
                'undo', 'redo'
            ],
            language: 'id'
        }).then(editor => {
            window.extraEditors[el.id] = editor;

            // sync ke textarea saat submit
            editor.model.document.on('change:data', () => {
                el.value = editor.getData();
            });
        }).catch(err => {
            console.error('CKEditor subtitle error:', err);
        });

    });
}
</script>


    <style>
table thead th {
    border-bottom: 2px solid #e5e7eb;
}

table tbody tr td {
    vertical-align: middle;
}
</style>
@endsection
