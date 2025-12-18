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
                    <a href="{{ route('slider.show', $item->id) }}"
                    class="px-3 py-1 text-xs bg-indigo-50 text-indigo-600 border border-indigo-200 rounded hover:bg-indigo-100">
                        Detail
                    </a>

                    <button
  onclick="openEditModal(this)"
    data-slider='@json($item->load("extraImages")->toArray())'

  class="px-3 py-1 text-xs bg-blue-50 text-blue-600 border rounded"
>
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
            <input type="checkbox" name="display_on_home" class="mr-2" value="1" {{ old('display_on_home') ? 'checked' : '' }}/>
            <span class="font-medium">Tampilkan di Homepage</span>
          </label>
        </div>

        <div>
          <label class="block mb-1 font-medium">Judul</label>
          <input type="text" name="title" class="w-full border rounded p-2 text-sm" value="{{ old('title') }}"/>
        </div>

        <div>
          <label class="block mb-1 font-medium">YouTube ID</label>
          <input type="text" name="youtube_id" class="w-full border rounded p-2 text-sm" placeholder="Contoh: dQw4w9WgXcQ" value="{{ old('youtube_id') }}"/>
        </div>

        <div class="col-span-2">
          <label class="block mb-1 font-medium">Subtitle</label>
          <textarea name="subtitle" id="editorAddSubtitle" rows="4" class="w-full border rounded p-2 text-sm">{{ old('subtitle') }}</textarea>
        </div>

        <div>
          <label class="block mb-1 font-medium">Button Text</label>
          <input type="text" name="button_text" class="w-full border rounded p-2 text-sm" placeholder="Contoh: Selengkapnya" value="{{ old('button_text') }}"/>
        </div>

        <div>
          <label class="block mb-1 font-medium">URL Link</label>
          <input type="url" name="url_link" class="w-full border rounded p-2 text-sm" placeholder="https://example.com" value="{{ old('url_link') }}"/>
        </div>
      </div>

      <!-- Upload Gambar -->
      <div class="mt-4">
        <label class="block mb-2 font-medium">Upload Gambar</label>

        <input type="file" name="image" id="addImageInput" onchange="previewImage(this, 'addPreview')"
               accept="image/png,image/jpg,image/jpeg,image/webp" class="hidden" />

        <div id="addUploadArea" onclick="document.getElementById('addImageInput').click()"
             class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors duration-200">
          <div class="flex flex-col items-center">
            <p class="text-gray-600 mb-2">Klik untuk upload atau drag and drop</p>
            <p class="text-sm text-gray-500">PNG, JPG, JPEG, atau WEBP (MAX. 2MB)</p>
          </div>
        </div>

        <div id="addPreview" class="mt-4"></div>
      </div>

      <hr class="my-6">

      <!-- Foto Tambahan -->
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-sm">Foto Tambahan</h3>
        <button type="button" onclick="addExtraImage()"
                class="text-xs px-3 py-1 rounded bg-blue-50 text-blue-600 hover:bg-blue-100">
          + Tambah Foto
        </button>
      </div>

      <div id="extraImagesWrapper" class="space-y-4"></div>

      <!-- Action -->
      <div class="flex justify-end space-x-2 mt-6">
        <button type="button" onclick="closeAddModal()"
                class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>

        <button type="button" onclick="submitAddForm()"
                class="px-4 py-2 rounded bg-blue-500 text-white">Simpan</button>
      </div>
    </form>
  </div>
</div>


    <!-- Modal Edit -->
