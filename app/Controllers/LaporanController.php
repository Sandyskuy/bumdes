<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\UserModel;
use App\Models\DetailTransaksiModel;
use App\Models\BarangModel;
use App\Models\RekapModel;
use CodeIgniter\API\ResponseTrait;
use DateTime;

class LaporanController extends BaseController
{
    use ResponseTrait;

    public function laporanKeuangan($filter = null, $via = null, $year = null)
    {
        $tanggalAwal = null;
        $tanggalAkhir = null;
        $currentYear = $year ?? date('Y'); // Use the provided year or default to the current year

        // Tentukan tanggal awal dan akhir berdasarkan filter
        switch ($filter) {
            case 'januari':
                $tanggalAwal = new DateTime("first day of January $currentYear");
                $tanggalAkhir = new DateTime("last day of January $currentYear");
                break;
            case 'februari':
                $tanggalAwal = new DateTime("first day of February $currentYear");
                $tanggalAkhir = new DateTime("last day of February $currentYear");
                break;
            case 'maret':
                $tanggalAwal = new DateTime("first day of March $currentYear");
                $tanggalAkhir = new DateTime("last day of March $currentYear");
                break;
            case 'april':
                $tanggalAwal = new DateTime("first day of April $currentYear");
                $tanggalAkhir = new DateTime("last day of April $currentYear");
                break;
            case 'mei':
                $tanggalAwal = new DateTime("first day of May $currentYear");
                $tanggalAkhir = new DateTime("last day of May $currentYear");
                break;
            case 'juni':
                $tanggalAwal = new DateTime("first day of June $currentYear");
                $tanggalAkhir = new DateTime("last day of June $currentYear");
                break;
            case 'juli':
                $tanggalAwal = new DateTime("first day of July $currentYear");
                $tanggalAkhir = new DateTime("last day of July $currentYear");
                break;
            case 'agustus':
                $tanggalAwal = new DateTime("first day of August $currentYear");
                $tanggalAkhir = new DateTime("last day of August $currentYear");
                break;
            case 'september':
                $tanggalAwal = new DateTime("first day of September $currentYear");
                $tanggalAkhir = new DateTime("last day of September $currentYear");
                break;
            case 'oktober':
                $tanggalAwal = new DateTime("first day of October $currentYear");
                $tanggalAkhir = new DateTime("last day of October $currentYear");
                break;
            case 'november':
                $tanggalAwal = new DateTime("first day of November $currentYear");
                $tanggalAkhir = new DateTime("last day of November $currentYear");
                break;
            case 'desember':
                $tanggalAwal = new DateTime("first day of December $currentYear");
                $tanggalAkhir = new DateTime("last day of December $currentYear");
                break;
            case 'tahun':
                $tanggalAwal = new DateTime("first day of January $currentYear");
                $tanggalAkhir = new DateTime("last day of December $currentYear");
                break;
            default:
                // Jika filter tidak disediakan, ambil semua data
                $tanggalAwal = null;
                $tanggalAkhir = null;
                break;
        }

        // Mendapatkan data transaksi berdasarkan rentang tanggal
        $transaksiModel = new TransaksiModel();
        if ($tanggalAwal && $tanggalAkhir) {
            $transaksiModel->where('tanggal >=', $tanggalAwal->format('Y-m-01 00:00:00'))
                ->where('tanggal <=', $tanggalAkhir->format('Y-m-t 23:59:59'));
        }

        // Menambahkan filter "via" jika diberikan
        if ($via !== null) {
            $transaksiModel->where('via', $via);
        }

        // Mengambil semua transaksi jika tidak ada filter yang diberikan
        $transaksi = $transaksiModel->findAll();

        // Mendapatkan detail transaksi untuk menghitung stok yang sudah dibeli
        $detailTransaksiModel = new DetailTransaksiModel();
        $transaksiWithStok = []; // Inisialisasi array untuk menyimpan transaksi yang sudah dimodifikasi

        foreach ($transaksi as $trx) {
            $stokDibeli = []; // Inisialisasi array untuk menyimpan informasi stok yang dibeli
            // Lakukan kloning transaksi untuk menghindari perubahan langsung
            $trxClone = $trx;

            // Mendapatkan detail transaksi berdasarkan ID transaksi
            $detailTransaksi = $detailTransaksiModel->where('transaksi_id', $trx['id'])->findAll();

            // Menghitung stok yang dibeli untuk setiap barang dalam transaksi
            foreach ($detailTransaksi as $dt) {
                $barangId = $dt['barang_id'];
                $jumlah = $dt['jumlah'];

                // Mendapatkan informasi barang dari database berdasarkan ID barang
                $barangModel = new BarangModel();
                $barang = $barangModel->find($barangId);

                // Menambahkan nama barang beserta jumlah yang dibeli ke dalam array $stokDibeli
                if ($barang) {
                    $namaBarang = $barang['nama']; // Misalnya, kolom yang menyimpan nama barang adalah 'nama'
                    if (!isset($stokDibeli[$barangId])) {
                        $stokDibeli[$barangId] = ['nama' => $namaBarang, 'jumlah' => $jumlah];
                    } else {
                        $stokDibeli[$barangId]['jumlah'] += $jumlah;
                    }
                }
            }

            // Menambahkan informasi stok yang dibeli ke dalam entri transaksi
            $trxClone['stok_dibeli'] = $stokDibeli;

            // Menambahkan transaksi yang telah dimodifikasi ke dalam array baru
            $transaksiWithStok[] = $trxClone;
        }

        // Mengembalikan response dalam format JSON
        return $this->respond([
            'laporan' => $transaksiWithStok
        ], 200);
    }
    private function hitungStokDibeli($transaksi)
    {
        $stokDibeli = [];
        $detailTransaksiModel = new DetailTransaksiModel();
        foreach ($transaksi as $trx) {
            $detailTransaksi = $detailTransaksiModel->where('transaksi_id', $trx['id'])->findAll();
            foreach ($detailTransaksi as $dt) {
                $barangId = $dt['barang_id'];
                $jumlah = $dt['jumlah'];

                // Menambahkan jumlah barang yang dibeli ke dalam array $stokDibeli
                if (!isset($stokDibeli[$barangId])) {
                    $stokDibeli[$barangId] = $jumlah;
                } else {
                    $stokDibeli[$barangId] += $jumlah;
                }
            }
        }
        return $stokDibeli;
    }
    private function hitungTotalPembelian($transaksi)
    {
        $totalPembelian = 0;
        foreach ($transaksi as $trx) {
            $totalPembelian += $trx['total'];
        }
        return $totalPembelian;
    }
    public function totalPembelian()
    {
        $transaksiModel = new TransaksiModel();
        $transaksi = $transaksiModel->where('status', 1)->findAll();

        $totalPembelian = 0;
        foreach ($transaksi as $trx) {
            $totalPembelian += $trx['total'];
        }

        return $this->respond([
            'total_pembelian' => $totalPembelian
        ], 200);
    }
    public function totalPembelianOnline()
    {
        $transaksiModel = new TransaksiModel();
        $transaksi = $transaksiModel->where('status', 1)->where('via', 'online')->findAll();

        $totalPembelian = 0;
        foreach ($transaksi as $trx) {
            $totalPembelian += $trx['total'];
        }

        return $this->respond([
            'total_pembelian' => $totalPembelian
        ], 200);
    }
    public function totalPembelianOffline()
    {
        $transaksiModel = new TransaksiModel();
        $transaksi = $transaksiModel->where('status', 1)->where('via', 'offline')->findAll();

        $totalPembelian = 0;
        foreach ($transaksi as $trx) {
            $totalPembelian += $trx['total'];
        }

        return $this->respond([
            'total_pembelian' => $totalPembelian
        ], 200);
    }
    public function totalPembelianPerBulan()
    {
        // Array for storing total purchases per month
        $totalPembelianPerBulan = [];
        $totalOnline = [];
        $totalOffline = [];

        // Array for storing month names
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Initialize total purchases per month with 0
        foreach ($namaBulan as $bulan) {
            $totalPembelianPerBulan[$bulan] = 0;
            $totalOnline[$bulan] = 0;
            $totalOffline[$bulan] = 0;
        }

        // Get all transactions with status 1
        $transaksiModel = new TransaksiModel();
        $transaksi = $transaksiModel->where('status', 1)->findAll();

        // Calculate total purchases per month
        foreach ($transaksi as $trx) {
            // Get month from transaction date
            $bulan = date('n', strtotime($trx['tanggal']));

            // Add transaction total to the respective month
            $totalPembelianPerBulan[$namaBulan[$bulan]] += $trx['total'];

            // Check transaction type and add to respective total
            if ($trx['via'] == 'online') {
                $totalOnline[$namaBulan[$bulan]] += $trx['total'];
            } else {
                $totalOffline[$namaBulan[$bulan]] += $trx['total'];
            }
        }

        // Return response in JSON format
        return $this->respond([
            'total' => $totalPembelianPerBulan,
            'total_online' => $totalOnline,
            'total_offline' => $totalOffline
        ], 200);
    }
    public function totalBuyer()
    {
        // Mendapatkan jumlah pengguna dengan role buyer
        $userModel = new UserModel();
        $totalBuyer = $userModel->where('role', 'buyer')->countAllResults();

        // Mengembalikan response dalam format JSON
        return $this->respond([
            'total_buyer' => $totalBuyer
        ], 200);
    }
    public function totalStaff()
    {
        // Mendapatkan jumlah pengguna dengan role buyer
        $userModel = new UserModel();
        $totalStaff = $userModel->where('role', 'staff')->countAllResults();

        // Mengembalikan response dalam format JSON
        return $this->respond([
            'total_staff' => $totalStaff
        ], 200);
    }
    public function jumlahTransaksi()
    {
        // Mendapatkan jumlah transaksi dengan status 1
        $transaksiModel = new TransaksiModel();
        $countTransaksi = $transaksiModel->where('status', 1)->countAllResults();

        // Mengembalikan response dalam format JSON
        return $this->respond([
            'count_transaksi' => $countTransaksi
        ], 200);
    }
    public function rekap()
    {
        // Array untuk menyimpan ringkasan stok per bulan berdasarkan ID barang
        $rekapStokPerBulan = [];

        // Mendapatkan semua ID barang dari database
        $barangModel = new BarangModel();
        $semuaBarang = $barangModel->findAll();

        // Mendapatkan bulan-bulan dalam format nama bulan
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Menginisialisasi ringkasan stok per bulan untuk setiap barang
        foreach ($semuaBarang as $barang) {
            $idBarang = $barang['id'];
            $rekapStokPerBulan[$idBarang] = [];

            foreach ($namaBulan as $bulan) {
                $rekapStokPerBulan[$idBarang][$bulan]['terjual'] = 0;
                $rekapStokPerBulan[$idBarang][$bulan]['sisa'] = $barang['stok'];
            }
        }

        // Mengambil semua transaksi dengan status 1 dari database menggunakan TransaksiModel
        $transaksiModel = new TransaksiModel();
        $transaksi = $transaksiModel->where('status', 1)->findAll();

        // Mengambil semua detail transaksi
        $detailTransaksiModel = new DetailTransaksiModel();

        // Menghitung stok keluar dan sisa stok per bulan berdasarkan ID barang
        foreach ($transaksi as $trx) {
            // Ekstrak bulan dari tanggal transaksi
            $bulan = date('n', strtotime($trx['tanggal']));

            // Mengambil detail transaksi untuk transaksi saat ini
            $detailTransaksi = $detailTransaksiModel->where('transaksi_id', $trx['id'])->findAll();

            // Menghitung stok keluar dan memperbarui sisa stok untuk setiap barang
            foreach ($detailTransaksi as $dt) {
                $idBarang = $dt['barang_id'];
                $jumlah = $dt['jumlah'];

                // Memperbarui stok keluar dan sisa stok untuk bulan dan barang saat ini
                $rekapStokPerBulan[$idBarang][$namaBulan[$bulan]]['terjual'] += $jumlah;
                $rekapStokPerBulan[$idBarang][$namaBulan[$bulan]]['sisa'] -= $jumlah;
            }
        }

        // Merespons dengan ringkasan stok per bulan berdasarkan ID barang dalam format JSON
        return $this->respond([
            'rekap_stok_per_bulan' => $rekapStokPerBulan
        ], 200);
    }
    // public function rekapForTable($filter = null)
    // {
    //     // Ambil tanggal awal dan tanggal akhir berdasarkan filter
    //     list($tanggalAwal, $tanggalAkhir) = $this->getDateRange($filter);

