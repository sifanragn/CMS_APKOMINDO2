<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tentangkami;
use App\Models\TentangkamiCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class TentangkamiController extends Controller
{
    public function index()
    {
        try {
            // PERBAIKAN 1: Pastikan model name consistency
            $totalRecords = Tentangkami::count();
            Log::info('Total Tentangkami records in database: ' . $totalRecords);

            // PERBAIKAN 2: Eager loading dengan debugging
            $tentangkami = Tentangkami::with(['category' => function($query) {
                Log::info('Loading category relationship');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

            Log::info('Records fetched for display: ' . $tentangkami->count());

            // PERBAIKAN 3: Enhanced debugging untuk relasi
            foreach ($tentangkami as $index => $item) {
                Log::info("Record {$index}: ID={$item->id}, Title={$item->title}, CategoryID={$item->category_tentangkami_id}");

                // Debug relasi kategori
                if ($item->category) {
                    Log::info("- Has category relationship: {$item->category->nama}");
                } else {
                    Log::warning("- No category relationship found for record {$item->id}");
                    // Cek manual apakah kategori ada
                    $manualCategory = TentangkamiCategory::find($item->category_tentangkami_id);
                    if ($manualCategory) {
                        Log::info("- Manual check found category: {$manualCategory->nama}");
                    } else {
                        Log::error("- Category with ID {$item->category_tentangkami_id} not found in database");
                    }
                }
            }

            // PERBAIKAN 4: Debug kategori yang tersedia
            $categories = TentangkamiCategory::orderBy('nama')->get();
            Log::info('Categories fetched: ' . $categories->count());

            foreach ($categories as $category) {
                Log::info("Available Category - ID: {$category->id}, Name: {$category->nama}");
            }

            // PERBAIKAN 5: Cek foreign key constraint
            $orphanedRecords = Tentangkami::whereNotIn('category_tentangkami_id',
                TentangkamiCategory::pluck('id'))->get();

            if ($orphanedRecords->count() > 0) {
                Log::warning('Found orphaned records (invalid category_id): ' . $orphanedRecords->count());
                foreach ($orphanedRecords as $orphan) {
                    Log::warning("Orphaned record ID {$orphan->id} has invalid category_id: {$orphan->category_tentangkami_id}");
                }
            }

            return view('tentangkami.index', compact('tentangkami', 'categories'));

        } catch (\Exception $e) {
            Log::error('Error in TentangkamiController@index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $tentangkami = collect();
            $categories = collect();

            return view('tentangkami.index', compact('tentangkami', 'categories'))
                ->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store request data:', $request->all());
            Log::info('Files:', $request->allFiles());

            $request->validate([
                'title' => 'required|string|max:255',
                'category_tentangkami_id' => 'required|exists:tentangkami_categories,id',
                'description' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'display_on_home' => 'sometimes|boolean',
            ]);

            // PERBAIKAN 6: Validasi kategori exists sebelum create
            $categoryExists = TentangkamiCategory::find($request->category_tentangkami_id);
            if (!$categoryExists) {
                Log::error('Category not found: ' . $request->category_tentangkami_id);
                return redirect()->back()
                    ->with('error', 'Kategori yang dipilih tidak valid.')
                    ->withInput();
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file && $file->isValid()) {
                    Log::info('Processing image upload...');
                    $path = $file->store('tentangkami', 'public');
                    $imagePath = 'storage/' . $path;
                    Log::info('Image stored at: ' . $imagePath);
                }
            }

            $dataToStore = [
                'title' => $request->title,
                'category_tentangkami_id' => $request->category_tentangkami_id,
                'description' => $request->description,
                'image' => $imagePath,
                'display_on_home' => $request->has('display_on_home') ? 1 : 0,
            ];
            Log::info('Data to store:', $dataToStore);

            $tentangkami = Tentangkami::create($dataToStore);
            Log::info('Record created with ID: ' . $tentangkami->id);

            // PERBAIKAN 7: Verify with relationship
            $verifyRecord = Tentangkami::with('category')->find($tentangkami->id);
            if ($verifyRecord) {
                if ($verifyRecord->category) {
                    Log::info('Verified record exists with category: ' . $verifyRecord->category->nama);
                } else {
                    Log::error('Record created but category relationship failed!');
                }
            } else {
                Log::error('Record not found after creation!');
            }

            return redirect()->route('tentangkami.index')->with('success', 'Data berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation errors:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error in TentangkamiController@store: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('Update request for ID: ' . $id);
            Log::info('Update request data:', $request->all());

            $tentangkami = Tentangkami::findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'category_tentangkami_id' => 'required|exists:tentangkami_categories,id',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'display_on_home' => 'sometimes|boolean',
            ]);

            $updateData = [
                'title' => $request->title,
                'category_tentangkami_id' => $request->category_tentangkami_id,
                'description' => $request->description,
                'display_on_home' => $request->has('display_on_home') ? 1 : 0,
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file && $file->isValid()) {
                    if ($tentangkami->image && File::exists(public_path($tentangkami->image))) {
                        File::delete(public_path($tentangkami->image));
                        Log::info('Old image deleted:', ['path' => $tentangkami->image]);
                    }

                    $path = $file->store('tentangkami', 'public');
                    $updateData['image'] = 'storage/' . $path;
                    Log::info('New image uploaded:', ['path' => $updateData['image']]);
                }
            }

            Log::info('Update data:', $updateData);
            $tentangkami->update($updateData);
            Log::info('Record updated successfully');

            return redirect()->route('tentangkami.index')->with('success', 'Data berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation errors:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error in TentangkamiController@update: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            Log::info('Deleting record with ID: ' . $id);
            $tentangkami = Tentangkami::findOrFail($id);

            if ($tentangkami->image && File::exists(public_path($tentangkami->image))) {
                File::delete(public_path($tentangkami->image));
                Log::info('Image deleted:', ['path' => $tentangkami->image]);
            }

            $tentangkami->delete();
            Log::info('Record deleted successfully');

            return redirect()->route('tentangkami.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error in TentangkamiController@destroy: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    // API Methods
    public function getByCategory($categoryId)
    {
        try {
            $request = request();
            $request->validate([
                'category_id' => 'sometimes|exists:tentangkami_categories,id'
            ]);

            $categoryId = $categoryId ?? $request->category_id;

            $tentangkami = Tentangkami::with('category')
                ->where('category_tentangkami_id', $categoryId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tentangkami
            ]);
        } catch (\Exception $e) {
            Log::error('Error in TentangkamiController@getByCategory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.'
            ], 500);
        }
    }

    public function getByCategoryName($categoryName)
    {
        try {
            $category = TentangkamiCategory::where('nama', $categoryName)->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $tentangkami = Tentangkami::with('category')
                ->where('category_tentangkami_id', $category->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tentangkami
            ]);
        } catch (\Exception $e) {
            Log::error('Error in TentangkamiController@getByCategoryName: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.'
            ], 500);
        }
    }

    public function getDisplayOnHome()
    {
        try {
            $tentangkami = Tentangkami::with('category')
                ->where('display_on_home', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tentangkami
            ]);
        } catch (\Exception $e) {
            Log::error('Error in TentangkamiController@getDisplayOnHome: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.'
            ], 500);
        }
    }
    public function toggle(Request $request, $id)
{
    $item = TentangKami::findOrFail($id);

    $item->display_on_home = $request->display_on_home;
    $item->save();

    return response()->json([
        'message' => $item->display_on_home ? 'Ditampilkan di home' : 'Disembunyikan dari home'
    ]);
}

}
