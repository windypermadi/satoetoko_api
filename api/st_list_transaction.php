<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_login         = $_GET['id_login'];
$tag              = $_GET['tag'];

$exp_date = date("Y-m-d H:i:s", strtotime("+72 hours"));

if (isset($id_login)) {

    switch ($tag) {
        case 'semua':
            $data = $conn->query("SELECT id_transaksi, invoice, tanggal_transaksi, total_harga_setelah_diskon, status_transaksi FROM `transaksi` WHERE id_user = '$id_login'");

            foreach ($data as $key) {

                $date = date_create($key['tanggal_transaksi']);
                date_add($date,  date_interval_create_from_date_string("3 days"));
                $exp_date = date_format($date, "Y-m-d H:i:s");

                if ($key['status_transaksi'] == '1') {
                    $status_transaksi = 'Menunggu Pembayaran';
                } else if ($key['status_transaksi'] == '2') {
                    $status_transaksi = 'Menunggu Verifikasi Pembayaran';
                } else if ($key['status_transaksi'] == '3') {
                    $status_transaksi = 'Pembayaran Berhasil';
                } else if ($key['status_transaksi'] == '4') {
                    $status_transaksi = 'Pembayaran Tidak Lengkap';
                } else if ($key['status_transaksi'] == '5') {
                    $status_transaksi = 'Dikirim';
                } else if ($key['status_transaksi'] == '6') {
                    $status_transaksi = 'Diterima';
                } else if ($key['status_transaksi'] == '7') {
                    $status_transaksi = 'Transaksi Selesai';
                } else if ($key['status_transaksi'] == '8') {
                    $status_transaksi = 'Expired';
                } else if ($key['status_transaksi'] == '9') {
                    $status_transaksi = 'Dibatalkan';
                } else if ($key['status_transaksi'] == '10') {
                    $status_transaksi = 'Pembayaran Ditolak';
                } else if ($key['status_transaksi'] == '11') {
                    $status_transaksi = 'PengembalianBarang';
                } else {
                    $status_transaksi = 'Expired';
                }

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'invoice' => $key['invoice'],
                    'exp_date' => $exp_date,
                    'total' => $key['total_harga_setelah_diskon'],
                    'total_format' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                ];
            }

            if ($result) {
                $response->data = $result;
                $response->sukses(200);
            } else {
                $response->data = [];
                $response->sukses(200);
            }
            break;
        case 'selesai':
            $data = $conn->query("SELECT id_transaksi, invoice, tanggal_transaksi, total_harga_setelah_diskon, status_transaksi FROM `transaksi` WHERE id_user = '$id_login' AND status_transaksi = '3'");

            foreach ($data as $key) {

                $date = date_create($key['tanggal_transaksi']);
                date_add($date,  date_interval_create_from_date_string("3 days"));
                $exp_date = date_format($date, "Y-m-d H:i:s");

                if ($key['status_transaksi'] == '1') {
                    $status_transaksi = 'Menunggu Pembayaran';
                } else if ($key['status_transaksi'] == '2') {
                    $status_transaksi = 'Menunggu Verifikasi Pembayaran';
                } else if ($key['status_transaksi'] == '3') {
                    $status_transaksi = 'Pembayaran Berhasil';
                } else if ($key['status_transaksi'] == '4') {
                    $status_transaksi = 'Pembayaran Tidak Lengkap';
                } else if ($key['status_transaksi'] == '5') {
                    $status_transaksi = 'Dikirim';
                } else if ($key['status_transaksi'] == '6') {
                    $status_transaksi = 'Diterima';
                } else if ($key['status_transaksi'] == '7') {
                    $status_transaksi = 'Transaksi Selesai';
                } else if ($key['status_transaksi'] == '8') {
                    $status_transaksi = 'Expired';
                } else if ($key['status_transaksi'] == '9') {
                    $status_transaksi = 'Dibatalkan';
                } else if ($key['status_transaksi'] == '10') {
                    $status_transaksi = 'Pembayaran Ditolak';
                } else if ($key['status_transaksi'] == '11') {
                    $status_transaksi = 'PengembalianBarang';
                } else {
                    $status_transaksi = 'Expired';
                }

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'invoice' => $key['invoice'],
                    'exp_date' => $exp_date,
                    'total' => $key['total_harga_setelah_diskon'],
                    'total_format' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                ];
            }

            if ($result) {
                $response->data = $result;
                $response->sukses(200);
            } else {
                $response->data = [];
                $response->sukses(200);
            }
            break;
        case 'batal':
            $data = $conn->query("SELECT id_transaksi, invoice, tanggal_transaksi, total_harga_setelah_diskon, status_transaksi FROM `transaksi` WHERE id_user = '$id_login' AND status_transaksi = '9'");

            foreach ($data as $key) {

                $date = date_create($key['tanggal_transaksi']);
                date_add($date,  date_interval_create_from_date_string("3 days"));
                $exp_date = date_format($date, "Y-m-d H:i:s");

                if ($key['status_transaksi'] == '1') {
                    $status_transaksi = 'Menunggu Pembayaran';
                } else if ($key['status_transaksi'] == '2') {
                    $status_transaksi = 'Menunggu Verifikasi Pembayaran';
                } else if ($key['status_transaksi'] == '3') {
                    $status_transaksi = 'Pembayaran Berhasil';
                } else if ($key['status_transaksi'] == '4') {
                    $status_transaksi = 'Pembayaran Tidak Lengkap';
                } else if ($key['status_transaksi'] == '5') {
                    $status_transaksi = 'Dikirim';
                } else if ($key['status_transaksi'] == '6') {
                    $status_transaksi = 'Diterima';
                } else if ($key['status_transaksi'] == '7') {
                    $status_transaksi = 'Transaksi Selesai';
                } else if ($key['status_transaksi'] == '8') {
                    $status_transaksi = 'Expired';
                } else if ($key['status_transaksi'] == '9') {
                    $status_transaksi = 'Dibatalkan';
                } else if ($key['status_transaksi'] == '10') {
                    $status_transaksi = 'Pembayaran Ditolak';
                } else if ($key['status_transaksi'] == '11') {
                    $status_transaksi = 'PengembalianBarang';
                } else {
                    $status_transaksi = 'Expired';
                }

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'invoice' => $key['invoice'],
                    'exp_date' => $exp_date,
                    'total' => $key['total_harga_setelah_diskon'],
                    'total_format' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                ];
            }

            if ($result) {
                $response->data = $result;
                $response->sukses(200);
            } else {
                $response->data = [];
                $response->sukses(200);
            }
            break;
    }
} else {
    $response->data = null;
    $response->error(400);
}
die();
mysqli_close($conn);
