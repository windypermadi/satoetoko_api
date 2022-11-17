<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_login         = $_POST['id_login'];
$id_cabang        = $_POST['id_cabang'];
$id_master        = $_POST['id_master'];
$id_variant       = $_POST['id_variant'] ?? '';
$jumlah           = $_POST['jumlah'];

if (isset($id_login) && isset($id_cabang) && isset($id_master) && isset($jumlah)) {

    if (empty($id_variant)) {
        $q = "SELECT id,qty FROM user_keranjang WHERE id_user = 
                '$id_login' AND id_barang = '$id_master' AND id_gudang = '$id_cabang'";
        $cekitemdata = $conn->query($q);

        if ($cekitemdata->num_rows > 0) {
            $query = mysqli_query($conn, "UPDATE user_keranjang SET qty = '$jumlah' WHERE id = '$data->id'");
        } else {
            $qty = $jumlah;
            $query = mysqli_query($conn, "INSERT INTO user_keranjang SET id = UUID(),
                        id_user='$id_login',
                        id_barang='$id_master',
                        id_variant='$id_variant',
                        id_gudang='$id_cabang',
                        qty='$jumlah'");
        }
    } else {
        $q = "SELECT id,qty FROM user_keranjang WHERE id_user = 
                '$id_login' AND id_barang = '$id_master' AND id_gudang = '$id_cabang' AND id_variant = '$id_variant'";
        $cekitemdata = $conn->query($q);
        // var_dump($cekitemdata->num_rows);
        // die();

        if ($cekitemdata->num_rows > 0) {
            $query = mysqli_query($conn, "UPDATE user_keranjang SET qty = '$jumlah' WHERE id = '$data->id'");
        } else {
            $query = mysqli_query($conn, "INSERT INTO user_keranjang SET id = UUID(),
                        id_user='$id_login',
                        id_barang='$id_master',
                        id_variant='$id_variant',
                        id_gudang='$id_cabang',
                        qty='$jumlah'");
        }
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
