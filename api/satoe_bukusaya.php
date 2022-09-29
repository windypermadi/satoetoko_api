<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_user                         = $_GET['id_user'];
$limit                           = $_GET['limit'];
$offset                          = $_GET['offset'];

//1 beli 2 sewa
$status                          = $_GET['status'];

switch ($status) {
    case '1':
        $query = mysqli_query($conn, "SELECT c.id_master, c.judul_master, c.image_master, e.url_pdf, d.nama_kategori, a.tgl_expired FROM ebook_transaksi_detail a 
    JOIN ebook_transaksi b ON a.id_transaksi = b.id_transaksi
    JOIN master_item c ON a.id_master = c.id_master
    JOIN kategori_sub d ON c.id_sub_kategori = d.id_sub
    JOIN master_ebook_detail e ON c.id_master = e.id_master 
    WHERE b.id_user = '$id_user' AND a.status_pembelian = '1' AND b.status_transaksi = '7' LIMIT $offset, $limit");

        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, array(
                'id_master' => $row['id_master'],
                'judul_master'   => $row['judul_master'],
                'image_master'   => $urlimg . $row['image_master'],
                'url_pdf' => $urlpdf . $row['url_pdf'],
                'nama_kategori'     => $row['nama_kategori'],
                'tgl_expired'     => '',
            ));
        }

        if (isset($result[0])) {
            $response->code = 200;
            $response->message = 'done';
            $response->data = $result;
            $response->json();
            die();
        } else {
            if ($offset == 0) {
                $response->code = 400;
                $response->message = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
                $response->data = '0';
                $response->json();
                die();
            } else {
                $response->code = 400;
                $response->message = "Ini merupakan halaman terakhir!\nKlik `Mengerti` untuk menutup pesan ini";
                $response->data = '1';
                $response->json();
                die();
            }
        }
        break;
    case '2':
        $query = mysqli_query($conn, "SELECT c.id_master, c.judul_master, c.image_master, e.url_pdf, d.nama_kategori, a.tgl_expired FROM ebook_transaksi_detail a 
    JOIN ebook_transaksi b ON a.id_transaksi = b.id_transaksi
    JOIN master_item c ON a.id_master = c.id_master
    JOIN kategori_sub d ON c.id_sub_kategori = d.id_sub
    JOIN master_ebook_detail e ON c.id_master = e.id_master 
    WHERE b.id_user = '$id_user' AND a.status_pembelian = '2' AND b.status_transaksi = '7' AND a.tgl_expired >= NOW() LIMIT $offset, $limit");

        $result = array();
        while ($row = mysqli_fetch_array($query)) {
            array_push($result, array(
                'id_master' => $row['id_master'],
                'judul_master'   => $row['judul_master'],
                'image_master'   => $urlimg . $row['image_master'],
                'url_pdf' => $urlpdf . $row['url_pdf'],
                'nama_kategori'     => $row['nama_kategori'],
                'tgl_expired'     => $row['tgl_expired'],
            ));
        }

        if (isset($result[0])) {
            $response->code = 200;
            $response->message = 'done';
            $response->data = $result;
            $response->json();
            die();
        } else {
            if ($offset == 0) {
                $response->code = 400;
                $response->message = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
                $response->data = '0';
                $response->json();
                die();
            } else {
                $response->code = 400;
                $response->message = "Ini merupakan halaman terakhir!\nKlik `Mengerti` untuk menutup pesan ini";
                $response->data = '1';
                $response->json();
                die();
            }
        }
        break;
}
mysqli_close($conn);
