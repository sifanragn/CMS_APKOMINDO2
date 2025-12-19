<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\Request;

class ApiAgendaController extends Controller
{
    /**
     * List agenda
     */
    public function index()
    {
        try {
            $agendas = Agenda::with(['speakers', 'extraImages'])
                ->latest()
                ->get()
                ->map(function ($agenda) {

                    // ========================
                    // FOTO UTAMA AGENDA
                    // ========================
                    $agenda->image_url = $agenda->image
                        ? asset('storage/' . $agenda->image)
                        : null;

                    // ========================
                    // PEMBICARA
                    // ========================
                    $agenda->speakers = $agenda->speakers->map(function ($speaker) {
                        $speaker->image_url = $speaker->image
                            ? asset('storage/' . $speaker->image)
                            : null;
                        return $speaker;
                    });

                    // ========================
                    // GALERI FOTO TAMBAHAN
                    // ========================
                    $agenda->extra_images = $agenda->extraImages->map(function ($img) {
                        return [
                            'id'        => $img->id,
                            'title'     => $img->title,
                            'subtitle'  => $img->subtitle,
                            'image_url' => asset('storage/' . $img->image),
                        ];
                    });

                    // Hapus relasi mentah biar clean
                    unset($agenda->extraImages);

                    return $agenda;
                });

            return response()->json([
                'success' => true,
                'message' => 'List agenda',
                'data'    => $agendas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data agenda',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail agenda
     */
    public function show($id)
    {
        try {
            $agenda = Agenda::with(['speakers', 'extraImages'])->find($id);

            if (!$agenda) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agenda tidak ditemukan',
                ], 404);
            }

            // ========================
            // FOTO UTAMA
            // ========================
            $agenda->image_url = $agenda->image
                ? asset('storage/' . $agenda->image)
                : null;

            // ========================
            // PEMBICARA
            // ========================
            $agenda->speakers = $agenda->speakers->map(function ($speaker) {
                $speaker->image_url = $speaker->image
                    ? asset('storage/' . $speaker->image)
                    : null;
                return $speaker;
            });

            // ========================
            // GALERI FOTO TAMBAHAN
            // ========================
            $agenda->extra_images = $agenda->extraImages->map(function ($img) {
                return [
                    'id'        => $img->id,
                    'title'     => $img->title,
                    'subtitle'  => $img->subtitle,
                    'image_url' => asset('storage/' . $img->image),
                ];
            });

            unset($agenda->extraImages);

            return response()->json([
                'success' => true,
                'message' => 'Detail agenda',
                'data'    => $agenda
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data agenda',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