<div id="editModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
        <h2 class="text-lg font-semibold">Edit Slider</h2>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- TEMPAT PENAMPUNG ID FOTO YANG DIHAPUS -->
            <div id="deletedExtraWrapper"></div>

            <!-- ===== BASIC FIELD ===== -->
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="display_on_home" id="editDisplayOnHome"
                               class="mr-2" value="1">
                        <span class="font-medium">Tampilkan di Homepage</span>
                    </label>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Judul</label>
                    <input type="text" name="title" id="editTitle"
                           class="w-full border rounded p-2 text-sm">
                </div>

                <div>
                    <label class="block mb-1 font-medium">YouTube ID</label>
                    <input type="text" name="youtube_id" id="editYoutubeId"
                           class="w-full border rounded p-2 text-sm">
                </div>

                <div class="col-span-2">
                    <label class="block mb-1 font-medium">Subtitle</label>
                    <textarea name="subtitle" id="editorEditSubtitle"
                              class="w-full border rounded p-2 text-sm"></textarea>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Button Text</label>
                    <input type="text" name="button_text" id="editButtonText"
                           class="w-full border rounded p-2 text-sm">
                </div>

                <div>
                    <label class="block mb-1 font-medium">URL Link</label>
                    <input type="url" name="url_link" id="editUrlLink"
                           class="w-full border rounded p-2 text-sm">
                </div>
            </div>

            <!-- ===== IMAGE UTAMA ===== -->
            <div class="mt-4">
                <label class="block mb-2 font-medium">Ganti Gambar Utama</label>

                <input type="file" name="image" id="editImageInput"
                       accept="image/*" class="hidden"
                       onchange="previewImage(this,'editPreview')">

                <div id="editUploadArea"
                     onclick="document.getElementById('editImageInput').click()"
                     class="border-2 border-dashed border-gray-300 rounded-lg p-6
                            text-center cursor-pointer hover:bg-blue-50">
                    Klik untuk upload gambar
                </div>

                <div id="editPreview" class="mt-3"></div>
            </div>

            <hr class="my-6">

           <!-- ===== FOTO TAMBAHAN ===== -->
<div class="mt-6">

    <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-sm">Foto Tambahan</h3>
        <button type="button"
                onclick="addExtraImageEdit()"
                class="text-xs px-3 py-1 rounded bg-blue-50 text-blue-600 hover:bg-blue-100">
            + Tambah Foto
        </button>
    </div>

    <div id="extraImagesWrapperEdit" class="space-y-4 mt-4"></div>

</div> <!-- ✅ WAJIB ADA -->



            <!-- ACTION -->
            <div class="flex justify-end gap-2 mt-6">
                <button type="button"
                        onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-300 rounded">
                    Batal
                </button>
                <button type="submit"
                        id="editSubmitBtn"
                        class="px-4 py-2 bg-blue-500 text-white rounded">
                    Simpan
                </button>

            </div>
        </form>
    </div>
</div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
/* =============================
   GLOBAL CKEDITOR CONFIG
============================= */
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