    //     // Initialize models
    //     $barangModel = new BarangModel();
    //     $transaksiModel = new TransaksiModel();
    //     $detailTransaksiModel = new DetailTransaksiModel();
    //     $rekapModel = new RekapModel();

    //     // Fetch all items
    //     $semuaBarang = $barangModel->findAll();

    //     // Initialize the rekap array
    //     $rekapForTable = [];
    //     foreach ($semuaBarang as $barang) {
    //         $rekapForTable[$barang['id']] = [
    //             'id' => $barang['id'],
    //             'nama' => $barang['nama'],
    //             'stok_awal' => 0, // Inisialisasi stok awal menjadi 0
    //             'harga' => $barang['harga'],
    //             'terjual' => 0,
    //             'sisa' => 0,
    //             'nilai_sisa' => 0
    //         ];
    //     }

    //     // Fetch transactions based on the date range
    //     $transaksiQuery = $transaksiModel->where('status', 1);
    //     if ($tanggalAwal && $tanggalAkhir) {
    //         $transaksiQuery->where('tanggal >=', $tanggalAwal->format('Y-m-d 00:00:00'))
    //             ->where('tanggal <=', $tanggalAkhir->format('Y-m-d 23:59:59'));
    //     }
    //     $transaksi = $transaksiQuery->findAll();

