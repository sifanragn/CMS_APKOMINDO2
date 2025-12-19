@extends('layouts.app')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Kategori Artikel</h1>
        <button onclick="openAddModal()"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Tambah Kategori
        </button>
    </div>

    <table class="min-w-full bg-white border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">No</th>
                <th class="px-4 py-2 border">Nama Kategori</th>
                <th class="px-4 py-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody id="categoryTable">
            @forelse($categories as $i => $cat)
            <tr>
                <td class="px-4 py-2 border">{{ $i+1 }}</td>
                <td class="px-4 py-2 border">{{ $cat->name }}</td>
                <td class="px-4 py-2 border space-x-1">
                    <button onclick="openEditModal(this)"
                        data-item='@json($cat)'
                        class="text-blue-600 text-xs border px-2 py-1 rounded hover:bg-blue-50">
                        Edit
                    </button>

                    <form method="POST"
                        action="{{ route('category-artikel.destroy',$cat) }}"
                        class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Hapus kategori?')"
                            class="text-red-600 text-xs border px-2 py-1 rounded hover:bg-red-50">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center py-6 text-gray-400">
                    Belum ada kategori
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ================= ADD MODAL ================= --}}
<div id="addModal"
  class="hidden fixed inset-0 bg-black/70 backdrop-blur-[1px] flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6 space-y-4">
        <h2 class="text-lg font-semibold">Tambah Kategori Artikel</h2>

        <form action="{{ route('category-artikel.store') }}" method="POST">
            @csrf
            <div>
                <label class="block mb-1 font-medium">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    name="name"
                    required
                    class="w-full border rounded p-2 text-sm"
                    placeholder="Masukkan nama kategori">
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button"
                    onclick="closeAddModal()"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= EDIT MODAL ================= --}}
<div id="editModal"
   class="hidden fixed inset-0 bg-black/70 backdrop-blur-[1px] flex items-center justify-center z-50">

    <div class="bg-white rounded-lg w-full max-w-md p-6 space-y-4">
        <h2 class="text-lg font-semibold">Edit Kategori Artikel</h2>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 font-medium">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text"
                    name="name"
                    id="editName"
                    required
                    class="w-full border rounded p-2 text-sm">
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button"
                    onclick="closeEditModal()"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function openEditModal(button) {
    const data = JSON.parse(button.getAttribute('data-item'));

    const form = document.getElementById('editForm');
    form.action = `/category-artikel/${data.id}`;

    document.getElementById('editName').value = data.name ?? '';

    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

/* ESC close */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});
</script>
@endsection
