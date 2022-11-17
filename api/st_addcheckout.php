<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$dataraw = json_decode(file_get_contents('php://input'));

// $id_user          = $_POST['id_user'];
// $id_produk        = $_POST['id_produk'];
// $voucher          = $_POST['voucher'] ?? '';
// $total            = $_POST['total'];

// $exp_date = date("Y-m-d H:i:s", strtotime("+72 hours"));

// $conn->begin_transaction();
// $transaction = mysqli_fetch_object($conn->query("SELECT UUID_SHORT() as id"));
// $idtransaksi = createID('invoice', 'transaksi', 'TR');
// $invoice = id_ke_struk($idtransaksi);

// $data[] = mysqli_query($conn, "INSERT INTO transaksi SET 
//         id_transaksi = '$transaction->id',
//         pembuat_transaksi = 'F',
//         invoice = '$invoice',
//         id_user = '$dataraw->id_user',
//         tanggal_transaksi = NOW(),
//         total_harga_sebelum_diskon = '$dataraw->total'");

// $data[] = $conn->query("INSERT INTO transaksi_detail SET 
//         id_transaksi_detail = UUID_SHORT(),
//         id_transaksi = '$transaction->id',
//         id_barang = '$id_user',
//         id_master = '$id_master',
//         harga_normal = '$harga_normal',
//         diskon = '$diskon',
//         harga_diskon = '$harga_diskon',
//         status_pembelian = '$status',
//         fee_toko = '$feeadmin',
//         sub_total = '$subtotalfee',
//         tgl_create = NOW()");

if (in_array(false, $data)) {
    $response->data = null;
    $response->error(400);
} else {
    $conn->commit();
    $response->data = 'done';
    $response->sukses(200);
    die();
}