    //     // Process each transaction and its details
    //     foreach ($transaksi as $trx) {
    //         $detailTransaksi = $detailTransaksiModel->where('transaksi_id', $trx['id'])->findAll();
    //         foreach ($detailTransaksi as $dt) {
    //             $idBarang = $dt['barang_id'];
    //             $jumlah = $dt['jumlah'];

    //             // Update terjual
    //             $rekapForTable[$idBarang]['terjual'] += $jumlah;
    //         }
    //     }

    //     // Fetch stok awal from rekap table
    //     foreach ($semuaBarang as $barang) {
    //         $idBarang = $barang['id'];
    //         $bulanIni = date('m');
    //         $tahunIni = date('Y');
    //         $rekap = $rekapModel->getRekapByBarangAndMonth($idBarang, $bulanIni, $tahunIni);

    //         // Update stok awal, sisa, dan nilai sisa
    //         if ($rekap) {
    //             $rekapForTable[$idBarang]['stok_awal'] = $rekap['stok_awal'];
    //             $rekapForTable[$idBarang]['sisa'] = $rekap['stok_awal'] - $rekapForTable[$idBarang]['terjual'];
    //             $rekapForTable[$idBarang]['nilai_sisa'] = $rekapForTable[$idBarang]['sisa'] * $barang['harga'];
    //         }
    //     }

