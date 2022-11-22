<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id         = $_POST['id'];
$jumlah         = $_POST['jumlah'];

if (isset($id)) {

    $getdata = $conn->query("SELECT * FROM user_keranjang WHERE id = '$id'");
    $data = $getdata->fetch_object();

    $query = mysqli_query($conn, "UPDATE user_keranjang SET qty = '$jumlah' WHERE id = '$data->id'");

    $data2 = $conn->query("SELECT * FROM user_keranjang a
    JOIN master_item b ON a.id_barang = b.id_master
    LEFT JOIN variant c ON a.id_variant = c.id_variant
    WHERE a.id = '$id';")->fetch_object();

    if ($data2->status_varian == 'Y') {

        if ($data2->diskon_rupiah_varian != 0) {
            $status_diskon = 'Y';
            $harga_disc = $data2->harga_varian - $data2->diskon_rupiah_varian;
        } else {
            $status_diskon = 'N';
            $harga_disc = $data2->harga_varian;
        }

        $harga_produk = "Rp" . number_format($data2->harga_varian, 0, ',', '.');
        $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.');
        $harga_produk_int = $data2->harga_varian;
        $harga_tampil_int = $harga_disc;
    } else {

        if ($data2->diskon_persen != 0) {
            $status_diskon = 'Y';
            $harga_disc = $data2->harga_master - $data2->diskon_rupiah;
        } else {
            $status_diskon = 'N';
            $harga_disc = $data2->harga_master;
        }

        $harga_produk = "Rp" . number_format($data2->harga_master, 0, ',', '.');
        $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.');
        $harga_produk_int = $data2->harga_master;
        $harga_produk_int = $harga_disc;
    }

    $data1 = [
        'id' => $data2->id,
        'image_master' => $urlimg . $data2->image_master,
        'judul' => $data2->judul_master,
        'id_varian' => $data2->id_variant,
        'varian' => $data2->keterangan_varian,
        'harga_produk' => $harga_produk,
        'harga_tampil' => $harga_tampil,
        'harga_produk_int' => $harga_produk_int,
        'harga_tampil_int' => $harga_produk_int,
        'status_diskon' => $status_diskon,
        'qty' => $data2->qty,
        'stok_saatini' => $data2->qty,
        'id_cabang' => $data2->id_gudang,
    ];

    if ($jumlah == 0) {

        $getdata = $conn->query("SELECT * FROM user_keranjang WHERE id = '$id'");
        $data = $getdata->fetch_object();

        if ($getdata->num_rows == 0) {
            $response->data = "Nothing data in cart user";
            $response->error(400);
        } else {
            $cekitemdata = $conn->query("DELETE FROM user_keranjang WHERE id = '$id'");

            if ($cekitemdata) {
                $response->data = $data1;
                $response->sukses(200);
            } else {
                $response->data = null;
                $response->error(400);
            }
        }
    } else {

        if ($data2->status_varian == 'Y') {

            if ($data2->diskon_rupiah_varian != 0) {
                $status_diskon = 'Y';
                $harga_disc = $data2->harga_varian - $data2->diskon_rupiah_varian;
            } else {
                $status_diskon = 'N';
                $harga_disc = $data2->harga_varian;
            }

            $harga_produk = "Rp" . number_format($data2->harga_varian, 0, ',', '.');
            $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.');
            $harga_produk_int = $data2->harga_varian;
            $harga_tampil_int = $harga_disc;
        } else {

            if ($data2->diskon_persen != 0) {
                $status_diskon = 'Y';
                $harga_disc = $data2->harga_master - $data2->diskon_rupiah;
            } else {
                $status_diskon = 'N';
                $harga_disc = $data2->harga_master;
            }

            $harga_produk = "Rp" . number_format($data2->harga_master, 0, ',', '.');
            $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.');
            $harga_produk_int = $data2->harga_master;
            $harga_produk_int = $harga_disc;
        }

        $data1 = [
            'id' => $data2->id,
            'image_master' => $urlimg . $data2->image_master,
            'judul' => $data2->judul_master,
            'id_varian' => $data2->id_variant,
            'varian' => $data2->keterangan_varian,
            'harga_produk' => $harga_produk,
            'harga_tampil' => $harga_tampil,
            'harga_produk_int' => $harga_produk_int,
            'harga_tampil_int' => $harga_produk_int,
            'status_diskon' => $status_diskon,
            'qty' => $data2->qty,
            'stok_saatini' => $data2->qty,
            'id_cabang' => $data2->id_gudang,
        ];

        if ($query) {
            $response->data = $data1;
            $response->sukses(200);
        } else {
            $response->data = null;
            $response->error(400);
        }
    }
} else {
    $response->data = null;
    $response->error(400);
}
die();
mysqli_close($conn);
