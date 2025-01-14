<?php

namespace App\Controllers\Transaksi;

use App\Models\Keranjang;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use App\Controllers\BaseController;
use Illuminate\Support\Str;



class Transaksi extends BaseController
{
    use ResponseTrait;

    protected $transaksiModel;
    protected $detailTransaksiModel;
    protected $barangModel;
    protected $userModel;
    private $keranjangModel;
    protected $db;


    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
        $this->barangModel = new BarangModel();
        $this->userModel = new UserModel();
        $this->keranjangModel = new Keranjang();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return $this->respondCreated(['message' => 'Checkout successful']);
    }

    // public function checkout()
    // {
    //     $key = getenv('JWT_SECRET');
    //     // Ambil token dari header permintaan
    //     $token = $this->request->getHeaderLine('Authorization');

    //     \Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY'); // Ganti dengan Server Key Anda
    //     \Midtrans\Config::$isProduction = false; // Ganti dengan true untuk mode produksi

    //     // Periksa apakah token ditemukan dalam header permintaan
    //     if (empty($token)) {
    //         // Tanggapi jika token tidak ditemukan
    //         return $this->failUnauthorized('Token not provided.');
    //     }

    //     // Buang kata "Bearer " dari token
    //     $token = str_replace('Bearer ', '', $token);

    //     // Decode token untuk mendapatkan payload
    //     try {
    //         $decoded = JWT::decode($token, new Key($key, 'HS256'));
    //     } catch (\Exception $e) {
    //         // Tanggapi jika terjadi kesalahan dalam mendecode token
    //         return $this->failUnauthorized('Invalid token.');
    //     }

    //     // Ambil email dari payload token
    //     $email = $decoded->email;

    //     // Ambil pengguna dari database berdasarkan email
    //     $user = $this->userModel->where('email', $email)->first();

    //     // Periksa apakah pengguna ditemukan
    //     if (!$user) {
    //         return $this->failUnauthorized('User not found.');
    //     }

    //     // Ambil pengguna_id dari hasil pencarian
    //     $user_id = $user['id'];

    //     // Mendapatkan data keranjang belanjaan pengguna
    //     $jsonOrderDetails = $this->request->getBody();


    //     $orderDetails = json_decode($jsonOrderDetails, true);
    //     $cartItems = $orderDetails['order_details'];

    //     $barangDetails = $this->getItems($cartItems);
    //     // Validasi keranjang belanjaan
    //     if (empty($barangDetails)) {
    //         return $this->fail('Cart is empty. Add items to cart before checkout.');
    //     }
    //     // Hitung total belanja
    //     $total = $this->calculateTotal($cartItems);

    //     // Buat data transaksi
    //     $transaksiData = [
    //         'pengguna_id' => $user_id,
    //         'total' => $total,
    //         'status' => 0, // Status 0 untuk belum bayar
    //     ];

    //     // Simpan transaksi ke database
    //     $transaksi = $this->transaksiModel->save($transaksiData);

    //     if (!$transaksi) {
    //         return $this->fail('Failed to create transaction.', 500);
    //     }
    //     // Dapatkan ID transaksi yang baru saja dibuat
    //     $transaksi_id = $this->transaksiModel->insertID();
    //     $midtrans_params = array(
    //         "payment_type" => $orderDetails['payment_methode'],
    //         "bank_transfer" => [
    //             "bank" => "bca"
    //         ],
    //         'transaction_details' => array(
    //             'order_id' => $transaksi_id,
    //             'gross_amount' => $total,
    //         ),
    //         'item_details' => $barangDetails, // Detail barang dalam keranjang belanja
    //         'customer_details' => array(
    //             'email' => $email,
    //         ),
    //     );

    //     // Simpan detail transaksi
    //     foreach ($cartItems as $item) {
    //         $detailTransaksiData = [
    //             'transaksi_id' => $transaksi_id, // Menggunakan id dari transaksi yang baru saja dibuat
    //             'barang_id' => $item['barang_id'],
    //             'jumlah' => $item['quantity'],
    //         ];

    //         $detailTransaksi = $this->detailTransaksiModel->save($detailTransaksiData);
    //         if (!$detailTransaksi) {
    //             // Rollback transaksi jika ada kesalahan pada detail transaksi
    //             $this->transaksiModel->delete($transaksi_id); // Menggunakan id dari transaksi yang baru saja dibuat
    //             return $this->fail('Failed to create transaction detail.', 500);
    //         }
    //         // Kurangi stok barang
    //         $this->updateStock($item['barang_id'], $item['quantity']);
    //     }

    //     $snapToken = $this->createCoreApiTransaction($midtrans_params);
    //     // success create ttansaction remove on cart
    //     $this->keranjangModel->where('pengguna_id', $user_id)->delete();
    //     return $this->respondCreated(['message' => 'Checkout successful.', 'payments' => $snapToken]);
    // }

    public function checkout()
{
    $key = getenv('JWT_SECRET');
    // Ambil token dari header permintaan
    $token = $this->request->getHeaderLine('Authorization');

    \Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY'); // Ganti dengan Server Key Anda
    \Midtrans\Config::$isProduction = false; // Ganti dengan true untuk mode produksi

    // Periksa apakah token ditemukan dalam header permintaan
    if (empty($token)) {
        // Tanggapi jika token tidak ditemukan
        return $this->failUnauthorized('Token not provided.');
    }

    // Buang kata "Bearer " dari token
    $token = str_replace('Bearer ', '', $token);

    // Decode token untuk mendapatkan payload
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
    } catch (\Exception $e) {
        // Tanggapi jika terjadi kesalahan dalam mendecode token
        return $this->failUnauthorized('Invalid token.');
    }

    // Ambil email dari payload token
    $email = $decoded->email;

    // Ambil pengguna dari database berdasarkan email
    $user = $this->userModel->where('email', $email)->first();

    // Periksa apakah pengguna ditemukan
    if (!$user) {
        return $this->failUnauthorized('User not found.');
    }

    // Ambil pengguna_id dari hasil pencarian
    $user_id = $user['id'];

    // Mendapatkan data keranjang belanjaan pengguna
    $jsonOrderDetails = $this->request->getBody();
    $orderDetails = json_decode($jsonOrderDetails, true);
    $cartItems = $orderDetails['order_details'];
    $alamat = $orderDetails['alamat']; // Tambahkan pengambilan alamat dari order details

    $barangDetails = $this->getItems($cartItems);
    // Validasi keranjang belanjaan
    if (empty($barangDetails)) {
        return $this->fail('Cart is empty. Add items to cart before checkout.');
    }

    // Hitung total belanja
    $total = $this->calculateTotal($cartItems);

    // Buat data transaksi
    $transaksiData = [
        'pengguna_id' => $user_id,
        'total' => $total,
        'status' => 0, // Status 0 untuk belum
        'barang_diterima' => false, // Barang belum dikonfirmasi diterima oleh pelanggan
        'alamat' => $alamat // Tambahkan alamat ke data transaksi
    ];

    // Simpan transaksi ke database
    $transaksi = $this->transaksiModel->save($transaksiData);

    if (!$transaksi) {
        return $this->fail('Failed to create transaction.', 500);
    }

    // Dapatkan ID transaksi yang baru saja dibuat
    $transaksi_id = $this->transaksiModel->insertID();
    $midtrans_params = [
        "payment_type" => $orderDetails['payment_methode'],
        "bank_transfer" => [
            "bank" => "bca"
        ],
        'transaction_details' => [
            'order_id' => $transaksi_id,
            'gross_amount' => $total,
        ],
        'item_details' => $barangDetails, // Detail barang dalam keranjang belanja
        'customer_details' => [
            'email' => $email,
            'alamat' => $alamat // Tambahkan alamat ke detail pelanggan
        ],
    ];

    // Simpan detail transaksi
    foreach ($cartItems as $item) {
        $detailTransaksiData = [
            'transaksi_id' => $transaksi_id, // Menggunakan id dari transaksi yang baru saja dibuat
            'barang_id' => $item['barang_id'],
            'jumlah' => $item['quantity']
        ];

        $detailTransaksi = $this->detailTransaksiModel->save($detailTransaksiData);
        if (!$detailTransaksi) {
            // Rollback transaksi jika ada kesalahan pada detail transaksi
            $this->transaksiModel->delete($transaksi_id); // Menggunakan id dari transaksi yang baru saja dibuat
            return $this->fail('Failed to create transaction detail.', 500);
        }
    }

    $snapToken = $this->createCoreApiTransaction($midtrans_params);

    // success create transaction remove from cart
    $this->keranjangModel->where('pengguna_id', $user_id)->delete();

    return $this->respondCreated(['message' => 'Checkout successful.', 'payments' => $snapToken]);
}

    public function pembayaran()
    {
        try {
            //code...
            \Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY'); // Ganti dengan Server Key Anda
            \Midtrans\Config::$isProduction = false; // Ganti dengan true untuk mode produksi
            $notif = new \Midtrans\Notification();
            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $order_id = $notif->order_id;
            if ($transaction == 'settlement') {
                // Ambil transaksi dari database berdasarkan ID
                $transaksi = $this->transaksiModel->find($order_id);
                $transaksi['status'] = 1;
                $this->transaksiModel->update($order_id, $transaksi);
                return $this->respond(['message' => 'Payment successful.', 'transaction_id' => $order_id]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->respond($th->getMessage(), 500);
        }
    }

    private function createCoreApiTransaction($params)
    {
        \Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY'); // Ganti dengan Server Key Anda
        \Midtrans\Config::$isProduction = false; // Ganti dengan true untuk mode produksi

        // Panggil endpoint Core API Midtrans untuk membuat transaksi
        $response = \Midtrans\CoreApi::charge($params);

        return $response;
    }

    public function getItems($cartItems)
    {
        $data = [];
        foreach ($cartItems as $key => $value) {
            # code...
            $barang = $this->barangModel->find($value['barang_id']);
            $barang['price'] = $barang['harga'];
            $barang['quantity'] = $value['quantity'];
            $barang['name'] = $barang['nama'];
            array_push($data, $barang);
        }
        return $data;
    }

    public function getTransactionNotComplete()
    {
        try {

            $json = $this->request->getjson();
            $key = getenv('JWT_SECRET');
            // Ambil token dari header permintaan
            $token = $this->request->getHeaderLine('Authorization');

            \Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY'); // Ganti dengan Server Key Anda
            \Midtrans\Config::$isProduction = false; // Ganti dengan true untuk mode produksi

            // Periksa apakah token ditemukan dalam header permintaan
            if (empty($token)) {
                // Tanggapi jika token tidak ditemukan
                return $this->failUnauthorized('Token not provided.');
            }

            // Buang kata "Bearer " dari token
            $token = str_replace('Bearer ', '', $token);

            // Decode token untuk mendapatkan payload
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
            } catch (\Exception $e) {
                // Tanggapi jika terjadi kesalahan dalam mendecode token
                return $this->failUnauthorized('Invalid token.');
            }

            $email = $decoded->email;

            $user = $this->userModel->where('email', $email)->first();
            $userId = $user['id'];
            $transaksis = $this->transaksiModel->findAll();
            $data = [];
            foreach ($transaksis as $transaksi) {
                if ($transaksi['pengguna_id'] == $userId) {
                    $midtrans = \Midtrans\Transaction::status($transaksi['id']);
                    $transaksi['midtrans'] = $midtrans;
                    if ($midtrans->transaction_status == 'pending') {
                        array_push($data, $transaksi);
                    }
                }
            }
            return $this->respond($data);
        } catch (\Throwable $th) {
            return $this->respond($th->getMessage());
        }
    }

    public function getTransactionComplete()
    {
        try {

            $json = $this->request->getjson();
            $key = getenv('JWT_SECRET');
            // Ambil token dari header permintaan
            $token = $this->request->getHeaderLine('Authorization');

            \Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY'); // Ganti dengan Server Key Anda
            \Midtrans\Config::$isProduction = false; // Ganti dengan true untuk mode produksi

            // Periksa apakah token ditemukan dalam header permintaan
            if (empty($token)) {
                // Tanggapi jika token tidak ditemukan
                return $this->failUnauthorized('Token not provided.');
            }

            // Buang kata "Bearer " dari token
            $token = str_replace('Bearer ', '', $token);

            // Decode token untuk mendapatkan payload
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
            } catch (\Exception $e) {
                // Tanggapi jika terjadi kesalahan dalam mendecode token
                return $this->failUnauthorized('Invalid token.');
            }

            $email = $decoded->email;

            $user = $this->userModel->where('email', $email)->first();
            $userId = $user['id'];
            $transaksis = $this->transaksiModel->findAll();
            $data = [];
            foreach ($transaksis as $transaksi) {
                if ($transaksi['pengguna_id'] == $userId) {
                    $midtrans = \Midtrans\Transaction::status($transaksi['id']);
                    $transaksi['midtrans'] = $midtrans;
                    if ($midtrans->transaction_status == 'settlement') {
                        array_push($data, $transaksi);
                    }
                }
            }
            return $this->respond($data);
        } catch (\Throwable $th) {
            return $this->respond($th->getLine());
        }
    }
    // public function addToCart()
    // {
    //     try {

    //         $json = $this->request->getjson();
    //         $key = getenv('JWT_SECRET');
    //         // Ambil token dari header permintaan
    //         $token = $this->request->getHeaderLine('Authorization');

    //         \Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY'); // Ganti dengan Server Key Anda
    //         \Midtrans\Config::$isProduction = false; // Ganti dengan true untuk mode produksi


    //         // Periksa apakah token ditemukan dalam header permintaan
    //         if (empty($token)) {
    //             // Tanggapi jika token tidak ditemukan
    //             return $this->failUnauthorized('Token not provided.');
    //         }

    //         // Buang kata "Bearer " dari token
    //         $token = str_replace('Bearer ', '', $token);

    //         // Decode token untuk mendapatkan payload
    //         try {
    //             $decoded = JWT::decode($token, new Key($key, 'HS256'));
    //         } catch (\Exception $e) {
    //             // Tanggapi jika terjadi kesalahan dalam mendecode token
    //             return $this->failUnauthorized('Invalid token.');
    //         }

    //         $email = $decoded->email;


    //         $user = $this->userModel->where('email', $email)->first();
    //         $barang_id = $json->barang_id; ///
    //         $barang = $this->barangModel->find($barang_id);
    //         $data = [];
    //         $data['pengguna_id'] = $user['id']; ///
    //         $data['barang_id'] = $barang_id; ///
    //         $data['harga'] = $json->jumlah * $barang['harga']; ///
    //         $data['jumlah'] = $json->jumlah; ///

    //         $keranjang = $this->keranjangModel->insert($data);
    //         return $this->respond(['message' => 'Item added to cart successfully.', 'data' => $keranjang]);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         return $this->respond(['message' => $th->getMessage()]);
    //     }
    // }

    public function addToCart()
    {
        try {
            $json = $this->request->getjson();
            $key = getenv('JWT_SECRET');
            // Get token from the request header
            $token = $this->request->getHeaderLine('Authorization');

            \Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY'); // Replace with your Server Key
            \Midtrans\Config::$isProduction = false; // Change to true for production mode

            // Check if token is found in the request header
            if (empty($token)) {
                // Respond if token is not found
                return $this->failUnauthorized('Token not provided.');
            }

            // Remove the "Bearer " word from the token
            $token = str_replace('Bearer ', '', $token);

            // Decode token to get the payload
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
            } catch (\Exception $e) {
                // Respond if there is an error in decoding the token
                return $this->failUnauthorized('Invalid token.');
            }

            $email = $decoded->email;

            $user = $this->userModel->where('email', $email)->first();
            $barang_id = $json->barang_id;
            $barang = $this->barangModel->find($barang_id);

            // Check if the item is in stock
            if ($barang['stok'] <= 0) {
                return $this->respond(['message' => 'Item is out of stock.'], 400);
            }

            // Check if the item already exists in the cart for the user
            $existingCartItem = $this->keranjangModel->where('pengguna_id', $user['id'])->where('barang_id', $barang_id)->first();

            if ($existingCartItem) {
                // If item exists, update the quantity and price
                $newJumlah = $existingCartItem['jumlah'] + $json->jumlah;

                // Check if the new quantity exceeds stock
                if ($newJumlah > $barang['stok']) {
                    return $this->respond(['message' => 'Not enough stock available.'], 400);
                }

                $updatedData = [
                    'jumlah' => $newJumlah,
                    'harga' => $newJumlah * $barang['harga']
                ];
                $this->keranjangModel->update($existingCartItem['id'], $updatedData);
                return $this->respond(['message' => 'Cart item updated successfully.', 'data' => $updatedData]);
            } else {
                // If item does not exist, insert a new record
                // Check if the requested quantity exceeds stock
                if ($json->jumlah > $barang['stok']) {
                    return $this->respond(['message' => 'Not enough stock available.'], 400);
                }

                $data = [
                    'pengguna_id' => $user['id'],
                    'barang_id' => $barang_id,
                    'harga' => $json->jumlah * $barang['harga'],
                    'jumlah' => $json->jumlah
                ];
                $keranjang = $this->keranjangModel->insert($data);
                return $this->respond(['message' => 'Item added to cart successfully.', 'data' => $keranjang]);
            }
        } catch (\Throwable $th) {
            return $this->respond(['message' => $th->getMessage()]);
        }
    }


    public function showCart()
    {
        //code... // Ambil data JSON dari body permintaana
        $json = $this->request->getjson();
        $key = getenv('JWT_SECRET');
        // Ambil token dari header permintaan
        $token = $this->request->getHeaderLine('Authorization');

        \Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY'); // Ganti dengan Server Key Anda
        \Midtrans\Config::$isProduction = false; // Ganti dengan true untuk mode produksi

        // Periksa apakah token ditemukan dalam header permintaan
        if (empty($token)) {
            // Tanggapi jika token tidak ditemukan
            return $this->failUnauthorized('Token not provided.');
        }

        // Buang kata "Bearer " dari token
        $token = str_replace('Bearer ', '', $token);

        // Decode token untuk mendapatkan payload
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            // Tanggapi jika terjadi kesalahan dalam mendecode token
            return $this->failUnauthorized('Invalid token.');
        }

        $email = $decoded->email;

        $user = $this->userModel->where('email', $email)->first();

        try {
            // Mendapatkan ID pengguna dari token atau sesuai dengan implementasi extractUserId Anda
            $userId = $user['id'];
            // Mencari data keranjang berdasarkan pengguna_id dengan join ke tabel barang
            $keranjang = $this->keranjangModel
                ->select('keranjang.*, barang.nama AS nama_barang')
                ->join('barang', 'barang.id = keranjang.barang_id')
                ->where('keranjang.pengguna_id', $userId)
                ->findAll();
            return $this->respond($keranjang);
        } catch (\Throwable $th) {
            // Menangani pengecualian
            return $this->fail($th->getLine()); // Mengembalikan respons dengan pesan kesalahan
        }
    }



    protected function calculateTotal($cartItems)
    {
        $total = 0;
        foreach ($cartItems as $item) {
            $barang = $this->barangModel->find($item['barang_id']);
            $total += $barang['harga'] * $item['quantity'];
        }
        return $total;
    }

    protected function updateStock($barang_id, $quantity)
    {
        $barang = $this->barangModel->find($barang_id);
        $updatedStock = $barang['stok'] - $quantity;
        $this->barangModel->update($barang_id, ['stok' => $updatedStock]);
    }



    public function getTransactions()
    {
        // Ambil semua transaksi
        $transactions = $this->transaksiModel->findAll();

        // Jika tidak ada transaksi, kirim respon kosong atau pesan yang sesuai
        if (empty($transactions)) {
            return $this->respond(['message' => 'Transaksi tidak ditemukan.'], 200);
        }

        // Jika ada transaksi, kirim respon dengan data transaksi
        return $this->respond($transactions, 200);
    }

    public function getOnlineTransactions()
    {
        // Ambil transaksi yang via-nya online
        $onlineTransactions = $this->transaksiModel->where('via', 'online')->findAll();

        // Jika tidak ada transaksi, kirim respon kosong atau pesan yang sesuai
        if (empty($onlineTransactions)) {
            return $this->respond(['message' => 'Transaksi tidak ditemukan.'], 200);
        }

        // Jika ada transaksi, kirim respon dengan data transaksi
        return $this->respond($onlineTransactions, 200);
    }
    public function getOfflineTransactions()
    {
        // Ambil transaksi yang via-nya online
        $onlineTransactions = $this->transaksiModel->where('via', 'offline')->findAll();

        // Jika tidak ada transaksi, kirim respon kosong atau pesan yang sesuai
        if (empty($onlineTransactions)) {
            return $this->respond(['message' => 'Transaksi tidak ditemukan.'], 200);
        }

        // Jika ada transaksi, kirim respon dengan data transaksi
        return $this->respond($onlineTransactions, 200);
    }


    public function showById($id)
    {
        // Lakukan join antara tabel transaksi dan users
        $transaction = $this->transaksiModel
            ->join('users', 'users.id = transaksi.pengguna_id')
            ->where('transaksi.id', $id)
            ->select('transaksi.*, users.username AS username_pengguna')
            ->first();

        // Periksa apakah transaksi ditemukan
        if (!$transaction) {
            return $this->fail('Transaction not found.', 404);
        }

        // Kirim respons dengan data transaksi
        return $this->respond($transaction, 200);
    }


    public function cancelTransaksi($id)
    {
        try {
            // Ambil transaksi berdasarkan ID
            $transaksi = $this->transaksiModel->find($id);

            // Periksa apakah transaksi ditemukan
            if (!$transaksi) {
                return $this->fail('Transaction not found.', 404);
            }

            // Periksa apakah status transaksi sudah menjadi settled
            if ($transaksi['status'] == 1) {
                return $this->fail('Transaction has already been settled and cannot be canceled.', 400);
            }

            // Ubah status transaksi menjadi canceled
            $transaksi['status'] = -1;

            // Update status transaksi di database
            $updateResult = $this->transaksiModel->update($id, $transaksi);

            // Periksa apakah update berhasil
            if (!$updateResult) {
                return $this->fail('Failed to cancel transaction.', 500);
            }

            return $this->respond(['message' => 'Transaction canceled successfully.', 'transaction_id' => $id]);
        } catch (\Throwable $th) {
            return $this->respond($th->getMessage(), 500);
        }
    }

    public function transaksiManual()
    {
        try {
            // Mulai transaksi database
            $this->db->transBegin();

            // Ambil data JSON dari body permintaan
            $json = $this->request->getJSON();

            // Validasi inputan
            $rules = [
                'barang_ids' => 'required',
                'quantities' => 'required',
            ];

            $errors = [
                'barang_ids' => [
                    'required' => 'Barang IDs are required.',
                ],
                'quantities' => [
                    'required' => 'Quantities are required.',
                ],
            ];

            if (!$this->validate($rules, $errors)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            $json = $this->request->getjson();
            $key = getenv('JWT_SECRET');
            // Ambil token dari header permintaan
            $token = $this->request->getHeaderLine('Authorization');

            // Periksa apakah token ditemukan dalam header permintaan
            if (empty($token)) {
                // Tanggapi jika token tidak ditemukan
                return $this->failUnauthorized('Token not provided.');
            }

            // Buang kata "Bearer " dari token
            $token = str_replace('Bearer ', '', $token);

            // Decode token untuk mendapatkan payload
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
            } catch (\Exception $e) {
                // Tanggapi jika terjadi kesalahan dalam mendecode token
                return $this->failUnauthorized('Invalid token.');
            }

            $email = $decoded->email;

            // Ambil ID pengguna dari database berdasarkan email
            $user = $this->userModel->where('email', $email)->first();
            if (!$user) {
                return $this->failUnauthorized('User not found.');
            }
            $userId = $user['id'];

            // Ubah ke array jika barang_ids dan quantities hanya merupakan satu nilai
            $barangIds = is_array($json->barang_ids) ? $json->barang_ids : [$json->barang_ids];
            $quantities = is_array($json->quantities) ? $json->quantities : [$json->quantities];

            // Cek stok barang
            foreach ($barangIds as $index => $barangId) {
                $barang = $this->barangModel->find($barangId);
                if (!$barang) {
                    $this->db->transRollback();
                    return $this->fail("Barang with ID $barangId not found.");
                }
                // Cek apakah stok mencukupi
                if ($barang['stok'] < $quantities[$index]) {
                    $this->db->transRollback();
                    return $this->fail("Not enough stock for Barang with ID $barangId.");
                }
            }

            // Hitung total transaksi
            $total = 0;
            foreach ($barangIds as $index => $barangId) {
                $barang = $this->barangModel->find($barangId);
                $total += $barang['harga'] * $quantities[$index];
            }

            // Buat data transaksi
            $transaksiData = [
                'pengguna_id' => $userId, // Menggunakan ID pengguna dari token JWT
                'total' => $total,
                'status' => 1, // Status 0 untuk belum bayar
                'via' => 'offline',
            ];

            // Simpan transaksi ke database
            $transaksi = $this->transaksiModel->save($transaksiData);

            if (!$transaksi) {
                $this->db->transRollback();
                return $this->fail('Failed to create transaction.', 500);
            }

            // Dapatkan ID transaksi yang baru saja dibuat
            $transaksiId = $this->transaksiModel->insertID();

            // Simpan detail transaksi
            foreach ($barangIds as $index => $barangId) {
                $detailTransaksiData = [
                    'transaksi_id' => $transaksiId,
                    'barang_id' => $barangId,
                    'jumlah' => $quantities[$index],
                ];

                $detailTransaksi = $this->detailTransaksiModel->save($detailTransaksiData);
                if (!$detailTransaksi) {
                    // Rollback transaksi jika ada kesalahan pada detail transaksi
                    $this->db->transRollback();
                    return $this->fail('Failed to create transaction detail.', 500);
                }
                // Kurangi stok barang
                $this->updateStock($barangId, $quantities[$index]);
            }

            // Commit transaksi database
            $this->db->transCommit();

            return $this->respondCreated(['message' => 'Manual transaction successful.', 'transaction_id' => $transaksiId]);
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            $this->db->transRollback();
            return $this->respond($th->getMessage(), 500);
        }
    }

    public function removeCart()
    {
        try {
            // Ambil data JSON dari body permintaan
            $json = $this->request->getJSON();

            // Validasi inputan
            $rules = [
                'barang_id' => 'required',
            ];

            $errors = [
                'barang_id' => [
                    'required' => 'Barang ID is required.',
                ],
            ];

            if (!$this->validate($rules, $errors)) {
                return $this->failValidationErrors($this->validator->getErrors());
            }

            // Ambil token dari header permintaan
            $token = $this->request->getHeaderLine('Authorization');

            // Periksa apakah token ditemukan dalam header permintaan
            if (empty($token)) {
                // Tanggapi jika token tidak ditemukan
                return $this->failUnauthorized('Token not provided.');
            }

            // Buang kata "Bearer " dari token
            $token = str_replace('Bearer ', '', $token);

            // Inisialisasi kunci untuk mendekode token JWT
            $key = getenv('JWT_SECRET');

            // Decode token untuk mendapatkan payload
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
            } catch (\Exception $e) {
                // Tanggapi jika terjadi kesalahan dalam mendecode token
                return $this->failUnauthorized('Invalid token.');
            }

            $email = $decoded->email;

            // Ambil pengguna dari database berdasarkan email
            $user = $this->userModel->where('email', $email)->first();

            // Periksa apakah pengguna ditemukan
            if (!$user) {
                return $this->failUnauthorized('User not found.');
            }

            // Ambil ID pengguna dari hasil pencarian
            $user_id = $user['id'];

            // Ambil ID barang dari permintaan
            $barangId = $json->barang_id;

            // Hapus item dari keranjang belanja pengguna
            $cart = $this->keranjangModel->where('pengguna_id', $user_id)
                ->where('barang_id', $barangId)
                ->delete();

            // Periksa apakah item berhasil dihapus
            if (!$cart) {
                return $this->fail('Failed to remove item from cart.', 500);
            }

            return $this->respondDeleted(['message' => 'Item removed from cart successfully.']);
        } catch (\Throwable $th) {
            return $this->respond($th->getMessage(), 500);
        }
    }




    public function confirmStockReceived($transaksi_id)
    {
        // Ambil transaksi berdasarkan ID
        $transaksi = $this->transaksiModel->find($transaksi_id);

        // Periksa apakah transaksi ditemukan
        if (!$transaksi) {
            return $this->failNotFound('Transaksi tidak ditemukan.');
        }

        // Ambil detail transaksi terkait dengan transaksi ID
        $detailTransaksi = $this->detailTransaksiModel->where('transaksi_id', $transaksi_id)->findAll();

        // Periksa apakah detail transaksi ditemukan
        if (!$detailTransaksi) {
            return $this->failNotFound('Detail transaksi tidak ditemukan.');
        }

        // Iterasi melalui detail transaksi untuk mengurangi stok barang
        foreach ($detailTransaksi as $detail) {
            $this->updateStock($detail['barang_id'], $detail['jumlah']);
        }

        // Perbarui kolom stok_diterima menjadi true di tabel transaksi
        $transaksi['barang_diterima'] = true;
        $this->transaksiModel->update($transaksi_id, $transaksi);

        return $this->respond(['message' => 'Barang sudah diterima pelanggan.']);
    }

    // public function confirmStockReceived($transaksi_id)
    // {
    //     // Ambil detail transaksi berdasarkan ID
    //     $detailTransaksi = $this->detailTransaksiModel->find($transaksi_id);

    //     // Periksa apakah detail transaksi ditemukan
    //     if (!$detailTransaksi) {
    //         return $this->failNotFound('Detail transaksi tidak ditemukan.');
    //     }

    //     // Perbarui kolom stok_diterima menjadi truez -->
    //     $detailTransaksi['stok_diterima'] = true;
    //     $this->detailTransaksiModel->update($transaksi_id, $detailTransaksi);

    //     // Kurangi stok barang setelah konfirmasi penerimaan stok
    //     $this->updateStock($detailTransaksi['barang_id'], $detailTransaksi['jumlah']);

    //     return $this->respond(['message' => 'Stok diterima dan stok barang diperbarui.']);
    // }

    public function getPendingStock()
    {
        // Get pending transactions
        $transaksi = $this->transaksiModel
            ->select('transaksi.*, detail_transaksi.barang_id, detail_transaksi.jumlah, barang.nama as nama_barang, users.username')
            ->join('detail_transaksi', 'detail_transaksi.transaksi_id = transaksi.id')
            ->join('barang', 'barang.id = detail_transaksi.barang_id')
            ->join('users', 'users.id = transaksi.pengguna_id')  // Join with the users table
            ->where('transaksi.barang_diterima', 0)
            ->findAll();

        // Organize data into the desired structure
        $result = [];
        foreach ($transaksi as $row) {
            $transaksi_id = $row['id'];

            if (!isset($result[$transaksi_id])) {
                $result[$transaksi_id] = [
                    'id' => $row['id'],
                    'tanggal' => $row['tanggal'],
                    'total' => $row['total'],
                    'username' => $row['username'],  // Include the user's name
                    'stok_dibeli' => []
                ];
            }

            $result[$transaksi_id]['stok_dibeli'][$row['barang_id']] = [
                'nama' => $row['nama_barang'],
                'jumlah' => $row['jumlah']
            ];
        }

        // Reindex the array to reset numeric keys
        $result = array_values($result);

        return $this->respond($result);
    }








    // Method untuk mendapatkan email pengguna dari token JWT



}
