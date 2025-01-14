<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'barang'; // Nama tabel

    protected $primaryKey = 'id'; // Primary key tabel

    protected $allowedFields = ['nama', 'deskripsi', 'harga', 'harga_kulak', 'stok', 'gambar', 'kategori_id', 'created_at', 'updated_at'];
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';


    // Relasi dengan tabel Kategori Barang
    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id');
    }
    public function findAllWithKategori()
    {
        return $this->db->query("
        SELECT barang.*, kategori.nama AS nama_kategori
        FROM barang
        JOIN kategori ON barang.kategori_id = kategori.id
    ")->getResult();
    }

    public function getBestSellingProducts()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('detail_transaksi');
        $builder->select('barang.*, SUM(detail_transaksi.jumlah) as total_sold');
        $builder->join('barang', 'detail_transaksi.barang_id = barang.id');
        $builder->groupBy('detail_transaksi.barang_id');
        $builder->orderBy('total_sold', 'DESC');
        $builder->limit(10);

        $query = $builder->get();
        return $query->getResultArray();
    }

}
