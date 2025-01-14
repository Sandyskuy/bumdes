<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// File: app/Config/Routes.php

$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->match(['post', 'options'], 'register', 'AuthController::register');
    $routes->match(['post', 'options'], 'login', 'AuthController::login');
    $routes->match(['post', 'options'], 'loginadmin', 'AuthController::loginadmin');
    $routes->post('logout', 'AuthController::logout');
});

$routes->group('barang', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'Barang::index');
    $routes->match(['get', 'options'], 'detail/(:num)', 'Barang::detail/$1');
    $routes->match(['get', 'options'], 'bestseller', 'Barang::bestSelling');
    $routes->match(['post', 'options'], 'postbarang', 'Barang::create');
    $routes->match(['put', 'options'], 'updatebarang/(:num)', 'Barang::update/$1');
    $routes->match(['delete', 'options'], 'deletebarang/(:num)', 'Barang::delete/$1');
    $routes->get('search', 'Barang::search');
    $routes->get('filter/(:segment)', 'Barang::filterByCategory/$1'); // This will match any segment including numbers
});

$routes->group('kategori', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'Kategori::index');
    $routes->match(['post', 'options'], 'postkategori', 'Kategori::create');
    $routes->match(['put', 'options'], 'updatekategori/(:num)', 'Kategori::update/$1');
    $routes->match(['delete', 'options'], 'deletekategori/(:num)', 'Kategori::delete/$1');
});

$routes->group('admin', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'Admin::index'); // Menampilkan daftar admin
    $routes->match(['get', 'options'], 'staff', 'Admin::indexstaff'); // Menampilkan daftar staff
    $routes->match(['get', 'options'], 'admin', 'Admin::indexadmin'); // Menampilkan daftar staff
    $routes->get('/(:num)', 'Admin::show:$1'); // Menampilkan daftar admin
    $routes->match(['post', 'options'], 'postuser', 'Admin::create'); // Membuat admin baru
    $routes->match(['put', 'options'], 'updateuser/(:num)', 'Admin::update/$1'); // Memperbarui admin
    $routes->match(['delete', 'options'], 'deleteuser/(:num)', 'Admin::delete/$1'); // Menghapus admin
    $routes->get('create-super-admin', 'Admin::createSuperAdmin');
});

$routes->group('users', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->match(['get', 'options'], '/', 'Users::index'); // Menampilkan daftar pengguna dengan peran 'buyer'
    $routes->match(['get', 'options'], '(:num)', 'Users::show/$1'); // Menampilkan detail pengguna berdasarkan ID
});

$routes->resource('review', ['controller' => 'Review']);
$routes->match(['post', 'options'], 'postreview', 'Review::create');

$routes->group('transaksi', ['namespace' => 'App\Controllers\Transaksi'], function ($routes) {
    $routes->get('/', 'Transaksi::index');
    $routes->match(['post', 'options'], 'checkout', 'Transaksi::checkout');
    $routes->match(['post', 'options'], 'Cart', 'Transaksi::addToCart');
    $routes->match(['get', 'options'], 'Cart', 'Transaksi::showCart');
    $routes->delete('Cart/(:num)', 'Transaksi::removeFromCart/$1');
    $routes->get('Cart', 'Transaksi::viewCart');
    $routes->match(['get', 'options'], 'not-complete', 'Transaksi::getTransactionNotComplete');
    $routes->match(['get', 'options'], 'complete', 'Transaksi::getTransactionComplete');
    $routes->match(['get', 'options'], 'online', 'Transaksi::getOnlineTransactions');
    $routes->match(['get', 'options'], 'offline', 'Transaksi::getOfflineTransactions');
    $routes->match(['post', 'options'], 'pembayaran', 'Transaksi::pembayaran');
    $routes->match(['get', 'options'], 'transaksi', 'Transaksi::getTransactions');
    $routes->match(['get', 'options'], 'transaksi/(:num)', 'Transaksi::showById/$1');
    $routes->match(['post', 'options'], 'cancel/(:num)', 'Transaksi::cancelTransaksi/$1');
    $routes->match(['post', 'options'], 'transaksimanual', 'Transaksi::transaksiManual');
    $routes->match(['put', 'options'], 'confirm/(:num)', 'Transaksi::confirmStockReceived/$1');
    $routes->match(['get', 'options'], 'pending-stock', 'Transaksi::getPendingStock');
    $routes->delete('removecart', 'Transaksi::removeCart');
});

