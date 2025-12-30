@extends('layouts.app')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-gray-800">
            Pendaftaran Anggota
        </h1>

        <span class="text-sm text-gray-500">
            Total: {{ $data->count() }} pendaftar
        </span>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow border">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-600 uppercase text-xs tracking-wider">
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Company</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Industry</th>
                    <th class="px-4 py-3 text-left">DPD</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($data as $i => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            {{ $i + 1 }}
                        </td>

                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $row->company }}
                        </td>

                        <td class="px-4 py-3 text-gray-600">
                            {{ $row->primary_email }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $row->industry->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $row->dpd->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            @if($row->status === 'pending')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            @elseif($row->status === 'approved')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                    Approved
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                    Rejected
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <a href="{{ route('member-registrations.show', $row->id) }}"
                               class="text-blue-600 hover:underline text-sm font-medium">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                            Belum ada data pendaftaran anggota
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
