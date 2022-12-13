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
            $data = $conn->query("SELECT id_transaksi, invoice, tanggal_transaksi, total_harga_setelah_diskon, status_transaksi, kurir_code FROM `transaksi` WHERE id_user = '$id_login'");

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

                $ambilditempat = $key['kurir_code'] == '00' ? 'Ambil Ditempat' : '';

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'invoice' => $key['invoice'],
                    'exp_date' => $exp_date,
                    'total' => $key['total_harga_setelah_diskon'],
                    'total_format' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                    'status_ambil_ditempat' => $ambilditempat
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
            $data = $conn->query("SELECT id_transaksi, invoice, tanggal_transaksi, total_harga_setelah_diskon, status_transaksi, kurir_code FROM `transaksi` WHERE id_user = '$id_login' AND status_transaksi = '3'");

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

                $ambilditempat = $key['kurir_code'] == '00' ? 'Ambil Ditempat' : '';

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'invoice' => $key['invoice'],
                    'exp_date' => $exp_date,
                    'total' => $key['total_harga_setelah_diskon'],
                    'total_format' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                    'status_ambil_ditempat' => $ambilditempat
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
            $data = $conn->query("SELECT id_transaksi, invoice, tanggal_transaksi, total_harga_setelah_diskon, status_transaksi, kurir_code FROM `transaksi` WHERE id_user = '$id_login' AND status_transaksi = '9'");

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

                $ambilditempat = $key['kurir_code'] == '00' ? 'Ambil Ditempat' : '';

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'invoice' => $key['invoice'],
                    'exp_date' => $exp_date,
                    'total' => $key['total_harga_setelah_diskon'],
                    'total_format' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                    'status_ambil_ditempat' => $ambilditempat
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
        case 'detail':
            $id_transaksi         = $_GET['id_transaksi'];
            $data = $conn->query("SELECT * FROM `transaksi` WHERE id_user = '$id_login' AND id_transaksi = '$id_transaksi'")->fetch_object();

            $status_transaksi = $data->status_transaksi;
            $kurir_code = $data->kurir_code;

            // $data->ambil_ditempat;
            // $data->midtrans_transaction_status;
            // $data->midtrans_token;
            // $data->midtrans_redirect_url;

            if ($status_transaksi == '1') {
                $status = 'Menunggu Pembayaran';
            } else if ($status_transaksi == '2') {
                $status = 'Menunggu Verifikasi Pembayaran';
            } else if ($status_transaksi == '3') {
                $status = 'Pembayaran Berhasil';
            } else if ($status_transaksi == '4') {
                $status = 'Pembayaran Tidak Lengkap';
            } else if ($status_transaksi == '5') {
                $status = 'Dikirim';
            } else if ($status_transaksi == '6') {
                $status = 'Diterima';
            } else if ($status_transaksi == '7') {
                $status = 'Transaksi Selesai';
            } else if ($status_transaksi == '8') {
                $status = 'Expired';
            } else if ($status_transaksi == '9') {
                $status = 'Dibatalkan';
            } else if ($status_transaksi == '10') {
                $status = 'Pembayaran Ditolak';
            } else if ($status_transaksi == '11') {
                $status = 'PengembalianBarang';
            } else {
                $status = 'Expired';
            }

            if ($kurir_code == '00') {
                $status_kurir = $data->kurir_pengirim;
                $ambil_ditempat = $data->ambil_ditempat;
            } else {
                $status_kurir = $data->kurir_pengirim;
                $ambil_ditempat = "";
            }

            if ($data->metode_pembayaran == '1') {
                $metode_pembayaran = 'Pembayaran Manual';
            } else if ($data->metode_pembayaran == '2') {
                $metode_pembayaran = 'Pembayaran Otomatis Midtrans';
            } else {
                $metode_pembayaran = 'Belum Memilih Metode Pembayaran';
            }

            $data1['id_transaksi'] = $data->id_transaksi;
            $data1['invoice'] = $data->invoice;
            $data1['total_harga'] = $data->total_harga_setelah_diskon + $data->harga_ongkir;
            $data1['metode_pembayaran'] = $metode_pembayaran;
            $data1['status_transaksi_ket'] = $status;
            $data1['status_transaksi'] = $status_transaksi;
            $data1['tanggal_transaksi'] = $data->tanggal_transaksi;
            $data1['ambil_ditempat'] = $ambil_ditempat;
            $data1['midtrans_payment_type'] = $data->midtrans_payment_type;
            $data1['midtrans_transaction_status'] = $data->midtrans_transaction_status;
            $data1['midtrans_token'] = $data->midtrans_token;
            $data1['midtrans_redirect_url'] = $data->midtrans_redirect_url;

            if ($data1) {
                $response->data = $data1;
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
