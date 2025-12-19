<?php

namespace App\Http\Controllers;

use App\Models\AgendaImage;
use App\Models\Agenda;
use App\Models\AgendaSpeaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;

class AgendaController extends Controller
{

    public function index()
    {
        $agendas = Agenda::with(['speakers', 'extraImages'])->latest()->get();
        $speakers = AgendaSpeaker::all();
        return view('agenda.index', compact('agendas', 'speakers'));
    }

    public function store(Request $request)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'start_datetime' => 'required|date',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'extra_images.*' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'title','description','start_datetime','end_datetime',
            'event_organizer','location','register_link',
            'youtube_link','type','status'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('agenda', 'public');
        }

        $agenda = Agenda::create($data);

        // 🔥 FOTO TAMBAHAN
        if ($request->hasFile('extra_images')) {
            foreach ($request->file('extra_images') as $key => $file) {
                if (!$file) continue;

                $path = $file->store('agenda/extra', 'public');

                AgendaImage::create([
                    'agenda_id' => $agenda->id,
                    'image'     => $path,
                    'title'     => $request->extra_titles[$key] ?? null,
                    'subtitle'  => $request->extra_subtitles[$key] ?? null,
                ]);
            }
        }

        // speakers
        $agenda->speakers()->sync($request->speaker_ids ?? []);

        DB::commit();
        return redirect()->route('agenda.index')->with('success', 'Agenda berhasil ditambahkan');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}


    public function show($id)
    {
        $agenda = Agenda::with(['speakers', 'extraImages'])->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($agenda);
        }

        return view('agenda.show', compact('agenda'));
    }

    public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        $agenda = Agenda::findOrFail($id);

        /* ===============================
           VALIDATION
        =============================== */
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'start_datetime' => 'required|date',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'extra_images.*' => 'nullable|image|max:2048',
            'speaker_ids' => 'nullable|array',
            'speaker_ids.*' => 'integer|exists:agenda_speakers,id',
        ]);

        /* ===============================
           UPDATE DATA UTAMA
        =============================== */
        $data = $request->only([
            'title',
            'description',
            'start_datetime',
            'end_datetime',
            'event_organizer',
            'location',
            'register_link',
            'youtube_link',
            'type',
            'status',
        ]);

        // image utama
        if ($request->hasFile('image')) {
            if ($agenda->image && Storage::disk('public')->exists($agenda->image)) {
                Storage::disk('public')->delete($agenda->image);
            }

            $data['image'] = $request->file('image')->store('agenda', 'public');
        }

        $agenda->update($data);

        /* ===============================
           ❌ DELETE EXTRA IMAGES
        =============================== */
        if ($request->filled('delete_extra_ids')) {
            foreach ($request->delete_extra_ids as $imgId) {
                $img = AgendaImage::find($imgId);
                if (!$img) continue;

                if ($img->image && Storage::disk('public')->exists($img->image)) {
                    Storage::disk('public')->delete($img->image);
                }

                $img->delete();
            }
        }

        /* ===============================
           UPDATE TITLE & SUBTITLE (TANPA FILE)
        =============================== */
        if ($request->has('extra_titles')) {
            foreach ($request->extra_titles as $key => $title) {

                // hanya existing image
                if (!is_numeric($key)) continue;

                $img = AgendaImage::find($key);
                if (!$img) continue;

                $img->update([
                    'title' => $title,
                    'subtitle' => $request->extra_subtitles[$key] ?? $img->subtitle,
                ]);
            }
        }

        /* ===============================
           HANDLE EXTRA IMAGES (ADD / REPLACE)
        =============================== */
        if ($request->hasFile('extra_images')) {
            foreach ($request->file('extra_images') as $key => $file) {
                if (!$file) continue;

                // ➕ FOTO BARU
                if (str_starts_with($key, 'new')) {
                    $path = $file->store('agenda/extra', 'public');

                    AgendaImage::create([
                        'agenda_id' => $agenda->id,
                        'image' => $path,
                        'title' => $request->extra_titles[$key] ?? null,
                        'subtitle' => $request->extra_subtitles[$key] ?? null,
                    ]);
                }

                // 🔁 REPLACE FOTO LAMA
                if (is_numeric($key)) {
                    $img = AgendaImage::find($key);
                    if (!$img) continue;

                    if ($img->image && Storage::disk('public')->exists($img->image)) {
                        Storage::disk('public')->delete($img->image);
                    }

                    $img->update([
                        'image' => $file->store('agenda/extra', 'public'),
                    ]);
                }
            }
        }

        /* ===============================
           SYNC SPEAKERS
        =============================== */
        $agenda->speakers()->sync($request->speaker_ids ?? []);

        DB::commit();

        return redirect()
            ->route('agenda.index')
            ->with('success', 'Agenda berhasil diperbarui');

    } catch (\Throwable $e) {
        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}


    public function destroy($id)
    {
        $agenda = Agenda::findOrFail($id);

        if ($agenda->image) {
            Storage::disk('public')->delete($agenda->image);
        }

        foreach ($agenda->extraImages as $img) {
            if ($img->image && Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }
                $img->delete();
        }

        if ($agenda->image && Storage::disk('public')->exists($agenda->image)) {
            Storage::disk('public')->delete($agenda->image);
        }

        $agenda->speakers()->detach();
        $agenda->delete();


        return response()->json(['message' => 'Agenda deleted successfully.']);
    }

    public function bulkDelete(Request $request)
{
    $ids = $request->ids;

    if (!$ids || count($ids) === 0) {
        return back()->with('error', 'Tidak ada agenda yang dipilih');
    }

    // 🔥 hapus relasi dulu (kalau ada)
    \App\Models\Agenda::whereIn('id', $ids)->each(function ($agenda) {
        // hapus image
        if ($agenda->image && \Storage::disk('public')->exists($agenda->image)) {
            \Storage::disk('public')->delete($agenda->image);
        }

        // detach speaker
        $agenda->speakers()->detach();

        // hapus agenda
        $agenda->delete();
    });

    return back()->with('success', count($ids).' agenda berhasil dihapus');
}

}
