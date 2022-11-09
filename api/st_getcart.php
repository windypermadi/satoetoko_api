<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_login         = $_GET['id_login'];

if (isset($id_login)) {

    $data = $conn->query("SELECT * FROM user_keranjang WHERE id_user = '$id_login'");

    if ($data) {
        $datalist = array();

        $data = $cekitemdata->fetch_object();
        $qty = $data->qty;
        $qty = $qty + $jumlah;
        $query = mysqli_query($conn, "UPDATE user_keranjang SET qty = '$qty' WHERE id = '$data->id'");
    } else {
        $qty = $jumlah;
        $query = mysqli_query($conn, "INSERT INTO user_keranjang SET id = UUID(),
                        id_user='$id_login',
                        id_barang='$id_master',
                        id_variant='$id_variant',
                        id_gudang='$id_cabang',
                        qty='$qty'");
    }

    if ($query) {
        $response->data = null;
        $response->sukses(200);
    } else {
        $response->data = null;
        $response->error(400);
    }
} else {
    $response->data = null;
    $response->error(400);
}
die();
mysqli_close($conn);
