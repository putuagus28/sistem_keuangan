<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

/** mahasiswa & pembina*/
Route::middleware(['auth:user', 'ceklevel:mahasiswa,pembina'])->group(function () {
    Route::get('setprivilege', 'LoginController@setprivilege')->name('setprivilege');
    Route::post('setprivilege', 'LoginController@setprivilege')->name('setprivilege');
    /** anggota ukm */
    Route::get('anggota-ukm', 'AnggotaController@index')->name('anggota.ukm');
    Route::post('insertanggotaukm', 'AnggotaController@insert')->name('insert.anggotaukm');
    Route::get('anggotaukm/edit', 'AnggotaController@edit')->name('edit.anggotaukm');
    Route::post('anggotaukm/update', 'AnggotaController@update')->name('update.anggotaukm');
    Route::get('anggotaukm/delete', 'AnggotaController@delete')->name('delete.anggotaukm');
    /** akun akutansi */
    Route::get('akun', 'AkunController@index')->name('akun');
    Route::post('akun', 'AkunController@index')->name('json.akun');
    Route::post('akun/insert', 'AkunController@insert')->name('insert.akun');
    Route::get('akun/edit', 'AkunController@edit')->name('edit.akun');
    Route::get('akun/delete', 'AkunController@delete')->name('delete.akun');
    Route::post('akun/update', 'AkunController@update')->name('update.akun');
    /** mutasi akutansi */
    Route::get('mutasi', 'MutasiAkunController@index')->name('mutasi');
    Route::post('mutasi', 'MutasiAkunController@index')->name('json.mutasi');
    Route::post('mutasi/insert', 'MutasiAkunController@insert')->name('insert.mutasi');
    Route::get('mutasi/edit', 'MutasiAkunController@edit')->name('edit.mutasi');
    Route::get('mutasi/delete', 'MutasiAkunController@delete')->name('delete.mutasi');
    Route::post('mutasi/update', 'MutasiAkunController@update')->name('update.mutasi');
    /** transfers */
    Route::get('transfers', 'TransfersController@index')->name('transfers');
    Route::post('transfers', 'TransfersController@index')->name('json.transfers');
    Route::post('transfers/insert', 'TransfersController@insert')->name('insert.transfers');
    Route::get('transfers/edit', 'TransfersController@edit')->name('edit.transfers');
    Route::get('transfers/delete', 'TransfersController@delete')->name('delete.transfers');
    Route::post('transfers/update', 'TransfersController@update')->name('update.transfers');
    /** jurnal akutansi */
    Route::get('jurnal', 'JurnalController@index')->name('jurnal');
    Route::get('get/akun', 'JurnalController@getAkun')->name('getakun');
    Route::post('jurnal', 'JurnalController@index')->name('json.jurnal');
    Route::post('jurnal/insert', 'JurnalController@insert')->name('insert.jurnal');
    Route::get('jurnal/edit', 'JurnalController@edit')->name('edit.jurnal');
    Route::get('jurnal/delete', 'JurnalController@delete')->name('delete.jurnal');
    Route::post('jurnal/update', 'JurnalController@update')->name('update.jurnal');
    Route::get('jurnal/cart', 'JurnalController@list')->name('json.cart');
    Route::post('jurnal/cart/insert', 'JurnalController@add')->name('insert.cart');
    Route::get('jurnal/cart/delete', 'JurnalController@del_cart')->name('delete.cart');
    /** pembayaran*/
    Route::get('transaksi/pembayaran', 'PembayaranController@index')->name('pembayaran');
    Route::post('transaksi/pembayaran', 'PembayaranController@index')->name('json.pembayaran');
    Route::get('transaksi/pembayaran/belum', 'PembayaranController@belum_bayar')->name('json.belum.pembayaran');
    Route::post('transaksi/pembayaran/insert', 'PembayaranController@insert')->name('insert.pembayaran');
    Route::get('transaksi/pembayaran/edit', 'PembayaranController@edit')->name('edit.pembayaran');
    Route::post('transaksi/pembayaran/update', 'PembayaranController@update')->name('update.pembayaran');
    /** pemasukan*/
    Route::get('transaksi/pemasukan', 'PemasukanController@index')->name('pemasukan');
    Route::post('transaksi/pemasukan', 'PemasukanController@index')->name('json.pemasukan');
    Route::post('transaksi/pemasukan/insert', 'PemasukanController@insert')->name('insert.pemasukan');
    Route::get('transaksi/pemasukan/edit', 'PemasukanController@edit')->name('edit.pemasukan');
    Route::post('transaksi/pemasukan/update', 'PemasukanController@update')->name('update.pemasukan');
    Route::get('transaksi/pemasukan/delete', 'PemasukanController@delete')->name('delete.pemasukan');
    Route::get('pemasukan/cart', 'PemasukanController@list')->name('json.cart.pemasukan');
    Route::post('pemasukan/cart/insert', 'PemasukanController@add')->name('insert.cart.pemasukan');
    Route::get('pemasukan/cart/delete', 'PemasukanController@del_cart')->name('delete.cart.pemasukan');
    /** pengeluaran*/
    Route::get('transaksi/pengeluaran', 'PengeluaranController@index')->name('pengeluaran');
    Route::post('transaksi/pengeluaran', 'PengeluaranController@index')->name('json.pengeluaran');
    Route::post('transaksi/pengeluaran/insert', 'PengeluaranController@insert')->name('insert.pengeluaran');
    Route::get('transaksi/pengeluaran/edit', 'PengeluaranController@edit')->name('edit.pengeluaran');
    Route::post('transaksi/pengeluaran/update', 'PengeluaranController@update')->name('update.pengeluaran');
    Route::get('transaksi/pengeluaran/delete', 'PengeluaranController@delete')->name('delete.pengeluaran');
    Route::get('pengeluaran/cart', 'PengeluaranController@list')->name('json.cart.pengeluaran');
    Route::post('pengeluaran/cart/insert', 'PengeluaranController@add')->name('insert.cart.pengeluaran');
    Route::get('pengeluaran/cart/delete', 'PengeluaranController@del_cart')->name('delete.cart.pengeluaran');
    /** kegiatan*/
    Route::get('kegiatan', 'KegiatanController@index')->name('kegiatan');
    Route::post('kegiatan', 'KegiatanController@index')->name('json.kegiatan');
    Route::post('kegiatan/insert', 'KegiatanController@insert')->name('insert.kegiatan');
    Route::get('kegiatan/edit', 'KegiatanController@edit')->name('edit.kegiatan');
    Route::post('kegiatan/update', 'KegiatanController@update')->name('update.kegiatan');
    Route::get('kegiatan/delete', 'KegiatanController@delete')->name('delete.kegiatan');
});

