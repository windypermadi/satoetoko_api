<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_master = $_GET['id_master'];
$data = mysqli_fetch_object($conn->query("SELECT a.id_master, a.judul_master, a.image_master, a.slug_judul_master, d.nama_kategori, a.harga_master, a.diskon_rupiah, 
		a.diskon_persen, a.harga_sewa, a.diskon_sewa_rupiah, a.diskon_sewa_persen, b.deskripsi_produk, b.video_produk, b.status_bahaya, b.merek, 
        b.status_garansi, b.berat, b.dimensi, b.masa_garansi, b.konsumsi_daya, b.masa_garansi, b.negara_asal, b.tegangan, b.daya_listrik, 
        b.masa_penyimpanan, b.tanggal_kadaluarsa FROM master_item a 
		JOIN master_fisik_detail b ON a.id_master = b.id_master
		JOIN kategori_sub d ON a.id_sub_kategori = d.id_sub
		WHERE a.status_approve = '2' AND a.status_aktif = 'Y' AND a.status_hapus = 'N' AND a.id_master = '$id_master';"));

$bukufisik = mysqli_fetch_object($conn->query("SELECT b.penulis, b.penerbit, b.isbn, b.deskripsi, b.sinopsis, b.tahun_terbit, b.edisi, 
		b.halaman, b.berat, b.gambar_1, b.gambar_2, b.gambar_3 FROM master_item a 
		JOIN master_buku_detail b ON a.id_master = b.id_master
		JOIN kategori_sub d ON a.id_sub_kategori = d.id_sub
		WHERE a.status_approve = '2' AND a.status_aktif = 'Y' AND a.status_hapus = 'N' AND a.id_master = '$id_master';"));

$datastok = mysqli_fetch_object($conn->query("SELECT * FROM stok WHERE id_barang = '$id_master'"));

$imageurl = $conn->query("SELECT b.image_master, a.video_produk, a.gambar_1, a.gambar_2, a.gambar_3 FROM master_fisik_detail a
JOIN master_item b ON a.id_master = b.id_master WHERE a.id_master = '$data->id_master'");
$imageurls = array();
while ($key = mysqli_fetch_object($imageurl)) {
    array_push($imageurls, array(
        'image_master' => $key->image_master,
        'video_produk' => $key->video_produk,
        'gambar_1' => $key->gambar_1,
        'gambar_2' => $key->gambar_2,
        'gambar_3' => $key->gambar_3,
    ));
}
//!status buat whislist apakah menjadi whislist atau tidak
// $cekwhislist =
$status_whislist = 'N';

//! untuk varian harga diskon atau enggak
$varian_harga = 'N';
if ($varian_harga == 'N') {

    if ($data->diskon_persen != 0) {
        $status_diskon = 'Y';
        (float)$harga_disc = $data->harga_master - $data->diskon_rupiah;
    } else {
        $status_diskon = 'N';
        (float)$harga_disc = $data->harga_master;
    }

    $harga_produk = "Rp" . number_format($data->harga_master, 0, ',', '.');
    $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.');
} else {

    if ($data->diskon_persen != 0) {
        $status_diskon = 'Y';
        (float)$harga_disc = $data->harga_master - $data->diskon_rupiah;
    } else {
        $status_diskon = 'N';
        (float)$harga_disc = $data->harga_master;
    }

    $harga_produk = "Rp" . number_format($data->harga_master, 0, ',', '.') . " - " . "Rp" . number_format($data->harga_master, 0, ',', '.');
    $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.') . " - " . "Rp" . number_format($harga_disc, 0, ',', '.');
}

$varian_diskon = 'N';
if ($varian_diskon == 'N') {
    $status_varian_diskon = 'OFF';
} else {
    $status_varian_diskon = 'UPTO';
}

$data1['id_master']    = $data->id_master;
$data1['judul_master'] = $data->judul_master;
$data1['slug_judul_master'] = $data->slug_judul_master;
$data1['deskripsi_produk'] = $data->deskripsi_produk;
$data1['harga_produk'] = $harga_produk;
$data1['harga_tampil'] = $harga_tampil;
$data1['status_diskon'] = $status_diskon;
$data1['status_varian_diskon'] = $status_varian_diskon;
$data1['diskon'] = $data->diskon_persen . "%";
$data1['total_dibeli'] = $data->total_dibeli . " terjual";
$data1['rating_item'] = 0;
$data1['status_whislist'] = $status_whislist;
$data1['stok'] = $datastok->jumlah;
$data1['penulis'] = $bukufisik->penulis;
$data1['penerbit'] = $bukufisik->penerbit;
$data1['isbn'] = $bukufisik->isbn;
$data1['image_produk'] = $imageurls;
// $data1['nama_kategori'] = $data->nama_kategori;
// $data1['sinopsis'] = $data->sinopsis;
// $data1['lama_sewa']  = $data->lama_sewa;
// $data1['harga_beli'] = (int)$data->harga_master;
// $data1['diskon_beli'] = (int)$jumlah_diskon;
// $data1['harga_diskon_beli'] = (int)$harga_disc;
// $data1['harga_sewa'] = (int)$data->harga_sewa;
// $data1['diskon_sewa'] = (int)$jumlah_diskon_sewa;
// $data1['harga_diskon_sewa']        = (int)$harga_disc_sewa;

if ($data) {
    $response->code = 200;
    $response->message = 'success';
    $response->data = $data1;
    $response->json();
    die();
} else {
    $response->code = 200;
    $response->message = mysqli_error($conn);
    $response->data = [];
    $response->json();
    die();
}

mysqli_close($conn);