$routes->group('detail-transaksi', ['namespace' => 'App\Controllers\Transaksi'], function ($routes) {
    $routes->match(['get', 'options'], '/', 'DetailTransaksi::index');
    $routes->match(['get', 'options'], 'show/(:num)', 'DetailTransaksi::show/$1');
    $routes->match(['get', 'options'], 'by-transaksi/(:num)', 'DetailTransaksi::getDetailTransaksiByTransaksi/$1');
});

$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('laporan-keuangan/(:segment)/(:segment)', 'LaporanController::laporanKeuangan/$1/$2');
    $routes->get('laporan-keuangan/(:segment)', 'LaporanController::laporanKeuangan/$1');
    $routes->get('laporan', 'LaporanController::totalPembelian');
    $routes->get('laporanonline', 'LaporanController::totalPembelianOnline');
    $routes->get('laporanoffline', 'LaporanController::totalPembelianOffline');
    $routes->get('laporanperbulan', 'LaporanController::totalPembelianPerBulan');
    $routes->get('totalbuyer', 'LaporanController::totalBuyer');
    $routes->get('totalstaff', 'LaporanController::totalStaff');
    $routes->get('totaltransaksi', 'LaporanController::jumlahTransaksi');
    $routes->get('rekap', 'LaporanController::rekap');
    $routes->get('rekap/(:segment)/(:segment)', 'LaporanController::rekapForTable/$1/$2');
    $routes->get('rekap/(:segment)', 'LaporanController::rekapForTable/$1');
    $routes->get('rekap/create/(:num)', 'LaporanController::createRekapFromUpdatedAt/$1');
});

// $routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
//     $routes->match(['post', 'options'], 'register', 'AuthController::register');
//     $routes->match(['post', 'options'], 'login', 'AuthController::login');
//     $routes->match(['post', 'options'], 'loginadmin', 'AuthController::loginadmin');
//     $routes->post('logout', 'AuthController::logout');
// });

// $routes->group('barang', ['namespace' => 'App\Controllers'], function ($routes) {
//     $routes->get('/', 'Barang::index');
//     $routes->match(['get', 'options'], 'detail/(:num)', 'Barang::detail/$1');
//     $routes->match(['post', 'options'], 'postbarang', 'Barang::create', ['filter' => 'authFilter']);
//     $routes->match(['put', 'options'], 'updatebarang/(:num)', 'Barang::update/$1', ['filter' => 'authFilter']);
//     $routes->match(['delete', 'options'], 'deletebarang/(:num)', 'Barang::delete/$1', ['filter' => 'authFilter']);
// });

// $routes->group('kategori', ['namespace' => 'App\Controllers'], function ($routes) {
//     $routes->get('/', 'Kategori::index');
//     $routes->match(['post', 'options'], 'postkategori', 'Kategori::create', ['filter' => 'authFilter']);
//     $routes->match(['put', 'options'], 'updatekategori/(:num)', 'Kategori::update/$1', ['filter' => 'authFilter']);
//     $routes->match(['delete', 'options'], 'deletekategori/(:num)', 'Kategori::delete/$1', ['filter' => 'authFilter']);
// });

