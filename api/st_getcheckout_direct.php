<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$dataraw = json_decode(file_get_contents('php://input'), true);

//? LIST PRODUK
$dataproduk = $dataraw["produk"][0];
//? LIST ONGKIR
// $dataongkir = $dataraw["ongkir"];

foreach ($dataproduk as $i => $key) {
    $getproduk[] = $conn->query("SELECT b.id_master, b.judul_master,b.image_master,
    c.keterangan_varian,b.harga_master, b.diskon_rupiah, c.harga_varian, c.diskon_rupiah_varian, c.diskon_rupiah_varian, d.berat as berat_buku, e.berat as berat_fisik, 
    b.status_master_detail FROM master_item b LEFT JOIN variant c ON b.id_master = c.id_master
LEFT JOIN master_buku_detail d ON b.id_master = d.id_master
LEFT JOIN master_fisik_detail e ON b.id_master = e.id_master
WHERE b.id_master = '$dataproduk[id_produk]'")->fetch_object();
}
foreach ($getproduk as $u) {
    if ($u->status_master_detail == '2') {
        $berat += $u->berat_buku * $u->qty;
    } else if ($u->status_master_detail == '3') {
        $berat += $u->berat_fisik * $u->qty;
    }
    if ($u->id_variant != null) {
        $diskon = ($u->harga_varian) - ($u->diskon_rupiah_varian);
        $diskon_format = "Rp" . number_format($diskon, 0, ',', '.');
        $harga_varian = "Rp" . number_format($u->harga_varian, 0, ',', '.');
        $getprodukcoba[] = [
            'id_cart' => $key['id_produk'],
            'id_master' => $u->id_master,
            'judul_master' => $u->judul_master,
            'image_master' => $u->image_master,
            // 'id_variant' => $u->id_variant,
            // 'keterangan_varian' => $u->keterangan_varian != null ? $u->keterangan_varian : "",
            // 'qty' => $u->qty,
            // 'harga_produk' => "Rp" . number_format($u->harga_varian, 0, ',', '.'),
            // 'harga_tampil' => $u->diskon_rupiah_varian != 0 ? ($diskon_format) : $harga_varian
        ];
    } else {
        $diskon = ($u->harga_master) - ($u->diskon_rupiah);
        $diskon_format = "Rp" . number_format($diskon, 0, ',', '.');
        $harga_master = "Rp" . number_format($u->harga_master, 0, ',', '.');
        $getprodukcoba[] = [
            'id_cart' => $key['id_cart'],
            'id_master' => $u->id_master,
            'judul_master' => $u->judul_master,
            'image_master' => $u->image_master,
            // 'id_variant' => $u->id_variant,
            // 'keterangan_varian' => $u->keterangan_varian != null ? $u->keterangan_varian : "",
            // 'qty' => $u->qty,
            // 'harga_produk' => $u->harga_master,
            // 'harga_tampil' => $u->diskon_rupiah != 0 ? ($diskon_format) : $harga_master
        ];
    }
}

//? ADDRESS
$query_alamat = "SELECT * FROM user_alamat WHERE status_alamat_utama = 'Y' AND id_user = '$dataraw[id_user]'";
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
$query_alamat_shipper = "SELECT * FROM cabang WHERE id_cabang = '$dataraw[idcabang]'";
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
        'subtotal' => (string) ($dataraw['total'] + $dataongkir['harga']),
        'subtotal_produk' => $dataraw['total'],
        'subtotal_pengiriman' => "0",
        'subtotal_diskon' => "0",
    ];

$getqtyproduk =
    [
        'count_order' => $u->jumlah_produk,
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
