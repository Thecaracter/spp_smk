<?php
namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JurusanController extends Controller
{
    public function index()
    {
        try {
            $jurusans = Jurusan::withCount('users')->get();
            return view('pages.jurusan', compact('jurusans'));
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menampilkan data');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_jurusan' => 'required|string|max:255|unique:jurusan'
            ]);

            Jurusan::create($validated);
            Log::info('Jurusan created: ' . $validated['nama_jurusan']);

            return back()->with('success', 'Berhasil menambahkan jurusan');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan jurusan');
        }
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        try {
            $validated = $request->validate([
                'nama_jurusan' => "required|string|max:255|unique:jurusan,nama_jurusan,{$jurusan->id}"
            ]);

            $jurusan->update($validated);
            Log::info('Jurusan updated: ' . $jurusan->nama_jurusan);

            return back()->with('success', 'Berhasil memperbarui jurusan');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui jurusan');
        }
    }

    public function destroy(Jurusan $jurusan)
    {
        try {
            $nama = $jurusan->nama_jurusan;
            $jurusan->delete();
            Log::info('Jurusan deleted: ' . $nama);

            return back()->with('success', 'Berhasil menghapus jurusan');
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus jurusan');
        }
    }
}