/** pembina */
// Route::middleware(['auth:user', 'ceklevel:pembina'])->group(function () {
// });

/** kemahasiswaan */
Route::middleware(['auth:user', 'ceklevel:kemahasiswaan'])->group(function () {
    /** user*/
    Route::get('user', 'UserController@index')->name('user');
    Route::post('user', 'UserController@index')->name('json.user');
    Route::get('user/edit', 'UserController@edit')->name('edit.user');
    Route::post('user/update', 'UserController@update')->name('update.user');
    /** ukms*/
    Route::get('ukms', 'UkmController@index')->name('ukms');
    Route::post('ukms', 'UkmController@index')->name('json.ukms');
    Route::post('ukms/insert', 'UkmController@insert')->name('insert.ukms');
    Route::get('ukms/edit', 'UkmController@edit')->name('edit.ukms');
    Route::get('ukms/delete', 'UkmController@delete')->name('delete.ukms');
    Route::post('ukms/update', 'UkmController@update')->name('update.ukms');
    /** mahasiswa*/
    Route::get('mahasiswa', 'MahasiswaController@index')->name('mahasiswa');
    Route::post('mahasiswa', 'MahasiswaController@index')->name('json.mahasiswa');
    Route::post('insertmahasiswa', 'MahasiswaController@insert')->name('insert.mahasiswa');
    Route::get('mahasiswa/edit', 'MahasiswaController@edit')->name('edit.mahasiswa');
    Route::post('mahasiswa/update', 'MahasiswaController@update')->name('update.mahasiswa');
    Route::get('mahasiswa/delete', 'MahasiswaController@delete')->name('delete.mahasiswa');
    Route::get('mahasiswa/ukm/{id}', 'MahasiswaController@ukm')->name('ukm.mahasiswa');
    Route::post('mahasiswa/add/ukm', 'MahasiswaController@ukm_post')->name('ukm_post.mahasiswa');
    Route::get('mahasiswa/edit/ukm', 'MahasiswaController@ukm_edit')->name('ukm_edit.mahasiswa');
    Route::post('mahasiswa/update/ukm', 'MahasiswaController@ukm_update')->name('ukm_update.mahasiswa');
    Route::get('mahasiswa/delete/ukm', 'MahasiswaController@ukm_delete')->name('ukm_delete.mahasiswa');
    /** pembina*/
    Route::get('pembina', 'PembinaController@index')->name('pembina');
    Route::post('pembina', 'PembinaController@index')->name('json.pembina');
    Route::post('insertpembina', 'PembinaController@insert')->name('insert.pembina');
    Route::get('pembina/edit', 'PembinaController@edit')->name('edit.pembina');
    Route::post('pembina/update', 'PembinaController@update')->name('update.pembina');
    Route::get('pembina/delete', 'PembinaController@delete')->name('delete.pembina');

    Route::get('pembina/ukm/{id}', 'PembinaController@ukm')->name('ukm.pembina');
    Route::post('pembina/add/ukm', 'PembinaController@ukm_post')->name('ukm_post.pembina');
    Route::get('pembina/edit/ukm', 'PembinaController@ukm_edit')->name('ukm_edit.pembina');
    Route::post('pembina/update/ukm', 'PembinaController@ukm_update')->name('ukm_update.pembina');
    Route::get('pembina/delete/ukm', 'PembinaController@ukm_delete')->name('ukm_delete.pembina');
});

