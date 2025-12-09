@extends('layouts.app')

@section('content')
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Loker</h1>
            <button onclick="openAddModal()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Tambah Career
            </button>
        </div>

        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Cari berdasarkan posisi..."
                class="border px-3 py-2 w-full rounded text-sm" onkeyup="searchTable()">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="px-4 py-2 border">Job Type</th>
                        <th class="px-4 py-2 border">Position</th>
                        <th class="px-4 py-2 border">Description</th>
                        <th class="px-4 py-2 border" width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody id="careerTable">
                    @foreach ($careers as $index => $career)
                        <tr>
                            <td class="px-4 py-2 border">{{ $career->job_type }}</td>
                            <td class="px-4 py-2 border">{{ $career->position_title }}</td>
                            <td class="px-4 py-2 border">
                                {{ Str::limit(strip_tags(implode(' ', is_array($career->deskripsi) ? $career->deskripsi : [$career->deskripsi])), 60) }}
                            </td>
                            <td class="px-4 py-2 border">
                                <div class="flex justify-end space-x-1">
                                    <a href="{{ route('career.show', $career->id) }}"
                                        class="text-green-600 hover:text-green-800 px-2 py-1 text-xs border border-green-300 rounded hover:bg-green-50 inline-block">Detail</a>
                                    <button onclick="openEditModal({{ $career->id }})"
                                        class="text-blue-600 hover:text-blue-800 px-2 py-1 text-xs border border-blue-300 rounded hover:bg-blue-50">Edit</button>
                                    <button onclick="confirmDelete({{ $career->id }})" data-id="{{ $career->id }}"
                                        class="text-red-600 hover:text-red-800 px-2 py-1 text-xs border border-red-300 rounded hover:bg-red-50 delete-btn">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        window.careers = @json($careers);
    </script>

    <!-- Form tersembunyi untuk delete -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Modal Tambah -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Tambah Career</h2>
            <form action="{{ route('career.store') }}" method="POST" id="addCareerForm">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Job Type</label>
                        <select name="job_type" required class="w-full border rounded p-2 text-sm">
                            <option value="">-- Pilih --</option>
                            <option value="Full Time">Full Time</option>
                            <option value="Part Time">Part Time</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Position Title</label>
                        <input type="text" name="position_title" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Lokasi</label>
                        <input type="text" name="lokasi" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Pengalaman</label>
                        <input type="text" name="pengalaman" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Jam Kerja</label>
                        <input type="text" name="jam_kerja" required class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Hari Kerja</label>
                        <input type="text" name="hari_kerja" required class="w-full border rounded p-2 text-sm" />
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block mb-1 font-medium">Ringkasan</label>
                    <textarea name="ringkasan" id="editorAddRingkasan" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                </div>

                <div class="mt-4">
                    <label class="block mb-1 font-medium">Klasifikasi</label>
                    <div id="addKlasifikasiContainer" class="space-y-2">
                        <div class="flex gap-2">
                            <input type="text" name="klasifikasi[]" required class="w-full border rounded p-2 text-sm" />
                        </div>
                    </div>
                    <button type="button" onclick="addInput('addKlasifikasiContainer', 'klasifikasi[]')"
                        class="text-sm text-blue-600 mt-2 hover:text-blue-800">+ Tambah Klasifikasi</button>
                </div>

                <div class="mt-4">
                    <label class="block mb-1 font-medium">Deskripsi</label>
                    <div id="addDeskripsiContainer" class="space-y-2">
                        <div class="flex gap-2">
                            <textarea name="deskripsi[]" id="editorAddDeskripsi0" rows="4" class="w-full border rounded p-2 text-sm"></textarea>
                        </div>
                    </div>
                    <button type="button" onclick="addTextarea('addDeskripsiContainer', 'deskripsi[]')"
                        class="text-sm text-blue-600 mt-2 hover:text-blue-800">+ Tambah Deskripsi</button>
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-semibold">Edit Career</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editId">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium">Job Type</label>
                        <select name="job_type" id="editJobType" required class="w-full border rounded p-2 text-sm">
                            <option value="">Pilih</option>
                            <option value="Full Time">Full Time</option>
                            <option value="Part Time">Part Time</option>
                            <option value="Contract">Contract</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Position Title</label>
                        <input type="text" name="position_title" id="editPositionTitle" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Lokasi</label>
                        <input type="text" name="lokasi" id="editLokasi" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Pengalaman</label>
                        <input type="text" name="pengalaman" id="editPengalaman" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Jam Kerja</label>
                        <input type="text" name="jam_kerja" id="editJamKerja" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Hari Kerja</label>
                        <input type="text" name="hari_kerja" id="editHariKerja" required
                            class="w-full border rounded p-2 text-sm" />
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block mb-1 font-medium">Ringkasan</label>
                    <textarea name="ringkasan" id="editorEditRingkasan" rows="4"
                        class="w-full border rounded p-2 text-sm"></textarea>
                </div>

                <div class="mt-4">
                    <label class="block mb-1 font-medium">Klasifikasi</label>
                    <div id="editKlasifikasiContainer" class="space-y-2">
                        <div class="flex gap-2">
                            <input type="text" name="klasifikasi[]" required
                                class="w-full border rounded p-2 text-sm" />
                        </div>
                    </div>
                    <button type="button" onclick="addInput('editKlasifikasiContainer', 'klasifikasi[]')"
                        class="text-sm text-blue-600 mt-2 hover:text-blue-800">+ Tambah Klasifikasi</button>
                </div>

                <div class="mt-4">
                    <label class="block mb-1 font-medium">Deskripsi</label>
                    <div id="editDeskripsiContainer" class="space-y-2">
                        <div class="flex gap-2">
                            <textarea name="deskripsi[]" id="editorEditDeskripsi0" rows="4"
                                class="w-full border rounded p-2 text-sm"></textarea>
                        </div>
                    </div>
                    <button type="button" onclick="addTextarea('editDeskripsiContainer', 'deskripsi[]')"
                        class="text-sm text-blue-600 mt-2 hover:text-blue-800">+ Tambah Deskripsi</button>
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
        let addRingkasanEditor = null;
        let editRingkasanEditor = null;
        let addDeskripsiEditors = [];
        let editDeskripsiEditors = [];
        let editorCounter = 0;

        // Initialize CKEditor when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced configuration for CKEditor
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
                    options: [ 9, 11, 13, 'default', 17, 19, 21 ]
                },
                alignment: {
                    options: [ 'left', 'right', 'center', 'justify' ]
                },
                image: {
                    toolbar: [ 'imageTextAlternative', 'imageStyle:inline', 'imageStyle:block', 'imageStyle:side' ]
                },
                table: {
                    contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
                }
            };

            // Initialize CKEditor instances
            ClassicEditor.create(document.querySelector('#editorAddRingkasan'), editorConfig)
                .then(editor => { 
                    addRingkasanEditor = editor; 
                    console.log('Add Ringkasan Editor initialized successfully');
                })
                .catch(error => console.error('Error initializing add ringkasan editor:', error));

            ClassicEditor.create(document.querySelector('#editorEditRingkasan'), editorConfig)
                .then(editor => { 
                    editRingkasanEditor = editor; 
                    console.log('Edit Ringkasan Editor initialized successfully');
                })
                .catch(error => console.error('Error initializing edit ringkasan editor:', error));

            ClassicEditor.create(document.querySelector('#editorAddDeskripsi0'), editorConfig)
                .then(editor => { 
                    addDeskripsiEditors[0] = editor; 
                    console.log('Add Deskripsi Editor initialized successfully');
                })
                .catch(error => console.error('Error initializing add deskripsi editor:', error));

            ClassicEditor.create(document.querySelector('#editorEditDeskripsi0'), editorConfig)
                .then(editor => { 
                    editDeskripsiEditors[0] = editor; 
                    console.log('Edit Deskripsi Editor initialized successfully');
                })
                .catch(error => console.error('Error initializing edit deskripsi editor:', error));

            // Handle form submissions
            const addForm = document.getElementById('addCareerForm');
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

            // Show flash messages using SweetAlert
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

        // Simplified alert functions
        function showAlert(type, message) {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Berhasil!' : 'Error!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        }

        // Simplified form submission - no complex fetch handling
        function handleFormSubmission(type) {
            const isAdd = type === 'add';
            const form = document.getElementById(isAdd ? 'addCareerForm' : 'editForm');
            const ringkasanEditor = isAdd ? addRingkasanEditor : editRingkasanEditor;
            const deskripsiEditors = isAdd ? addDeskripsiEditors : editDeskripsiEditors;

            try {
                // Validate
                let hasValidDeskripsi = false;
                deskripsiEditors.forEach((editor) => {
                    if (editor && editor.getData().trim()) {
                        hasValidDeskripsi = true;
                    }
                });

                if (!hasValidDeskripsi) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Minimal harus ada satu deskripsi yang diisi'
                    });
                    return;
                }

                if (ringkasanEditor && !ringkasanEditor.getData().trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ringkasan harus diisi'
                    });
                    return;
                }

                // Sync CKEditor data to form
                syncCKEditorData(form, ringkasanEditor, deskripsiEditors);

                // Close modal first to avoid seeing it during navigation
                if (isAdd) {
                    closeAddModal();
                } else {
                    closeEditModal();
                }

                // Submit form normally - let Laravel handle the redirect and flash messages
                form.submit();

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memproses data'
                });
            }
        }

        // Sync CKEditor data to hidden form inputs
        function syncCKEditorData(form, ringkasanEditor, deskripsiEditors) {
            // Handle ringkasan - use regular form field, not hidden input
            if (ringkasanEditor) {
                const ringkasanTextarea = form.querySelector('textarea[name="ringkasan"]');
                if (ringkasanTextarea) {
                    ringkasanTextarea.value = ringkasanEditor.getData();
                }
            }

            // Handle deskripsi - sync to existing textareas
            const deskripsiTextareas = form.querySelectorAll('textarea[name="deskripsi[]"]');
            deskripsiEditors.forEach((editor, index) => {
                if (editor && deskripsiTextareas[index]) {
                    deskripsiTextareas[index].value = editor.getData();
                }
            });
        }

        // Modal functions
        function openAddModal() {
            document.getElementById('addCareerForm').reset();
            if (addRingkasanEditor) addRingkasanEditor.setData('');
            
            // Reset additional deskripsi editors (keep first one)
            addDeskripsiEditors.forEach((editor, index) => {
                if (index > 0 && editor) editor.destroy();
            });
            addDeskripsiEditors = addDeskripsiEditors.slice(0, 1);
            if (addDeskripsiEditors[0]) addDeskripsiEditors[0].setData('');

            // Reset dynamic fields
            document.getElementById('addKlasifikasiContainer').innerHTML = 
                '<div class="flex gap-2"><input type="text" name="klasifikasi[]" required class="w-full border rounded p-2 text-sm" /></div>';
            document.getElementById('addDeskripsiContainer').innerHTML = 
                '<div class="flex gap-2"><textarea name="deskripsi[]" id="editorAddDeskripsi0" rows="4" class="w-full border rounded p-2 text-sm"></textarea></div>';

            // Reinitialize first deskripsi editor
            setTimeout(() => {
                ClassicEditor.create(document.querySelector('#editorAddDeskripsi0'))
                    .then(editor => { addDeskripsiEditors[0] = editor; })
                    .catch(error => console.error('Error reinitializing add deskripsi editor:', error));
            }, 100);

            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(id) {
            const careerData = window.careers?.find(career => career.id == id);
            if (!careerData) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Data career tidak ditemukan' });
                return;
            }

            const form = document.getElementById('editForm');
            form.action = `/career/${careerData.id}`;

            // Populate basic fields
            document.getElementById('editId').value = careerData.id || '';
            document.getElementById('editJobType').value = careerData.job_type || '';
            document.getElementById('editPositionTitle').value = careerData.position_title || '';
            document.getElementById('editLokasi').value = careerData.lokasi || '';
            document.getElementById('editPengalaman').value = careerData.pengalaman || '';
            document.getElementById('editJamKerja').value = careerData.jam_kerja || '';
            document.getElementById('editHariKerja').value = careerData.hari_kerja || '';

            if (editRingkasanEditor) {
                editRingkasanEditor.setData(careerData.ringkasan || '');
            }

            // Handle klasifikasi
            let klasifikasiHTML = '';
            if (careerData.klasifikasi && Array.isArray(careerData.klasifikasi) && careerData.klasifikasi.length > 0) {
                careerData.klasifikasi.forEach((k, index) => {
                    klasifikasiHTML += `<div class="flex gap-2">
                        <input type="text" name="klasifikasi[]" value="${escapeHtml(k || '')}" required class="w-full border rounded p-2 text-sm" />
                        ${index > 0 ? '<button type="button" onclick="removeField(this)" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm">×</button>' : ''}
                    </div>`;
                });
            } else {
                klasifikasiHTML = '<div class="flex gap-2"><input type="text" name="klasifikasi[]" required class="w-full border rounded p-2 text-sm" /></div>';
            }
            document.getElementById('editKlasifikasiContainer').innerHTML = klasifikasiHTML;

            // Handle deskripsi - destroy existing editors
            editDeskripsiEditors.forEach(editor => { if (editor) editor.destroy(); });
            editDeskripsiEditors = [];

            let deskripsiHTML = '';
            if (careerData.deskripsi && Array.isArray(careerData.deskripsi) && careerData.deskripsi.length > 0) {
                careerData.deskripsi.forEach((d, index) => {
                    deskripsiHTML += `<div class="flex gap-2">
                        <textarea name="deskripsi[]" id="editorEditDeskripsi${index}" rows="4" class="w-full border rounded p-2 text-sm">${escapeHtml(d || '')}</textarea>
                        ${index > 0 ? `<button type="button" onclick="removeDeskripsiField(this, ${index})" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm self-start">×</button>` : ''}
                    </div>`;
                });
            } else {
                deskripsiHTML = '<div class="flex gap-2"><textarea name="deskripsi[]" id="editorEditDeskripsi0" rows="4" class="w-full border rounded p-2 text-sm"></textarea></div>';
            }
            document.getElementById('editDeskripsiContainer').innerHTML = deskripsiHTML;

            // Initialize CKEditor for each deskripsi textarea
            setTimeout(() => {
                const textareas = document.querySelectorAll('#editDeskripsiContainer textarea');
                textareas.forEach((textarea, index) => {
                    ClassicEditor.create(textarea)
                        .then(editor => {
                            editDeskripsiEditors[index] = editor;
                            if (careerData.deskripsi && careerData.deskripsi[index]) {
                                editor.setData(careerData.deskripsi[index]);
                            }
                        })
                        .catch(error => console.error(`Error initializing edit deskripsi editor ${index}:`, error));
                });
            }, 100);

            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        function addInput(containerId, name) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = 'flex gap-2';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = name;
            input.className = 'w-full border rounded p-2 text-sm';
            input.required = true;

            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm';
            deleteBtn.innerHTML = '×';
            deleteBtn.onclick = function() { removeField(this); };

            div.appendChild(input);
            div.appendChild(deleteBtn);
            container.appendChild(div);
        }

        function addTextarea(containerId, name) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = 'flex gap-2';

            const textarea = document.createElement('textarea');
            textarea.name = name;
            textarea.rows = 4;
            textarea.className = 'w-full border rounded p-2 text-sm';

            // Generate unique ID for CKEditor
            editorCounter++;
            const editorId = containerId.includes('add') ? `editorAddDeskripsi${editorCounter}` : `editorEditDeskripsi${editorCounter}`;
            textarea.id = editorId;

            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm self-start';
            deleteBtn.innerHTML = '×';
            deleteBtn.onclick = function() { removeDeskripsiField(this, editorCounter); };

            div.appendChild(textarea);
            div.appendChild(deleteBtn);
            container.appendChild(div);

            // Initialize CKEditor for the new textarea
            setTimeout(() => {
                ClassicEditor.create(document.querySelector(`#${editorId}`))
                    .then(editor => {
                        if (containerId.includes('add')) {
                            addDeskripsiEditors[editorCounter] = editor;
                        } else {
                            editDeskripsiEditors[editorCounter] = editor;
                        }
                    })
                    .catch(error => console.error(`Error initializing editor ${editorId}:`, error));
            }, 100);
        }

        function removeField(button) {
            const container = button.parentElement.parentElement;
            if (container.children.length > 1) {
                button.parentElement.remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Minimal harus ada satu field!'
                });
            }
        }

        function removeDeskripsiField(button, editorIndex) {
            const container = button.parentElement.parentElement;
            if (container.children.length > 1) {
                // Destroy the CKEditor instance
                const isAdd = container.id.includes('add');
                const editorArray = isAdd ? addDeskripsiEditors : editDeskripsiEditors;

                if (editorArray[editorIndex]) {
                    editorArray[editorIndex].destroy();
                    delete editorArray[editorIndex];
                }

                button.parentElement.remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Minimal harus ada satu field deskripsi!'
                });
            }
        }

        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#careerTable tr");
            rows.forEach(row => {
                let position = row.cells[1]?.textContent?.toLowerCase() || '';
                let jobType = row.cells[0]?.textContent?.toLowerCase() || '';
                let description = row.cells[2]?.textContent?.toLowerCase() || '';

                const shouldShow = position.includes(input) || jobType.includes(input) || description.includes(input);
                row.style.display = shouldShow ? "" : "none";
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Career?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteForm');
                    form.action = `/career/${id}`;
                    form.submit();
                }
            });
        }
    </script>
@endsection