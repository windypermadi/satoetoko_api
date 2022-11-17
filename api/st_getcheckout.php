<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$dataraw = json_decode(file_get_contents('php://input'), true);

//? LIST PRODUK
$dataproduk = $dataraw["produk"];

foreach ($dataproduk as $i => $key) {
    $getproduk[] = $conn->query("SELECT b.judul_master,b.image_master,a.id_variant,
    c.keterangan_varian,b.harga_master, b.diskon_rupiah, c.harga_varian, c.diskon_rupiah_varian, a.qty, c.diskon_rupiah_varian FROM user_keranjang a
JOIN master_item b ON a.id_barang = b.id_master
LEFT JOIN variant c ON a.id_variant = c.id_variant
WHERE a.id = '$key[id_cart]'")->fetch_object();
}
foreach ($getproduk as $u) {
    if ($u->id_variant != null) {
        $diskon = ($u->harga_varian) - ($u->diskon_rupiah_varian);
        $diskon_format = "Rp" . number_format($diskon, 0, ',', '.');
        $harga_varian = "Rp" . number_format($u->harga_varian, 0, ',', '.');
        $getprodukcoba[] = [
            'judul_master' => $u->judul_master,
            'image_master' => $u->image_master,
            'id_variant' => $u->id_variant,
            'keterangan_varian' => $u->keterangan_varian,
            'qty' => $u->qty,
            'harga_produk' => "Rp" . number_format($u->harga_varian, 0, ',', '.'),
            'harga_tampil' => $u->diskon_rupiah_varian != 0 ? ($diskon_format) : $harga_varian
        ];
    } else {
        $diskon = ($u->harga_master) - ($u->diskon_rupiah);
        $diskon_format = "Rp" . number_format($diskon, 0, ',', '.');
        $harga_master = "Rp" . number_format($u->harga_master, 0, ',', '.');
        $getprodukcoba[] = [
            'judul_master' => $u->judul_master,
            'image_master' => $u->image_master,
            'id_variant' => $u->id_variant,
            'keterangan_varian' => $u->keterangan_varian,
            'qty' => $u->qty,
            'harga_produk' => $u->harga_master,
            'harga_tampil' => $u->diskon_rupiah != 0 ? ($diskon_format) : $harga_master

        ];
    }
}

//? ADDRESS
$query_alamat = "SELECT * FROM user_alamat WHERE status_alamat_utama = 'Y' AND id_user = '$dataraw[id_user]'";
$getalamat = $conn->query($query_alamat);
$data_alamat = $getalamat->fetch_object();
$gabung_alamat = $data_alamat->nama_penerima . " | " . $data_alamat->telepon_penerima . " " . $data_alamat->alamat
    . "," . $data_alamat->kelurahan . "," . $data_alamat->kecamatan . "," . $data_alamat->kota . "," . $data_alamat->provinsi . "," . $data_alamat->kodepos;

// foreach ($dataraw->id_produk as $key => $value) {
//     $query = $conn->query("SELECT * FROM master_item a
//     LEFT JOIN variant b ON a.id_master = b.id_master WHERE a.id_master = '$value[id_produk]'
//     ");
// }

$data1['address'] = $gabung_alamat;
$data1['data_product'] = $getprodukcoba;
$data1['count_order'] = '';
$data1['subtotal'] = $dataraw['total'];
$data1['subtotal_produk'] = $dataraw['total'];
$data1['subtotal_pengiriman'] = 'Rp 10.000';
$data1['subtotal_diskon'] = $dataraw['total'];

$response->data = $data1;
$response->sukses(200);

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

// if (in_array(false, $data)) {
//     $response->data = null;
//     $response->error(400);
// } else {
//     $conn->commit();
//     $response->data = 'done';
//     $response->sukses(200);
//     die();
// }
