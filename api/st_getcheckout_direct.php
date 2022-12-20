<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$dataraw = json_decode(file_get_contents('php://input'));
$dataraw2 = json_decode(file_get_contents('php://input'), true);

//? LIST PRODUK
$dataproduk = $dataraw2["produk"][0];
if (empty($dataproduk['id_variant'])) {
    $que = "SELECT b.id_master, b.judul_master,b.image_master,a.id_variant,
            c.keterangan_varian,b.harga_master, b.diskon_rupiah, c.harga_varian, c.diskon_rupiah_varian, 
            a.qty, c.diskon_rupiah_varian, d.berat as berat_buku, e.berat as berat_fisik, 
            b.status_master_detail, a.id_gudang, COUNT(a.id) as jumlah_produk,
            f.id_supplier FROM user_keranjang a
            JOIN master_item b ON a.id_barang = b.id_master
            LEFT JOIN variant c ON a.id_variant = c.id_variant
            LEFT JOIN master_buku_detail d ON b.id_master = d.id_master
            LEFT JOIN master_fisik_detail e ON b.id_master = e.id_master
            LEFT JOIN supplier f ON b.id_supplier = f.id_supplier
            WHERE a.id_barang = '$dataproduk[id_produk]'";
} else {
    $que = "SELECT b.id_master, b.judul_master,b.image_master,a.id_variant,
            c.keterangan_varian,b.harga_master, b.diskon_rupiah, c.harga_varian, c.diskon_rupiah_varian, 
            a.qty, c.diskon_rupiah_varian, d.berat as berat_buku, e.berat as berat_fisik, 
            b.status_master_detail, a.id_gudang, COUNT(a.id) as jumlah_produk,
            f.id_supplier FROM user_keranjang a
            JOIN master_item b ON a.id_barang = b.id_master
            LEFT JOIN variant c ON a.id_variant = c.id_variant
            LEFT JOIN master_buku_detail d ON b.id_master = d.id_master
            LEFT JOIN master_fisik_detail e ON b.id_master = e.id_master
            LEFT JOIN supplier f ON b.id_supplier = f.id_supplier
            WHERE a.id = '$dataproduk[id_produk]' AND a.id_variant = '$dataproduk[id_variant]'";
}
$getproduk = $conn->query($que)->fetch_object();

if ($getproduk->status_master_detail == '2') {
    $berat += $getproduk->berat_buku * $getproduk->qty;
    $berat_detail = $getproduk->berat_buku * $getproduk->qty;
} else if ($getproduk->status_master_detail == '3') {
    $berat += $getproduk->berat_fisik * $getproduk->qty;
    $berat_detail = $getproduk->berat_fisik * $getproduk->qty;
}

if ($getproduk->id_variant) {
    $diskon = ($getproduk->harga_varian) - ($getproduk->diskon_rupiah_varian);
    $diskon_format = "Rp" . number_format($diskon, 0, ',', '.');
    $harga_varian = "Rp" . number_format($getproduk->harga_varian, 0, ',', '.');
    $getprodukcoba[] = [
        'id_cart' => "",
        'id_master' => $getproduk->id_master,
        'judul_master' => $getproduk->judul_master,
        'image_master' => $getimagefisik . $getproduk->image_master,
        'id_variant' => $dataproduk['id_variant'],
        'keterangan_varian' => $getproduk->keterangan_varian != null ? $getproduk->keterangan_varian : "",
        'qty' => $dataraw->qty,
        'harga_produk' => "Rp" . number_format($getproduk->harga_varian, 0, ',', '.'),
        'harga_tampil' => $getproduk->diskon_rupiah_varian != 0 ? ($diskon_format) : $harga_varian
    ];
} else {
    $diskon = ($getproduk->harga_master) - ($getproduk->diskon_rupiah);
    $diskon_format = "Rp" . number_format($diskon, 0, ',', '.');
    $harga_master = "Rp" . number_format($getproduk->harga_master, 0, ',', '.');
    $getprodukcoba[] = [
        'id_cart' => "",
        'id_master' => $getproduk->id_master,
        'judul_master' => $getproduk->judul_master,
        'image_master' => $getimagefisik . $getproduk->image_master,
        'id_variant' => $dataproduk['id_variant'],
        'keterangan_varian' => $getproduk->keterangan_varian != null ? $getproduk->keterangan_varian : "",
        'qty' => $dataraw->qty,
        'harga_produk' => $getproduk->harga_master,
        'harga_tampil' => $getproduk->diskon_rupiah != 0 ? ($diskon_format) : $harga_master
    ];
}

//? ADDRESS
$query_alamat = "SELECT * FROM user_alamat WHERE status_alamat_utama = 'Y' AND id_user = '$dataraw2[id_user]'";
$getalamat = $conn->query($query_alamat);
$data_alamat = $getalamat->fetch_object();
$gabung_alamat = $data_alamat->nama_penerima . " | " . $data_alamat->telepon_penerima . " " . $data_alamat->alamat
    . "," . $data_alamat->kelurahan . "," . $data_alamat->kecamatan . "," . $data_alamat->kota . "," . $data_alamat->provinsi . "," . $data_alamat->kodepos;
$address =
    [
        'id_address' => $data_alamat->id,
        'address' => $gabung_alamat,
    ];

//? ADDRESS SHIPPER
$query_alamat_shipper = "SELECT * FROM cabang WHERE id_cabang = '$dataraw2[idcabang]'";
$getalamat_shipper = $conn->query($query_alamat_shipper);
$data_alamat_shipper = $getalamat_shipper->fetch_object();
$gabung_alamat_shipper = $data_alamat_shipper->nama_cabang . " | " . $data_alamat_shipper->telepon_cabang . " " . $data_alamat_shipper->alamat_lengkap_cabang
    . "," . $data_alamat_shipper->kelurahan_cabang . "," . $data_alamat_shipper->kecamatan_cabang . "," . $data_alamat_shipper->kota_cabang . "," . $data_alamat_shipper->provinsi_cabang . "," . $data_alamat_shipper->kodepos_cabang;
$address_shipper =
    [
        'id_address' => $data_alamat_shipper->id_cabang,
        'address' => $gabung_alamat_shipper,
    ];

$getdatatotal =
    [
        'subtotal' => (string) (($dataraw2['total'] * $dataraw2['qty']) + $dataongkir['harga']),
        'subtotal_produk' => $dataraw2['total'] * $dataraw2['qty'],
        'subtotal_pengiriman' => "0",
        'subtotal_diskon' => "0",
    ];

$getqtyproduk =
    [
        'count_order' => $getproduk->jumlah_produk,
        'weight' => $berat,
    ];

//? ONGKIR
// $dataongkir = [
//     'layanan' => $dataongkir['layanan'],
//     'estimasi' => "Barang akan sampai dalam " . $dataongkir['estimasi'] . " hari",
//     'harga' => "Rp" . number_format($dataongkir['harga'], 0, ',', '.'),
// ];

$data1['data_address_buyer'] = $address;
$data1['data_address_shipper'] = $address_shipper;
$data1['data_product'] = $getprodukcoba;
$data1['data_qty_product'] = $getqtyproduk;
$data1['data_price'] = $getdatatotal;


$response->data = $data1;
$response->sukses(200);