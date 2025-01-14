<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table = 'detail_transaksi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['transaksi_id', 'barang_id', 'jumlah', 'stok_diterima'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    // Definisikan relasi dengan tabel transaksi
    public function transaksi()
    {
        return $this->belongsTo(TransaksiModel::class, 'transaksi_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(UserModel::class, 'pengguna_id');
    }

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }
}
