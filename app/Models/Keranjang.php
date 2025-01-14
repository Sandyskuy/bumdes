<?php

namespace App\Models;

use CodeIgniter\Model;

class Keranjang extends Model
{
    protected $table            = 'keranjang';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pengguna_id', "barang_id", "jumlah", "harga"];

    protected bool $allowEmptyInserts = false;
    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Relasi Many-to-One ke model User
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'pengguna_id', 'id');
    }

    // Relasi Many-to-One ke model Barang
    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'id');
    }
}