</script>

    <script>
        let extraEditEditors = {};
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



            const editForm = document.getElementById('editForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleFormSubmission('edit');
                });
            }
        });

    function initEditSubtitleEditor(initialData = '') {
    const el = document.getElementById('editorEditSubtitle');
    if (!el) return;

    // destroy editor lama
    if (editSubtitleEditor) {
        editSubtitleEditor.destroy();
        editSubtitleEditor = null;
    }

    ClassicEditor
        .create(el, editorConfig)
        .then(editor => {
            editSubtitleEditor = editor;

            // 🔥 SET DATA DI SINI (KUNCI)
            editor.setData(initialData);

            // sync ke textarea
            editor.model.document.on('change:data', () => {
                el.value = editor.getData();
            });
        })
        .catch(err => console.error(err));
}

        // Handle form submission with proper AJAX response handling
        function handleFormSubmission() {
    const form = document.getElementById('editForm');
    const subtitleEditor = editSubtitleEditor;
    const submitBtn = document.getElementById('editSubmitBtn');

    if (!form || !submitBtn) return;

    Swal.fire({
        title: 'Menyimpan data...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });

    submitBtn.disabled = true;

    const formData = new FormData(form);

    if (subtitleEditor) {
        formData.set('subtitle', subtitleEditor.getData());
    }

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        }
    })
    .then(res => res.json())
    .then(result => {
        Swal.close();

        if (result.success) {
            showAlert('success', result.message);
            setTimeout(() => location.reload(), 800);
        } else {
            showAlert('error', result.message || 'Gagal menyimpan');
        }
    })
    .catch(() => {
        Swal.close();
        showAlert('error', 'Terjadi kesalahan');
    })
    .finally(() => {
        submitBtn.disabled = false;
    });
}

        
        function openAddModal() {
                // TUTUP EDIT MODAL JIKA ADA
    document.getElementById('editModal').classList.add('hidden');

    // reset form
    document.getElementById('addForm').reset();
    document.getElementById('addPreview').innerHTML = '';
    document.getElementById('addUploadArea').style.display = 'block';

    if (addSubtitleEditor) {
        addSubtitleEditor.setData('');
    }

    document.getElementById('addModal').classList.remove('hidden');
        
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
    const slider = JSON.parse(button.dataset.slider);

    /* ===============================
       RESET EXTRA IMAGE WRAPPER
    =============================== */
    const wrapper = document.getElementById('extraImagesWrapperEdit');
    wrapper.innerHTML = '';

    /* ===============================
       DESTROY EDITOR LAMA (PENTING)
    =============================== */
    if (editSubtitleEditor) {
        editSubtitleEditor.destroy();
        editSubtitleEditor = null;
    }

    if (window.extraEditEditors) {
        Object.values(extraEditEditors).forEach(ed => ed && ed.destroy());
    }
    window.extraEditEditors = {};

    /* ===============================
       SET DATA UTAMA
    =============================== */
    document.getElementById('editTitle').value = slider.title || '';
    document.getElementById('editYoutubeId').value = slider.youtube_id || '';
    document.getElementById('editButtonText').value = slider.button_text || '';
    document.getElementById('editUrlLink').value = slider.url_link || '';
    document.getElementById('editDisplayOnHome').checked = !!slider.display_on_home;

    document.getElementById('editPreview').innerHTML =
        slider.image
            ? `<img src="/storage/${slider.image}" class="h-32 w-32 rounded border">`
            : '';

    document.getElementById('editForm').action = `/slider/${slider.id}`;
    document.getElementById('editModal').classList.remove('hidden');

    /* ===============================
       INIT CKEDITOR SUBTITLE UTAMA
    =============================== */
    initEditSubtitleEditor(slider.subtitle || '');

    /* ===============================
       FOTO TAMBAHAN + CKEDITOR
    =============================== */
    if (Array.isArray(slider.extra_images) && slider.extra_images.length) {
        slider.extra_images.forEach(img => {
            const index = img.id;

            const div = document.createElement('div');
            div.className = 'border rounded-lg p-4 bg-gray-50 relative';

            div.innerHTML = `
                <button type="button"
                onclick="removeExtraEdit(this, '${index}')"
                class="absolute top-2 right-2 text-red-500 font-bold">✕</button>


                <label class="block text-sm font-medium mb-1">Foto Tambahan</label>

                <input type="file"
                    accept="image/*"
                    name="extra_images[${index}]"
                    id="extraInput_${index}"
                    class="hidden"
                    onchange="previewExtraReplace(this, ${index})">

                <div onclick="document.getElementById('extraInput_${index}').click()"
                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer">
                    Klik atau drag foto ke sini (replace)
                </div>

                <div id="extraPreview_${index}" class="mt-3">
                    <img src="/storage/${img.image}"
                         class="w-20 h-20 object-cover rounded border">
                </div>

                <label class="block text-sm font-medium mt-3">Judul</label>
                <input type="text"
                    name="extra_titles[${index}]"
                    value="${img.title ?? ''}"
                    class="w-full border rounded p-2 text-sm mb-2">

                <label class="block text-sm font-medium">Subtitle</label>
                <textarea
                    name="extra_subtitles[${index}]"
                    id="extraEditEditor_${index}"
                    class="w-full border rounded p-2 text-sm"
                    rows="3">${img.subtitle ?? ''}</textarea>
            `;

            wrapper.appendChild(div);

            /* ===============================
               INIT CKEDITOR EXTRA SUBTITLE
            =============================== */
            setTimeout(() => {
                const textarea = document.getElementById(`extraEditEditor_${index}`);
                if (!textarea) return;

                ClassicEditor.create(textarea, editorConfig)
                    .then(editor => {
                        extraEditEditors[index] = editor;
                        editor.model.document.on('change:data', () => {
                            textarea.value = editor.getData();
                        });
                    })
                    .catch(err => console.error(err));
            }, 50);
        });
    }
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

        // validate type
        if (!file.type.startsWith('image/')) {
            showAlert('error', 'File harus berupa gambar');
            input.value = '';
            return;
        }

        // validate size
        if (file.size > 2 * 1024 * 1024) {
            showAlert('error', 'Ukuran maksimal 2MB');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.innerHTML = `
                <div class="relative inline-block">
                    <img src="${e.target.result}"
                        class="h-32 w-32 rounded-lg shadow-md object-cover border">
                    <button type="button"
                        onclick="removeCurrentImage('${previewId === 'addPreview' ? 'add' : 'edit'}')"
                        class="absolute -top-2 -right-2 bg-red-500 text-white
                               rounded-full w-6 h-6 flex items-center justify-center">
                        ✕
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
<script>
let extraIndex = 0;

function addExtraImage() {
  const wrapper = document.getElementById('extraImagesWrapper');
  const index = extraIndex++;

  const div = document.createElement('div');
  div.className = 'border rounded-lg p-4 bg-gray-50 relative';

  div.innerHTML = `
    <button type="button"
      onclick="this.parentElement.remove()"
      class="absolute top-2 right-2 text-red-500">✕</button>

    <label class="block text-sm font-medium mb-1">Foto Tambahan</label>

<input type="file"
  name="extra_images[${index}][]"
  id="extraFile_${index}"
  class="hidden"
  multiple
  accept="image/*">


    <div
      onclick="document.getElementById('extraFile_${index}').click()"
      ondragover="event.preventDefault()"
      ondrop="handleDrop(event, ${index})"
      class="border-2 border-dashed border-gray-300 rounded-lg p-6
             text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50">
      Klik atau drag foto ke sini
    </div>

    <div id="preview_${index}" class="grid grid-cols-4 gap-2 mt-3"></div>

    <label class="block text-sm font-medium mt-3">Judul Foto</label>
    <input type="text"
      name="extra_titles[${index}]"
      class="w-full border rounded p-2 text-sm mb-2">

    <label class="block text-sm font-medium">Subtitle Foto</label>
    <textarea
      name="extra_subtitles[${index}]"
      id="extraEditor_${index}"
      class="w-full border rounded p-2 text-sm"
      rows="3"></textarea>
  `;

  wrapper.appendChild(div);

  // CKEDITOR
  ClassicEditor.create(
    document.querySelector(`#extraEditor_${index}`),
    editorConfig
  ).then(editor => {
    editor.model.document.on('change:data', () => {
      document.querySelector(`#extraEditor_${index}`).value = editor.getData();
    });
  });

  document
    .getElementById(`extraFile_${index}`)
    .addEventListener('change', e => {
      renderPreview(e.target.files, index);
    });
}