/** semua */
Route::middleware(['auth:user', 'ceklevel:mahasiswa,pembina,kemahasiswaan'])->group(function () {
    /** dashboard */
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('chart/{tahun1?}/{tahun2?}', 'DashboardController@chart')->name('chart');
    Route::get('chartukm/{tahun1?}/{tahun2?}', 'DashboardController@chartukm')->name('chartukm');
    /** profile*/
    Route::get('profile', 'LoginController@index')->name('profile');
    Route::post('update-akun', 'LoginController@updateData')->name('update.profile');
    Route::get('getprofile', 'LoginController@get')->name('get.profile');
    /** ajax*/
    Route::get('total_keuangan/{table}/{metode}/{bulan?}/{tahun?}', 'Ajax@total_keuangan')->name('ajax.total');
    /** laporan lpj */
    Route::get('laporan/{jenis}', 'LaporanController@index')->name('laporan');
    Route::get('laporan-pdf/{jenis}/{periode}/{id?}/{ukm?}', 'LaporanController@generatePDF')->name('laporan-pdf');
    Route::post('laporan', 'LaporanController@getLaporan')->name('post.laporan');
});


// Login
Route::middleware(['guest'])->group(function () {
    Route::get('login', 'LoginController@getLogin')->name('login');
    Route::post('login', 'LoginController@postLogin');
});

Route::middleware(['auth:user'])->group(function () {
    Route::get('logout', 'LoginController@logout')->name('logout');
});
