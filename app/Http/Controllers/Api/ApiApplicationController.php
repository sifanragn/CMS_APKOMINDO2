<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiApplicationController extends Controller
{
    /**
     * GET: Ambil semua lamaran kerja (paginate)
     */
    public function index()
    {
        try {
            $applications = Application::with('career:id,position_title')
                ->latest()
                ->paginate(10);

            // Tambahkan file_url
            $applications->getCollection()->transform(function ($item) {
                $item->file_url = $item->file 
                    ? asset('storage/' . $item->file)
                    : null;
                return $item;
            });

            return response()->json([
                'status' => true,
                'message' => 'Daftar lamaran berhasil diambil',
                'data' => $applications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data lamaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET: Detail lamaran kerja
     */
    public function show($id)
    {
        try {
            $application = Application::with('career:id,position_title')->findOrFail($id);

            // Tambahkan file_url
            $application->file_url = $application->file
                ? asset('storage/' . $application->file)
                : null;

            return response()->json([
                'status' => true,
                'message' => 'Detail lamaran berhasil diambil',
                'data' => $application
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil detail lamaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST: Kirim lamaran kerja
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'career_id' => 'required|exists:careers,id',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_telepon' => 'required|string|max:20',
            'cover_letter' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only([
                'career_id', 'nama', 'email', 'no_telepon', 'cover_letter'
            ]);

            // Upload file jika ada
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('applications', $filename, 'public');
                $data['file'] = $path;
            }

            $application = Application::create($data);
            $application->load('career:id,position_title');

            // Tambahkan file_url
            $application->file_url = $application->file
                ? asset('storage/' . $application->file)
                : null;

            return response()->json([
                'status' => true,
                'message' => 'Lamaran berhasil dikirim!',
                'data' => $application
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengirim lamaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
