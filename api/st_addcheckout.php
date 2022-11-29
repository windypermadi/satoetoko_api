<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$dataraw = json_decode(file_get_contents('php://input'));

$exp_date = date("Y-m-d H:i:s", strtotime("+72 hours"));

$conn->begin_transaction();
$transaction = mysqli_fetch_object($conn->query("SELECT UUID_SHORT() as id"));
$idtransaksi = createID('invoice', 'transaksi', 'TR');
$invoice = id_ke_struk($idtransaksi);

//? ADDRESS
$query_alamat = "SELECT * FROM user_alamat WHERE id = '$dataraw[id_alamat]' AND id_user = '$dataraw[id_user]'";
$getalamat = $conn->query($query_alamat);
$data_alamat = $getalamat->fetch_object();
$label_alamat = $data_alamat->label_alamat;
$alamat = $data_alamat->alamat;
$telepon_penerima = $data_alamat->telepon_penerima;
$nama_penerima = $data_alamat->nama_penerima;
$provinsi = $data_alamat->provinsi;
$kota = $data_alamat->kota;
$kecamatan = $data_alamat->kecamatan;
$kelurahan = $data_alamat->kelurahan;
$gabung_alamat = $data_alamat->nama_penerima . " | " . $data_alamat->telepon_penerima . " " . $data_alamat->alamat
    . "," . $data_alamat->kelurahan . "," . $data_alamat->kecamatan . "," . $data_alamat->kota . "," . $data_alamat->provinsi . "," . $data_alamat->kodepos;

// belum selesai 
$data[] = mysqli_query($conn, "INSERT INTO transaksi SET 
        id_transaksi = '$transaction->id',
        pembuat_transaksi = 'F',
        invoice = '$invoice',
        id_user = '$dataraw->id_user',
        tanggal_transaksi = NOW(),
        catatan_pembeli = '$dataraw->catatan_pembeli',
        label_alamat = '$label_alamat',
        alamat_penerima = '$gabung_alamat',
        nama_penerima = '$nama_penerima',
        telepon_penerima = '$telepon_penerima',
        total_harga_sebelum_diskon = '$dataraw->total'");

$data[] = $conn->query("INSERT INTO transaksi_detail SET 
        id_transaksi_detail = UUID_SHORT(),
        id_transaksi = '$transaction->id',
        id_barang = '$id_user',
        id_master = '$id_master',
        harga_normal = '$harga_normal',
        diskon = '$diskon',
        harga_diskon = '$harga_diskon',
        status_pembelian = '$status',
        fee_toko = '$feeadmin',
        sub_total = '$subtotalfee',
        tgl_create = NOW()");

if (in_array(false, $data)) {
    $response->data = null;
    $response->error(400);
} else {
    $conn->commit();
    $response->data = 'done';
    $response->sukses(200);
    die();
}
