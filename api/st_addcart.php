<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_login         = $_POST['id_login'];
$id_cabang        = $_POST['id_cabang'];
$id_master        = $_POST['id_master'];
$id_variant       = $_POST['id_variant'] ?? '';
$jumlah           = $_POST['jumlah'];

$query = mysqli_query($conn, "INSERT INTO user_keranjang SET id = UUID(),
                        id_user='$id_login',
                        id_barang='$id_master',
                        id_variant='$id_variant',
                        id_gudang='$id_cabang',
                        qty='$jumlah'");

if ($query) {
    $response->code = 200;
    $response->message = 'Berhasil dimasukkan ke keranjang';
    $response->data = '';
    $response->json();
} else {
    $response->code = 400;
    $response->message = 'Barang gagal dimasukkan di keranjang.';
    $response->data = '';
    $response->json();
}
die();
mysqli_close($conn);
