<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\JenisPembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Dotenv\Exception\ValidationException;

class JenisPembayaranController extends Controller
{
    public function index()
    {
        try {
            $jenisPembayaran = JenisPembayaran::latest()->paginate(10);
            return view('pages.jenis-pembayaran', compact('jenisPembayaran'));
        } catch (Exception $e) {
            Log::error('Error saat mengambil data jenis pembayaran: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data. Silahkan coba lagi.');
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'nama' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'nominal' => 'required|numeric|min:0',
                'dapat_dicicil' => 'required|boolean',
            ], [
                'nama.required' => 'Nama pembayaran harus diisi',
                'nama.max' => 'Nama pembayaran maksimal 255 karakter',
                'nominal.required' => 'Nominal harus diisi',
                'nominal.numeric' => 'Nominal harus berupa angka',
                'nominal.min' => 'Nominal tidak boleh negatif',
                'dapat_dicicil.required' => 'Status cicilan harus dipilih',
            ]);

            JenisPembayaran::create($request->all());
            DB::commit();

            return redirect()->route('jenis-pembayaran.index')
                ->with('success', 'Jenis Pembayaran berhasil ditambahkan');
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('error', ['message' => $e->getMessage()]);
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error saat menambah jenis pembayaran: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $jenisPembayaran = JenisPembayaran::findOrFail($id);

            $request->validate([
                'nama' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'nominal' => 'required|numeric|min:0',
                'dapat_dicicil' => 'required|boolean',
            ], [
                'nama.required' => 'Nama pembayaran harus diisi',
                'nama.max' => 'Nama pembayaran maksimal 255 karakter',
                'nominal.required' => 'Nominal harus diisi',
                'nominal.numeric' => 'Nominal harus berupa angka',
                'nominal.min' => 'Nominal tidak boleh negatif',
                'dapat_dicicil.required' => 'Status cicilan harus dipilih',
            ]);

            $data = $request->all();
            $data['dapat_dicicil'] = filter_var($data['dapat_dicicil'], FILTER_VALIDATE_BOOLEAN);

            $jenisPembayaran->update($data);

            DB::commit();

            return redirect()->route('jenis-pembayaran.index')
                ->with('success', 'Jenis pembayaran berhasil diperbarui');

        } catch (QueryException $e) {
            DB::rollback();
            Log::error('Database Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan database. Silahkan coba lagi.');

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error saat update jenis pembayaran: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan. Silahkan coba lagi.');
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $jenisPembayaran = JenisPembayaran::findOrFail($id);
            $jenisPembayaran->delete();
            DB::commit();

            return redirect()->route('jenis-pembayaran.index')
                ->with('success', 'Jenis pembayaran berhasil dihapus');
        } catch (QueryException $e) {
            DB::rollback();
            Log::error('Database Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan database. Silahkan coba lagi.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error saat menghapus jenis pembayaran: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan. Silahkan coba lagi.');
        }
    }
}