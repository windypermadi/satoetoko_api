<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$limit = $_GET['limit'];
$offset = $_GET['offset'];

$result2 = array();
$data = $conn->query("SELECT id_master, image_master, judul_master, harga_master, diskon_rupiah, diskon_persen,
total_dibeli, total_disukai FROM master_item WHERE status_aktif = 'Y' AND status_approve = '2' AND status_hapus = 'N' ORDER BY tanggal_approve DESC LIMIT $offset, $limit");
foreach ($data as $key => $value) {

    //! untuk varian harga diskon atau enggak
    $varian_harga = 'N';
    if ($varian_harga == 'N') {

        if ($value['diskon_persen'] != 0) {
            $status_diskon = 'Y';
            (float)$harga_disc = $value['harga_master'] - $value['diskon_rupiah'];
        } else {
            $status_diskon = 'N';
            (float)$harga_disc = $value['harga_master'];
        }

        $harga_produk = "Rp" . number_format($value['harga_master'], 0, ',', '.');
        $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.');
    } else {

        if ($value['diskon_persen'] != 0) {
            $status_diskon = 'Y';
            (float)$harga_disc = $value['harga_master'] - $value['diskon_rupiah'];
        } else {
            $status_diskon = 'N';
            (float)$harga_disc = $value['harga_master'];
        }

        $harga_produk = "Rp" . number_format($value['harga_master'], 0, ',', '.') . " - " . "Rp" . number_format($value['harga_master'], 0, ',', '.');
        $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.') . " - " . "Rp" . number_format($harga_disc, 0, ',', '.');
    }

    $varian_diskon = 'N';
    if ($varian_diskon == 'N') {
        $status_varian_diskon = 'OFF';
    } else {
        $status_varian_diskon = 'UPTO';
    }

    $status_jenis_harga = '1';

    array_push($result2, array(
        'id_master' => $value['id_master'],
        'judul_master' => $value['judul_master'],
        'image_master' => $getimagefisik . $value['image_master'],
        'harga_produk' => $harga_produk,
        'harga_tampil' => $harga_tampil,
        'status_diskon' => $status_diskon,
        'status_varian_diskon' => $status_varian_diskon,
        'status_jenis_harga' => $status_jenis_harga,
        'diskon' => $value['diskon_persen'] . "%",
        'total_dibeli' => $value['total_dibeli'] . " terjual",
        'rating_item' => 0,
    ));
}

$result['nama_list'] = 'New Arrival';
$result['url'] = 'localhost/satoetoko_api/st_item_unggulan.php';
$result['produk_list'] = $result2;

if (isset($result2[0])) {
    $response->code = 200;
    $response->message = 'result';
    $response->data = $result;
    $response->json();
    die();
} else {
    $response->code = 200;
    $response->message = 'Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini';
    $response->data = [];
    $response->json();
    die();
}
mysqli_close($conn);
