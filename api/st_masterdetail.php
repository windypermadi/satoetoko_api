<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_master = $_GET['id_master'];
$data = mysqli_fetch_object($conn->query("SELECT a.id_master, a.judul_master, a.slug_judul_master, d.nama_kategori, a.harga_master, a.diskon_rupiah,
a.diskon_persen, a.harga_sewa, a.diskon_sewa_rupiah, a.diskon_sewa_persen, b.deskripsi_produk, b.status_bahaya, b.merek,
b.status_garansi, b.berat, b.dimensi, b.masa_garansi, b.konsumsi_daya, b.masa_garansi, b.negara_asal, b.tegangan, b.daya_listrik,
b.masa_penyimpanan, b.tanggal_kadaluarsa, a.image_master, b.video_produk, b.gambar_1, b.gambar_2, b.gambar_3 FROM master_item a
JOIN master_fisik_detail b ON a.id_master = b.id_master
JOIN kategori_sub d ON a.id_sub_kategori = d.id_sub
WHERE a.status_approve = '2' AND a.status_aktif = 'Y' AND a.status_hapus = 'N' AND a.id_master = '$id_master'"));

$bukufisik = mysqli_fetch_object($conn->query("SELECT b.penulis, b.penerbit, b.isbn, b.deskripsi, b.sinopsis, b.tahun_terbit, b.edisi,
b.halaman, b.berat, a.image_master, b.gambar_1, b.gambar_2, b.gambar_3 FROM master_item a
JOIN master_buku_detail b ON a.id_master = b.id_master
JOIN kategori_sub d ON a.id_sub_kategori = d.id_sub
WHERE a.status_approve = '2' AND a.status_aktif = 'Y' AND a.status_hapus = 'N' AND a.id_master = '$id_master'"));

$datastok = mysqli_fetch_object($conn->query("SELECT sum(jumlah) as jumlah, alamat_cabang FROM stok a JOIN cabang b ON a.id_warehouse = b.id_cabang WHERE a.id_barang = '$id_master';"));
$warehousedata = $conn->query("SELECT * FROM stok a JOIN cabang b ON a.id_warehouse = b.id_cabang WHERE a.id_barang = '$id_master' AND b.status_aktif = 'Y' AND b.status_hapus = 'N'");
$warehousedatas = array();
foreach ($warehousedata as $key => $value) {
    array_push($warehousedatas, array(
        'id_cabang' => $value['id_cabang'],
        'kode_cabang' => $value['kode_cabang'],
        'nama_cabang' => $value['nama_cabang'],
        'alamat_lengkap_cabang' => $value['alamat_lengkap_cabang'],
        'alamat_cabang' => $value['alamat_cabang'],
        'stok' => $value['jumlah']
    ));
}

$imageurl = $conn->query("SELECT b.image_master, a.video_produk, a.gambar_1, a.gambar_2, a.gambar_3 FROM master_fisik_detail a
JOIN master_item b ON a.id_master = b.id_master WHERE a.id_master = '$data->id_master'");
$imageurls = array();
while ($key = mysqli_fetch_object($imageurl)) {
    array_push($imageurls, array(
        'status_url' => '1',
        'keterangan' => 'image',
        'url' => $getvideofisik . $key->video_produk,
    ));
    array_push($imageurls, array(
        'status_url' => '2',
        'keterangan' => 'video',
        'url' => $getimagefisik . $key->image_master,
    ));
    array_push($imageurls, array(
        'status_url' => '1',
        'keterangan' => 'image',
        'url' => $getimagefisik . $key->gambar_1,
    ));
    array_push($imageurls, array(
        'status_url' => '1',
        'keterangan' => 'image',
        'url' => $getimagefisik . $key->gambar_2,
    ));
    array_push($imageurls, array(
        'status_url' => '1',
        'keterangan' => 'image',
        'url' => $getimagefisik . $key->gambar_3,
    ));
}

// while ($key = mysqli_fetch_object($imageurl)) {
//     array_push($imageurls, array(
//         'gambar_varian' => $key->image_master,
//     ));
// }

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

$status_jenis_harga = '1';

// $varian['jumlah_varian'] = '0';
// $varian['data_varian'] = $imageurls;

// a.id_master, a.judul_master, a.slug_judul_master, d.nama_kategori, a.harga_master, a.diskon_rupiah,
// a.diskon_persen, a.harga_sewa, a.diskon_sewa_rupiah, a.diskon_sewa_persen, b.deskripsi_produk, b.status_bahaya, b.merek,
// b.status_garansi, b.berat, b.dimensi, b.masa_garansi, b.konsumsi_daya, b.masa_garansi, b.negara_asal, b.tegangan, b.daya_listrik,
// b.masa_penyimpanan, b.tanggal_kadaluarsa, a.image_master, b.video_produk, b.gambar_1, b.gambar_2, b.gambar_3

$data1['id_master'] = $data->id_master;
$data1['judul_master'] = $data->judul_master;
$data1['slug_judul_master'] = $data->slug_judul_master;
$data1['deskripsi_produk'] = $data->deskripsi_produk;
$data1['harga_produk'] = $harga_produk;
$data1['harga_tampil'] = $harga_tampil;
$data1['status_diskon'] = $status_diskon;
$data1['status_varian_diskon'] = $status_varian_diskon;
$data1['status_jenis_harga'] = $status_jenis_harga;
$data1['diskon'] = $data->diskon_persen . "%";
$data1['total_dibeli'] = $data->total_dibeli . " terjual";
$data1['rating_item'] = 0;
$data1['status_whislist'] = $status_whislist;
$data1['stok'] = $datastok->jumlah;
$data1['warehouse'] = $warehousedatas;

$data1['status_bahaya'] = $data->status_bahaya;
$data1['merek'] = $data->merek;
$data1['status_garansi'] = $data->status_garansi;
$data1['negara_asal'] = $data->negara_asal;
$data1['tanggal_kadaluarsa'] = $data->tanggal_kadaluarsa;
$data1['berat'] = $data->berat;
$data1['dimensi'] = $data->dimensi;
$data1['masa_garansi'] = $data->masa_garansi;
$data1['masa_penyimpanan'] = $data->masa_penyimpanan;
$data1['dikirim_dari'] = $datastok->alamat_cabang;

$data1['penulis'] = $bukufisik->penulis;
$data1['penerbit'] = $bukufisik->penerbit;
$data1['isbn'] = $bukufisik->isbn;
$data1['deskripsi'] = $bukufisik->deskripsi;
$data1['sinopsis'] = $bukufisik->sinopsis;
$data1['tahun_terbit'] = $bukufisik->tahun_terbit;
$data1['edisi'] = $bukufisik->edisi;
$data1['halaman'] = $bukufisik->halaman;
$data1['berat'] = $bukufisik->berat;
$data1['status_varian'] = $varian;
$data1['url'] = $imageurls;

if ($data) {
    $response->code = 200;
    $response->message = 'success';
    $response->data = $data1;
    $response->json();
    die();
} else {
    $response->code = 400;
    $response->message = mysqli_error($conn);
    $response->data = [];
    $response->json();
    die();
}

mysqli_close($conn);
