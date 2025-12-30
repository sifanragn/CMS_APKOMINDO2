@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-semibold mb-4">Master Industry</h1>

    <form method="POST" action="{{ route('industries.store') }}" class="flex gap-2 mb-6">
        @csrf
        <input type="text" name="name" class="border px-3 py-2 rounded w-64" placeholder="Nama industry">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Tambah</button>
    </form>

    <table class="bg-white w-full text-sm rounded shadow">
        @foreach($data as $row)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $row->name }}</td>
            <td class="px-4 py-2 text-right">
                <form method="POST" action="{{ route('industries.destroy',$row->id) }}">
                    @csrf @method('DELETE')
                    <button class="text-red-600">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
