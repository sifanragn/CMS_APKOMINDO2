<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\Request;

class ApiAgendaController extends Controller
{

    public function index()
    {
        try {
            $agendas = Agenda::with('speakers')
                ->latest()
                ->get()
                ->map(function ($agenda) {

                    // Tambahkan URL gambar agenda
                    $agenda->image_url = $agenda->image
                        ? asset('storage/' . $agenda->image)
                        : null;

                    // Tambahkan URL gambar setiap speaker
                    $agenda->speakers->map(function ($speaker) {
                        $speaker->image_url = $speaker->image
                            ? asset('storage/' . $speaker->image)
                            : null;
                        return $speaker;
                    });

                    return $agenda;
                });

            return response()->json([
                'success' => true,
                'message' => 'List agenda',
                'data' => $agendas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data agenda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $agenda = Agenda::with('speakers')->find($id);

            if (!$agenda) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agenda tidak ditemukan',
                ], 404);
            }

            // Tambahkan URL gambar agenda
            $agenda->image_url = $agenda->image
                ? asset('storage/' . $agenda->image)
                : null;

            // Tambahkan URL gambar speaker
            $agenda->speakers->map(function ($speaker) {
                $speaker->image_url = $speaker->image
                    ? asset('storage/' . $speaker->image)
                    : null;
                return $speaker;
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail agenda',
                'data' => $agenda
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data agenda',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
