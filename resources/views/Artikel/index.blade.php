@extends('layouts.app')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Artikel</h1>
        <button onclick="openAddModal()"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Tambah Artikel
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm rounded-xl overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Judul</th>
                    <th class="px-4 py-3 text-left">Kategori</th>
                    <th class="px-4 py-3 text-left">Gambar</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $i => $item)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $i+1 }}</td>
                    <td class="px-4 py-3 font-medium">{{ $item->title }}</td>
                    <td class="px-4 py-3">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                            {{ $item->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($item->image)
                            <img src="{{ asset('storage/'.$item->image) }}"
                                 class="w-14 h-14 rounded-lg object-cover border">
                        @else
                            <span class="text-xs text-gray-400">Tidak ada</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 space-x-1">
                        <button
                            onclick="openEditModal(@json($item))"
                            class="px-3 py-1 text-xs bg-blue-50 text-blue-600 rounded">
                            Edit
                        </button>

                        <form method="POST"
                              action="{{ route('artikel.destroy',$item) }}"
                              class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Hapus artikel?')"
                                class="px-3 py-1 text-xs bg-red-50 text-red-600 rounded">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-500">
                        Belum ada artikel
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= MODAL ADD ================= --}}
<div id="addModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
        <h2 class="text-lg font-semibold">Tambah Artikel</h2>

        <form action="{{ route('artikel.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="display" value="1" checked>
                Tampilkan artikel
            </label>

            <div>
                <label class="text-sm font-medium">Judul</label>
                <input type="text" name="title" required
                       class="w-full border rounded p-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
    Kategori <span class="text-red-500">*</span>
</label>

<select
    name="category_id"
    required
    class="w-full bg-white text-gray-800
           border border-gray-300 rounded-lg px-3 py-2 text-sm
           focus:outline-none focus:ring-2 focus:ring-blue-500
           focus:border-blue-500
           appearance-none">

    <option value="" class="text-gray-400 bg-white">
        Pilih kategori
    </option>

    @foreach($categories as $cat)
        <option
            value="{{ $cat->id }}"
            class="bg-white text-gray-800">
            {{ $cat->name }}
        </option>
    @endforeach
</select>

            </div>

            <div>
                <label class="text-sm font-medium">Konten</label>
                <textarea id="editorAdd" name="content" rows="4"
                          class="w-full border rounded p-2 text-sm"></textarea>
            </div>

            <label class="block mb-2 font-medium">Upload Gambar</label>

<input
    type="file"
    name="image"
    id="articleImageInput"
    accept="image/png,image/jpeg,image/gif"
    class="hidden"
    onchange="handleArticleImage(this)"
>

<div
    id="articleDropzone"
    onclick="document.getElementById('articleImageInput').click()"
    class="border-2 border-dashed border-blue-400 rounded-xl p-10 text-center cursor-pointer
           bg-blue-50 hover:bg-blue-100 transition">

    <div id="articleDropzoneContent" class="flex flex-col items-center gap-2">
        <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>

        <p class="text-gray-700 font-medium">
            Klik untuk upload atau drag and drop
        </p>
        <p class="text-sm text-gray-500">
            PNG, JPG, atau GIF (MAX. 2MB)
        </p>
    </div>
</div>

        <div id="addPreview" class="mt-4"></div>


            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="closeAddModal()"
                    class="px-4 py-2 rounded bg-gray-300">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded bg-blue-500 text-white">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL EDIT ================= --}}
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-2xl p-6 space-y-4 overflow-y-auto max-h-screen">
        <h2 class="text-lg font-semibold">Edit Artikel</h2>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="display" id="editDisplay">
                Tampilkan artikel
            </label>

            <div>
                <label class="text-sm font-medium">Judul</label>
                <input type="text" name="title" id="editTitle"
                       class="w-full border rounded p-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kategori <span class="text-red-500">*</span>
                </label>

                <select
                    name="category_id"
                    id="editCategory"
                    required
                    class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm
                        focus:ring-2 focus:ring-blue-500">

                    <option value="">Pilih kategori</option>

                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

                <label class="text-sm font-medium">Konten</label>
                <textarea id="editorEdit" name="content"
                          class="w-full border rounded p-2 text-sm"></textarea>
            </div>

        <label class="block mb-2 font-medium">Upload Gambar</label>

<input
    type="file"
    name="image"
    id="articleImageInput"
    accept="image/png,image/jpeg,image/gif"
    class="hidden"
    onchange="handleArticleImage(this)"
>

<div
    id="articleDropzone"
    onclick="document.getElementById('articleImageInput').click()"
    class="border-2 border-dashed border-blue-400 rounded-xl p-10 text-center cursor-pointer
           bg-blue-50 hover:bg-blue-100 transition">

    <div id="articleDropzoneContent" class="flex flex-col items-center gap-2">
        <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>

        <p class="text-gray-700 font-medium">
            Klik untuk upload atau drag and drop
        </p>
        <p class="text-sm text-gray-500">
            PNG, JPG, atau GIF (MAX. 2MB)
        </p>
    </div>
</div>


        <div id="addPreview" class="mt-4"></div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 rounded bg-gray-300">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded bg-blue-500 text-white">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
let editorAdd, editorEdit;

ClassicEditor.create(document.querySelector('#editorAdd'))
    .then(e => editorAdd = e);

ClassicEditor.create(document.querySelector('#editorEdit'))
    .then(e => editorEdit = e);

function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function openEditModal(data) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editForm').action = `/artikel/${data.id}`;

    document.getElementById('editTitle').value = data.title;
    document.getElementById('editCategory').value = data.category_id;
    document.getElementById('editDisplay').checked = data.display == 1;

    editorEdit.setData(data.content ?? '');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<script>
function handleArticleImage(input) {
    const file = input.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('File harus berupa gambar');
        input.value = '';
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran maksimal 2MB');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('articleDropzoneContent').innerHTML = `
            <div class="relative inline-block">
                <img src="${e.target.result}"
                     class="max-h-64 rounded-lg shadow border object-contain bg-white">

                <button type="button"
                    onclick="removeArticleImage()"
                    class="absolute -top-2 -right-2 bg-red-500 text-white
                           w-7 h-7 rounded-full flex items-center justify-center">
                    ✕
                </button>
            </div>
        `;
    };
    reader.readAsDataURL(file);
}

function removeArticleImage() {
    document.getElementById('articleImageInput').value = '';
    document.getElementById('articleDropzoneContent').innerHTML = `
        <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <p class="text-gray-700 font-medium">
            Klik untuk upload atau drag and drop
        </p>
        <p class="text-sm text-gray-500">
            PNG, JPG, atau GIF (MAX. 2MB)
        </p>
    `;
}
</script>

@endsection
