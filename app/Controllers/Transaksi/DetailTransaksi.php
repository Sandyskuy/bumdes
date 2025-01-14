<?php

namespace App\Controllers\Transaksi;

use App\Models\DetailTransaksiModel;
use App\Models\BarangModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;

class DetailTransaksi extends BaseController
{
    use ResponseTrait;
    protected $detailTransaksiModel;
    protected $barangModel;

    public function __construct()
    {
        $this->detailTransaksiModel = new DetailTransaksiModel();
        $this->barangModel = new BarangModel();
    }

    public function index()
    {
        // Tampilkan halaman detail transaksi, atau daftar detail transaksi jika Anda mempunyai halaman dashboard admin
    }

    public function show($id)
    {
        // Ambil detail transaksi berdasarkan ID
        $detailTransaksi = $this->detailTransaksiModel->find($id);

        if (!$detailTransaksi) {
            return $this->fail(['error' => 'Detail transaction not found.'], 404);
        }

        return $this->respond($detailTransaksi);
    }

    public function getDetailTransaksiByTransaksi($transaksi_id)
    {
        // Lakukan join ke tabel barang untuk mendapatkan nama dan harga barang
        $details = $this->detailTransaksiModel
            ->select('detail_transaksi.*, barang.nama AS nama_barang, barang.harga AS harga_barang')
            ->join('barang', 'barang.id = detail_transaksi.barang_id')
            ->where('detail_transaksi.transaksi_id', $transaksi_id)
            ->findAll();

        if (empty($details)) {
            return $this->fail(['error' => 'No detail transactions found for the given transaction ID.'], 404);
        }

        return $this->respond($details);
    }



}