    //     // Respond with the rekap data
    //     return $this->respond(['rekap' => array_values($rekapForTable)], 200);
    // }


    private function getDateRange($filter)
    {
        $currentYear = date('Y');
        $tanggalAwal = null;
        $tanggalAkhir = null;

        switch ($filter) {
            case 'januari':
                $tanggalAwal = new DateTime("first day of January $currentYear");
                $tanggalAkhir = new DateTime("last day of January $currentYear");
                break;
            case 'februari':
                $tanggalAwal = new DateTime("first day of February $currentYear");
                $tanggalAkhir = new DateTime("last day of February $currentYear");
                break;
            case 'maret':
                $tanggalAwal = new DateTime("first day of March $currentYear");
                $tanggalAkhir = new DateTime("last day of March $currentYear");
                break;
            case 'april':
                $tanggalAwal = new DateTime("first day of April $currentYear");
                $tanggalAkhir = new DateTime("last day of April $currentYear");
                break;
            case 'mei':
                $tanggalAwal = new DateTime("first day of May $currentYear");
                $tanggalAkhir = new DateTime("last day of May $currentYear");
                break;
            case 'juni':
                $tanggalAwal = new DateTime("first day of June $currentYear");
                $tanggalAkhir = new DateTime("last day of June $currentYear");
                break;
            case 'juli':
                $tanggalAwal = new DateTime("first day of July $currentYear");
                $tanggalAkhir = new DateTime("last day of July $currentYear");
                break;
            case 'agustus':
                $tanggalAwal = new DateTime("first day of August $currentYear");
                $tanggalAkhir = new DateTime("last day of August $currentYear");
                break;
            case 'september':
                $tanggalAwal = new DateTime("first day of September $currentYear");
                $tanggalAkhir = new DateTime("last day of September $currentYear");
                break;
            case 'oktober':
                $tanggalAwal = new DateTime("first day of October $currentYear");
                $tanggalAkhir = new DateTime("last day of October $currentYear");
                break;
            case 'november':
                $tanggalAwal = new DateTime("first day of November $currentYear");
                $tanggalAkhir = new DateTime("last day of November $currentYear");
                break;
            case 'desember':
                $tanggalAwal = new DateTime("first day of December $currentYear");
                $tanggalAkhir = new DateTime("last day of December $currentYear");
                break;
            case 'tahun':
                $tanggalAwal = new DateTime("first day of January $currentYear");
                $tanggalAkhir = new DateTime("last day of December $currentYear");
                break;
            default:
                // If no filter or invalid filter, use null dates to fetch all data
                $tanggalAwal = null;
                $tanggalAkhir = null;
                break;
        }

        return [$tanggalAwal, $tanggalAkhir];
    }
    public function createRekapFromUpdatedAt($barangId)
    {
        // Ambil data barang dari ID
        $barangModel = new BarangModel();
        $barang = $barangModel->find($barangId);

        if (!$barang) {
            // Jika barang tidak ditemukan, kembalikan false
            return false;
        }

        // Ekstrak bulan dan tahun dari updated_at
        $updatedDate = new DateTime($barang['updated_at']);
        $bulan = $updatedDate->format('m');
        $tahun = $updatedDate->format('Y');

        // Masukkan data ke dalam tabel rekap
        $stokAwal = $barang['stok'];

        // Initialize RekapModel
        $rekapModel = new RekapModel();

        // Insert data into rekap table
        return $rekapModel->insert([
            'barang_id' => $barangId,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'stok_awal' => $stokAwal
        ]);
    }
    public function rekapForTable($filter = null, $via = null)
    {
        list($tanggalAwal, $tanggalAkhir) = $this->getDateRange($filter);

        // Initialize models
        $barangModel = new BarangModel();
        $transaksiModel = new TransaksiModel();
        $detailTransaksiModel = new DetailTransaksiModel();

        // Fetch all items
        $semuaBarang = $barangModel->findAll();

        // Initialize the rekap array
        $rekapForTable = [];
        foreach ($semuaBarang as $barang) {
            $rekapForTable[$barang['id']] = [
                'id' => $barang['id'],
                'nama' => $barang['nama'],
                'stok_awal' => $barang['stok'],
                'harga' => $barang['harga'],
                'terjual' => 0,
                'sisa' => $barang['stok'],
                'nilai_sisa' => $barang['stok'] * $barang['harga']
            ];
        }

        // Fetch transactions based on the date range
        $transaksiQuery = $transaksiModel->where('status', 1);
        if ($tanggalAwal && $tanggalAkhir) {
            $transaksiQuery->where('tanggal >=', $tanggalAwal->format('Y-m-d 00:00:00'))
                ->where('tanggal <=', $tanggalAkhir->format('Y-m-d 23:59:59'));
        }
        // Menambahkan filter "via" jika diberikan
        if ($via !== null) {
            $transaksiModel->where('via', $via);
        }

        // Mengambil semua transaksi jika tidak ada filter yang diberikan
        $transaksi = $transaksiModel->findAll();

        // Process each transaction and its details
        foreach ($transaksi as $trx) {
            $detailTransaksi = $detailTransaksiModel->where('transaksi_id', $trx['id'])->findAll();
            foreach ($detailTransaksi as $dt) {
                $idBarang = $dt['barang_id'];
                $jumlah = $dt['jumlah'];

                if (isset($rekapForTable[$idBarang])) {
                    $rekapForTable[$idBarang]['terjual'] += $jumlah;
                    $rekapForTable[$idBarang]['sisa'] -= $jumlah;
                    $rekapForTable[$idBarang]['nilai_sisa'] = $rekapForTable[$idBarang]['sisa'] * $rekapForTable[$idBarang]['harga'];
                }
            }
        }

        // Respond with the rekap data
        return $this->respond(['rekap' => array_values($rekapForTable)], 200);
    }

}