// $routes->group('admin', ['namespace' => 'App\Controllers', 'filter' => 'authFilter', 'superAdmin'], function ($routes) {
//     $routes->get('/', 'Admin::index'); // Menampilkan daftar admin
//     $routes->match(['get', 'options'], 'staff', 'Admin::indexstaff'); // Menampilkan daftar staff
//     $routes->match(['get', 'options'], 'admin', 'Admin::indexadmin'); // Menampilkan daftar staff
//     $routes->get('/(:num)', 'Admin::show:$1'); // Menampilkan daftar admin
//     $routes->match(['post', 'options'], 'postuser', 'Admin::create'); // Membuat admin baru
//     $routes->match(['put', 'options'], 'updateuser/(:num)', 'Admin::update/$1'); // Memperbarui admin
//     $routes->match(['delete', 'options'], 'deleteuser/(:num)', 'Admin::delete/$1'); // Menghapus admin
//     $routes->get('create-super-admin', 'Admin::createSuperAdmin');
// });

// $routes->group('users', ['namespace' => 'App\Controllers'], function ($routes) {
//     $routes->match(['get', 'options'], '/', 'Users::index'); // Menampilkan daftar pengguna dengan peran 'buyer'
//     $routes->match(['get', 'options'], '(:num)', 'Users::show/$1'); // Menampilkan detail pengguna berdasarkan ID
// });

// $routes->resource('review', ['controller' => 'Review']);
// $routes->match(['post', 'options'], 'postreview', 'Review::create');

// $routes->group('transaksi', ['namespace' => 'App\Controllers\Transaksi', 'filter' => 'authFilter'], function ($routes) {
//     $routes->get('/', 'Transaksi::index');
//     $routes->match(['post', 'options'], 'checkout', 'Transaksi::checkout');
//     $routes->match(['post', 'options'], 'Cart', 'Transaksi::addToCart');
//     $routes->match(['get', 'options'], 'Cart', 'Transaksi::showCart');
//     $routes->delete('Cart/(:num)', 'Transaksi::removeFromCart/$1');
//     $routes->get('Cart', 'Transaksi::viewCart');
//     $routes->match(['get', 'options'], 'not-complete', 'Transaksi::getTransactionNotComplete');
//     $routes->match(['get', 'options'], 'complete', 'Transaksi::getTransactionComplete');
//     $routes->match(['post', 'options'], 'pembayaran', 'Transaksi::pembayaran');
//     $routes->match(['get', 'options'], 'transaksi', 'Transaksi::getTransactions');
//     $routes->match(['get', 'options'], 'transaksi/(:num)', 'Transaksi::showById/$1');
//     $routes->match(['post', 'options'], 'cancel/(:num)', 'Transaksi::cancelTransaksi/$1');
//     $routes->match(['post', 'options'], 'transaksimanual', 'Transaksi::manualTransaction');

// });

// $routes->group('detail-transaksi', ['namespace' => 'App\Controllers\Transaksi', 'filter' => 'authFilter'], function ($routes) {
//     $routes->match(['get', 'options'], '/', 'DetailTransaksi::index');
//     $routes->match(['get', 'options'], 'show/(:num)', 'DetailTransaksi::show/$1');
//     $routes->match(['get', 'options'], 'by-transaksi/(:num)', 'DetailTransaksi::getDetailTransaksiByTransaksi/$1');
// });

// $routes->group('api', ['namespace' => 'App\Controllers', 'filter' => 'authFilter'], function ($routes) {
//     $routes->get('laporan-keuangan/(:segment)', 'LaporanController::laporanKeuangan/$1');
//     $routes->get('laporan', 'LaporanController::totalPembelian');
//     $routes->get('laporanperbulan', 'LaporanController::totalPembelianPerBulan');
//     $routes->get('totalbuyer', 'LaporanController::totalBuyer');
//     $routes->get('totalstaff', 'LaporanController::totalStaff');
//     $routes->get('totaltransaksi', 'LaporanController::jumlahTransaksi');
//     $routes->get('rekap', 'LaporanController::rekap');
//     $routes->get('rekap/(:segment)', 'LaporanController::rekapForTable/$1');
// });