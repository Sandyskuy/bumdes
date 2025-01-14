<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Barang extends ResourceController
{
    protected $modelName = 'App\Models\BarangModel';
    protected $format = 'json';

    public function index()
    {
        $model = new $this->modelName();
        $data = $model->findAllWithKategori();
        return $this->respond($data);
    }


    public function detail($id)
    {
        $model = new $this->modelName();
        $data = $model->find($id);

        if ($data === null) {
            return $this->failNotFound('Barang not found');
        }

        return $this->respond($data);
    }


    public function create()
    {
        $model = new $this->modelName();

        // Ambil data dari body permintaan
        $data = $this->request->getPost();

        // Cek apakah ada file gambar yang diunggah
        $gambar = $this->request->getFile('gambar');

        // Jika ada file gambar yang diunggah, proses penyimpanannya
        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            // Pindahkan file gambar ke direktori yang ditentukan
            $gambar->move(ROOTPATH . 'public/uploads');

            // Simpan nama file gambar ke dalam data barang
            $data['gambar'] = $gambar->getName();
        }

        // Masukkan data barang ke dalam database
        $model->insert($data);

        // Beri respons bahwa barang telah berhasil dibuat
        return $this->respondCreated(['message' => 'Barang created successfully']);
    }



    // public function update($id = null)
    // {
    //     $model = new $this->modelName();
    //     $data = (array) $this->request->getJSON(); // Ubah objek stdClass menjadi array

    //     // Periksa apakah ID barang yang ingin diperbarui diberikan
    //     if ($id === null) {
    //         // Jika tidak, kirim respons dengan kode 400 Bad Request
    //         return $this->fail('Missing ID parameter', 400);
    //     }

    //     // Periksa apakah data yang diperlukan untuk pembaruan diberikan
    //     if (empty($data)) {
    //         // Jika tidak, kirim respons dengan kode 400 Bad Request
    //         return $this->fail('No data provided for update', 400);
    //     }

    //     // Lakukan pembaruan data dalam model
    //     $barangModel = new \App\Models\BarangModel();
    //     $rekapModel = new \App\Models\RekapModel();

    //     // Ambil data barang sebelum diupdate
    //     $barangSebelumUpdate = $model->find($id);

    //     if ($barangSebelumUpdate === null) {
    //         // Jika barang tidak ditemukan, kirim respons dengan kode 404 Not Found
    //         return $this->failNotFound('Barang not found');
    //     }

    //     // Update barang
    //     $model->update($id, $data);

    //     // Perbarui stok awal di tabel rekap jika terjadi perubahan stok
    //     if (isset($data['stok'])) {
    //         // Hitung selisih stok untuk update stok awal di tabel rekap
    //         $selisihStok = $data['stok'] - $barangSebelumUpdate['stok'];

    //         // Perbarui stok awal di tabel rekap
    //         $bulanIni = date('m');
    //         $tahunIni = date('Y');
    //         $rekapModel->updateStokAwal($id, $bulanIni, $tahunIni, $selisihStok);
    //     }

    //     // Kirim respons berhasil dengan kode 200 OK
    //     return $this->respond(['message' => 'Barang updated successfully'], 200);
    // }

    public function update($id = null)
    {
        $model = new $this->modelName();
        $data = (array) $this->request->getJSON(); // Ubah objek stdClass menjadi array

        // Periksa apakah ID barang yang ingin diperbarui diberikan
        if ($id === null) {
            // Jika tidak, kirim respons dengan kode 400 Bad Request
            return $this->fail('Missing ID parameter', 400);
        }

        // Periksa apakah data yang diperlukan untuk pembaruan diberikan
        if (empty($data)) {
            // Jika tidak, kirim respons dengan kode 400 Bad Request
            return $this->fail('No data provided for update', 400);
        }

        // Lakukan pembaruan data dalam model
        $model->update($id, $data);

        // Kirim respons berhasil dengan kode 200 OK
        return $this->respond(['message' => 'Barang updated successfully'], 200);
    }



    public function delete($id = null)
    {
        $model = new $this->modelName();
        $model->delete($id);
        return $this->respond(['message' => 'Barang deleted successfully']);
    }

    public function filterByCategory($categoryId)
    {
        // Validate the category ID
        if (!is_numeric($categoryId)) {
            return $this->fail('Invalid category ID', 400);
        }

        // Create an instance of the model
        $model = new $this->modelName();

        // Use query binding to prevent SQL injection
        $sql = "
        SELECT barang.*, kategori.nama AS nama_kategori
        FROM barang
        JOIN kategori ON barang.kategori_id = kategori.id
        WHERE barang.kategori_id = ?
    ";
        $query = $model->db->query($sql, [$categoryId]);

        // Perform the query to retrieve items filtered by category ID
        $filteredItems = $query->getResult();

        // Check if any items are found for the specified category
        if (empty($filteredItems)) {
            // If no items are found, respond with a not found error
            return $this->failNotFound('No items found for the specified category');
        }

        // Respond with the filtered items
        return $this->respond($filteredItems);
    }

    public function search()
    {
        // Get the search query from the request
        $keyword = $this->request->getGet('keyword');

        // If no keyword is provided, respond with a bad request error
        if (empty($keyword)) {
            return $this->fail('Search keyword is required', 400);
        }

        // Create an instance of the model
        $model = new $this->modelName();

        // Perform the search using the model's method
        $searchResult = $model->search($keyword);

        // Check if any results are found
        if (empty($searchResult)) {
            // If no results are found, respond with a not found error
            return $this->fail('No matching results found', 404);
        }

        // Respond with the search results
        return $this->respond($searchResult);
    }

    public function bestSelling()
    {
        $model = new $this->modelName();
        $data = $model->getBestSellingProducts();

        // Check if any best-selling products are found
        if (empty($data)) {
            return $this->failNotFound('Produk unggulan tidak ditemukan');
        }

        return $this->respond($data);
    }
}
