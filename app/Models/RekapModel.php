<?php

namespace App\Models;

use CodeIgniter\Model;

class RekapModel extends Model
{
    protected $table = 'rekap';
    protected $primaryKey = 'id';
    protected $allowedFields = ['barang_id', 'bulan', 'tahun', 'stok_awal'];

    public function getRekapByBarangAndMonth($barangId, $bulan, $tahun)
    {
        return $this->where('barang_id', $barangId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();
    }

    public function updateStokAwal($barangId, $bulan, $tahun, $stokAwal)
    {
        $rekap = $this->getRekapByBarangAndMonth($barangId, $bulan, $tahun);

        if ($rekap) {
            $rekap['stok_awal'] = $stokAwal;
            return $this->save($rekap);
        } else {
            return false;
        }
    }
}
