@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-xl font-semibold mb-6">Detail Pendaftaran</h1>

    <div class="bg-white p-6 rounded shadow space-y-4">
        <div><strong>Company:</strong> {{ $item->company }}</div>
        <div><strong>Email:</strong> {{ $item->primary_email }}</div>
        <div><strong>Phone:</strong> {{ $item->primary_phone }}</div>
        <div><strong>Industry:</strong> {{ $item->industry->name ?? '-' }}</div>
        <div><strong>DPD:</strong> {{ $item->dpd->name ?? '-' }}</div>
        <div><strong>Alamat:</strong> {{ $item->street }}, {{ $item->city }}</div>
        <div><strong>NIB / SIUP:</strong> {{ $item->nib_siup }}</div>
        <div><strong>NPWP:</strong> {{ $item->npwp_usaha }}</div>

        @if($item->ktp_pic)
        <div>
            <strong>KTP PIC:</strong>
            <a href="{{ asset('storage/'.$item->ktp_pic) }}" target="_blank"
               class="text-blue-600 underline">
                Lihat Dokumen
            </a>
        </div>
        @endif
    </div>

    <div class="flex gap-4 mt-6">
        <form method="POST" action="{{ url('/member-registrations/'.$item->id.'/approve') }}">
            @csrf
            <button class="bg-green-600 text-white px-5 py-2 rounded">
                Approve
            </button>
        </form>

        <form method="POST" action="{{ url('/member-registrations/'.$item->id.'/reject') }}">
            @csrf
            <textarea name="admin_note"
                class="border rounded px-3 py-2 text-sm"
                placeholder="Alasan penolakan"></textarea>
            <button class="bg-red-600 text-white px-5 py-2 rounded ml-2">
                Reject
            </button>
        </form>
    </div>
</div>
@endsection