function handleDrop(e, index) {
  e.preventDefault();
  const input = document.getElementById(`extraFile_${index}`);
  input.files = e.dataTransfer.files;
  renderPreview(input.files, index);
}

function renderPreview(files, index) {
  const preview = document.getElementById(`preview_${index}`);
  preview.innerHTML = '';

  [...files].forEach(file => {
    if (!file.type.startsWith('image/')) return;

    const reader = new FileReader();
    reader.onload = e => {
      const img = document.createElement('img');
      img.src = e.target.result;
      img.className = 'w-20 h-20 object-cover rounded border';
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
}
</script>

<script>
function renderExtraPreview(files, index) {
    const preview = document.getElementById(`extraEditPreview_${index}`);
    if (!preview) return;

    preview.innerHTML = '';

    [...files].forEach(file => {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative w-20 h-20';

            div.innerHTML = `
                <img src="${e.target.result}"
                     class="w-20 h-20 object-cover rounded-lg border">
            `;

            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>

<script>
function handleExtraDropReplace(e, index) {
  e.preventDefault();
  const input = document.getElementById(`extraInput_${index}`);
  if (!input) return;

  input.files = e.dataTransfer.files;
  renderExtraReplacePreview(input.files[0], index);
}

function renderExtraReplacePreview(file, index) {
  const preview = document.getElementById(`extraEditPreview_${index}`);
  preview.innerHTML = '';

  const reader = new FileReader();
  reader.onload = e => {
    preview.innerHTML = `
      <img src="${e.target.result}"
           class="w-20 h-20 object-cover rounded-lg border">
    `;
  };
  reader.readAsDataURL(file);
}

</script>

<script>
function removeExistingExtraImage(imageId, index) {
    const preview = document.getElementById(`extraEditPreview_${index}`);
    if (!preview) return;

    // hapus thumbnail saja
    const thumb = preview.querySelector(`[data-existing-id="${imageId}"]`);
    if (thumb) thumb.remove();

    // kirim ID ke backend
    const wrapper = preview.closest('.extra-item');
    if (!wrapper.querySelector(`input[value="${imageId}"]`)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_extra_ids[]';
        input.value = imageId;
        wrapper.appendChild(input);
    }
}
</script>
<script>
function submitAddForm() {
    const form = document.getElementById('addForm');
    const formData = new FormData(form);

    // inject subtitle utama
    if (addSubtitleEditor) {
        formData.set('subtitle', addSubtitleEditor.getData());
    }

    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(result => {
        Swal.close();

        if (result.success !== false) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                timer: 1200,
                showConfirmButton: false
            });
            setTimeout(() => location.reload(), 1200);
        } else {
            Swal.fire('Error', result.message || 'Gagal', 'error');
        }
    })
    .catch(err => {
        Swal.close();
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
        console.error(err);
    });
}
</script>
<script>
function handleExtraDropEdit(e, index) {
    e.preventDefault();
    const input = document.getElementById(`extraInput_${index}`);
    if (!input) return;

    input.files = e.dataTransfer.files;
    renderExtraPreview(input.files, index);
}
</script>
<script>
function previewExtraReplace(input, index) {
  if (!input.files[0]) return;

  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById(`extraPreview_${index}`).innerHTML = `
      <img src="${e.target.result}"
           class="w-20 h-20 object-cover rounded border">
    `;
  };
  reader.readAsDataURL(input.files[0]);
}

</script>
<script>
let extraEditIndex = 0;

/* ===============================
   ➕ TAMBAH FOTO EXTRA (EDIT)
=============================== */
function addExtraImageEdit() {
    const wrapper = document.getElementById('extraImagesWrapperEdit');
    const index = 'new_' + extraEditIndex++;

    const div = document.createElement('div');
    div.className = 'border rounded-lg p-4 bg-gray-50 relative';

    div.innerHTML = `
        <button type="button"
            onclick="removeExtraEdit(this, '${index}')"
            class="absolute top-2 right-2 text-red-500 font-bold">✕</button>

        <label class="block text-sm font-medium mb-1">Foto Tambahan</label>

        <input type="file"
            accept="image/*"
            name="extra_images[${index}]"
            id="extraInput_${index}"
            class="hidden"
            onchange="previewExtraReplace(this, '${index}')">

        <div onclick="document.getElementById('extraInput_${index}').click()"
            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer">
            Klik untuk upload foto
        </div>

        <div id="extraPreview_${index}" class="mt-3"></div>

        <label class="block text-sm font-medium mt-3">Judul</label>
        <input type="text"
            name="extra_titles[${index}]"
            class="w-full border rounded p-2 text-sm mb-2">

        <label class="block text-sm font-medium">Subtitle</label>
        <textarea
            name="extra_subtitles[${index}]"
            id="extraEditEditor_${index}"
            class="w-full border rounded p-2 text-sm"
            rows="3"></textarea>
    `;

    wrapper.appendChild(div);

    /* ===============================
       🔥 INIT CKEDITOR (KUNCI)
    =============================== */
    setTimeout(() => {
        const textarea = document.getElementById(`extraEditEditor_${index}`);
        if (!textarea) return;

        ClassicEditor.create(textarea, editorConfig)
            .then(editor => {
                extraEditEditors[index] = editor;

                editor.model.document.on('change:data', () => {
                    textarea.value = editor.getData();
                });
            })
            .catch(err => console.error('CKEditor extra edit error:', err));
    }, 50);
}

/* ===============================
   ❌ HAPUS FOTO EXTRA (GLOBAL)
=============================== */
function removeExtraEdit(button, id) {
    const wrapper = button.closest('.border');
    if (wrapper) wrapper.remove();

    // destroy CKEditor
    if (extraEditEditors[id]) {
        extraEditEditors[id].destroy();
        delete extraEditEditors[id];
    }

    // tandai hapus di backend (untuk data lama)
    if (!id.startsWith('new_')) {
        const deletedWrapper = document.getElementById('deletedExtraWrapper');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_extra_ids[]';
        input.value = id;
        deletedWrapper.appendChild(input);
    }
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
