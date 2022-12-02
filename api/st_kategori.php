<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_kategori = $_GET['id_kategori'] ?? '';
if (empty($id_kategori)) {
    $query = mysqli_query($conn, "SELECT * FROM kategori WHERE jenis_kategori = '0' AND status_tampil = 'Y' AND status_hapus = 'N' ORDER BY nama_kategori ASC");
    foreach ($query as $key => $value) {
        $result[] = [
            'id_kategori'    => $value['id_kategori'],
            'kode_kategori'    => $value['kode_kategori'],
            'nama_kategori'     => $value['nama_kategori'],
            'icon_apps'     => $value['icon_apps'],
        ];
    }
} else {
    $query = mysqli_query($conn, "SELECT * FROM kategori_sub WHERE status_tampil = 'Y' AND status_aktif = 'N' AND parent_kategori = '$id_kategori' ORDER BY nama_kategori ASC");
    foreach ($query as $key => $value) {
        $result[] = [
            'id_kategori'    => $value['id_sub'],
            'kode_kategori'    => $value['kode_kategori'],
            'nama_kategori'     => $value['nama_kategori'],
            'icon_apps'     => $value['icon'],
        ];
    }
}

if (isset($result[0])) {
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
