@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-semibold mb-4">Master Lokasi DPD</h1>

    <form method="POST"
          action="{{ route('dpd-locations.store') }}"
          class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        @csrf

        <input
            type="text"
            name="name"
            class="border px-3 py-2 rounded"
            placeholder="Nama DPD"
            required
        >

        <input
            type="email"
            name="email"
            class="border px-3 py-2 rounded"
            placeholder="Email (opsional)"
        >

        <input
            type="text"
            name="phone"
            class="border px-3 py-2 rounded"
            placeholder="No. Telepon (opsional)"
        >

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Tambah
        </button>
    </form>

    <table class="bg-white w-full text-sm rounded shadow">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Nama DPD</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Telepon</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($data as $row)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $row->name }}</td>
                <td class="px-4 py-2">{{ $row->email ?? '-' }}</td>
                <td class="px-4 py-2">{{ $row->phone ?? '-' }}</td>
                <td class="px-4 py-2 text-right">
                    <form method="POST"
                          action="{{ route('dpd-locations.destroy',$row->id) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
