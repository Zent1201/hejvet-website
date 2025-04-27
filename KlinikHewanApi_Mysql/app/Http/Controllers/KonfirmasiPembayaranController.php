<?php

namespace App\Http\Controllers;

use App\Models\KonfirmasiPembayaran;
use App\Models\Pembayaran;
use App\Models\Konsul;
use App\Models\Service;
use Illuminate\Http\Request;

class KonfirmasiPembayaranController extends Controller
{
    // Menampilkan semua konfirmasi pembayaran
    public function index()
    {
        $konfirmasiPembayarans = KonfirmasiPembayaran::with(['pembayaran', 'konsul', 'service'])->get();

        $data = $konfirmasiPembayarans->map(function ($item) {
            return [
                'id' => $item->id,
                'pembayaran_id' => $item->pembayaran_id,
                'pasien_id' => $item->pasien_id,
                'nama_rekening' => $item->nama_rekening,
                'tgl_bayar' => $item->tgl_bayar,
                'total_bayar' => $this->getTotalBayar($item),
                'metode_pembayaran' => $item->pembayaran ? $item->pembayaran->metode : null,
                'status_konsultasi' => $item->status_konsultasi,
                'status_service' => $item->status_service,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json($data);
    }

    // Mendapatkan total_bayar berdasarkan konsul_id atau service_id
    private function getTotalBayar($item)
    {
        if ($item->konsul_id) {
            $konsul = Konsul::find($item->konsul_id);
            return $konsul ? $konsul->harga : 0;
        } elseif ($item->service_id) {
            $service = Service::find($item->service_id);
            return $service ? $service->harga : 0;
        }
        return 0; // Jika tidak ada, kembalikan 0
    }

    // Menampilkan konfirmasi pembayaran tertentu
    public function show($id)
    {
        $konfirmasiPembayaran = KonfirmasiPembayaran::with(['pembayaran', 'konsul', 'service'])->findOrFail($id);

        return response()->json([
            'id' => $konfirmasiPembayaran->id,
            'pembayaran_id' => $konfirmasiPembayaran->pembayaran_id,
            'pasien_id' => $konfirmasiPembayaran->pasien_id,
            'nama_rekening' => $konfirmasiPembayaran->nama_rekening,
            'tgl_bayar' => $konfirmasiPembayaran->tgl_bayar,
            'total_bayar' => $this->getTotalBayar($konfirmasiPembayaran),
            'metode_pembayaran' => $konfirmasiPembayaran->pembayaran ? $konfirmasiPembayaran->pembayaran->metode : null,
            'status_konsultasi' => $konfirmasiPembayaran->status_konsultasi,
            'status_service' => $konfirmasiPembayaran->status_service,
            'created_at' => $konfirmasiPembayaran->created_at,
            'updated_at' => $konfirmasiPembayaran->updated_at,
        ]);
    }

    // Menyimpan konfirmasi pembayaran baru
    public function store(Request $request)
    {
        $request->validate([
            'pembayaran_id' => 'required|exists:pembayarans,id',
            'pasien_id' => 'required|exists:users,id',
            'nama_rekening' => 'required|string|max:255',
            'tgl_bayar' => 'required|date',
            'konsul_id' => 'nullable|exists:konsuls,id',
            'service_id' => 'nullable|exists:services,id',
        ]);

        // Mengambil harga dari konsultasi atau layanan
        $total_bayar = $this->getTotalBayarFromRequest($request);

        // Menentukan status berdasarkan entitas yang ada
        $status_konsultasi = $request->konsul_id ? 'sukses sudah melakukan pembayaran' : null;
        $status_service = $request->service_id ? 'sukses sudah melakukan pembayaran' : null;

        // Menyimpan data konfirmasi pembayaran
        $konfirmasiPembayaran = KonfirmasiPembayaran::create([
            'pembayaran_id' => $request->pembayaran_id,
            'pasien_id' => $request->pasien_id,
            'nama_rekening' => $request->nama_rekening,
            'tgl_bayar' => $request->tgl_bayar,
            'konsul_id' => $request->konsul_id,
            'service_id' => $request->service_id,
            'status_konsultasi' => $status_konsultasi,
            'status_service' => $status_service,
        ]);

        return response()->json($konfirmasiPembayaran, 201);
    }

    // Mendapatkan total_bayar dari request
    private function getTotalBayarFromRequest($request)
    {
        if ($request->konsul_id) {
            $konsul = Konsul::find($request->konsul_id);
            return $konsul ? $konsul->harga : 0;
        } elseif ($request->service_id) {
            $service = Service::find($request->service_id);
            return $service ? $service->harga : 0;
        }
        return 0; // Jika tidak ada, kembalikan 0
    }

    // Memperbarui konfirmasi pembayaran tertentu (bisa ditambahkan nanti)
    public function update(Request $request, $id)
    {
        // Logika untuk memperbarui konfirmasi pembayaran
    }

    // Menghapus konfirmasi pembayaran tertentu (bisa ditambahkan nanti)
    public function destroy($id)
    {
        // Logika untuk menghapus konfirmasi pembayaran
    }
}
