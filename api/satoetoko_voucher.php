<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$tag = $_GET['tag'];

switch ($tag) {
    case "umum":
        $datalist = array();
        $data = $conn->query("SELECT * FROM voucher WHERE status_voucher = '1' AND tgl_mulai <= CURRENT_DATE() AND tgl_berakhir >= CURRENT_DATE();");
        foreach ($data as $key => $value) {
            array_push($datalist, array(
                'idvoucher' => $value['idvoucher'],
                'kode_voucher' => $value['kode_voucher'],
                'nama_voucher' => $value['nama_voucher'],
                'deskripsi_voucher' => $value['deskripsi_voucher'],
                'nilai_voucher' => $value['nilai_voucher'],
                'minimal_transaksi' => $value['minimal_transaksi'],
                'tgl_mulai' => $value['tgl_mulai'],
                'tgl_berakhir' => $value['tgl_berakhir'],
            ));
        }

        if (isset($datalist[0])) {
            $response->code = 200;
            $response->message = 'result';
            $response->data = $datalist;
            $response->json();
            die();
        } else {
            $response->code = 200;
            $response->message = 'Tidak ada data ditampilkan.';
            $response->data = [];
            $response->json();
            die();
        }
        break;
    case "ongkir":
        $datalist = array();
        $data = $conn->query("SELECT * FROM voucher WHERE status_voucher = '2' AND tgl_mulai <= CURRENT_DATE() AND tgl_berakhir >= CURRENT_DATE();");
        foreach ($data as $key => $value) {
            array_push($datalist, array(
                'idvoucher' => $value['idvoucher'],
                'kode_voucher' => $value['kode_voucher'],
                'nama_voucher' => $value['nama_voucher'],
                'deskripsi_voucher' => $value['deskripsi_voucher'],
                'nilai_voucher' => $value['nilai_voucher'],
                'minimal_transaksi' => $value['minimal_transaksi'],
                'tgl_mulai' => $value['tgl_mulai'],
                'tgl_berakhir' => $value['tgl_berakhir'],
            ));
        }

        if (isset($datalist[0])) {
            $response->code = 200;
            $response->message = 'result';
            $response->data = $datalist;
            $response->json();
            die();
        } else {
            $response->code = 200;
            $response->message = 'Tidak ada data ditampilkan.';
            $response->data = [];
            $response->json();
            die();
        }
        break;
}
