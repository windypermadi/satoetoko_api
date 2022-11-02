<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_master = $_GET['id_master'];
$data = mysqli_fetch_object($conn->query("SELECT a.id_master, a.judul_master, a.image_master, c.nama_kategori, a.harga_master, a.diskon_rupiah, 
		a.diskon_persen, a.harga_sewa, a.diskon_sewa_rupiah, a.diskon_sewa_persen, b.sinopsis,
		b.penerbit, b.tahun_terbit, b.tahun_terbit, b.edisi, b.isbn, b.status_ebook, b.lama_sewa FROM master_item a 
		JOIN master_ebook_detail b ON a.id_master = b.id_master
		JOIN kategori_sub c ON a.id_sub_kategori = c.id_sub 
		WHERE a.status_approve = '2' AND a.status_aktif = 'Y' AND a.status_hapus = 'N' AND a.id_master = '$id_master'"));

$data1['id_master']    = $data->id_master;
$data1['judul_master'] = $data->judul_master;
$data1['image_master'] = $urlimg . $data->image_master;
$data1['status_ebook'] = $data->status_ebook;
$data1['rating_ebook'] = 0;
$data1['nama_kategori'] = $data->nama_kategori;
$data1['sinopsis'] = $data->sinopsis;
$data1['lama_sewa']  = $data->lama_sewa;
$data1['harga_beli'] = (int)$data->harga_master;
$data1['diskon_beli'] = (int)$jumlah_diskon;
$data1['harga_diskon_beli'] = (int)$harga_disc;
$data1['harga_sewa'] = (int)$data->harga_sewa;
$data1['diskon_sewa'] = (int)$jumlah_diskon_sewa;
$data1['harga_diskon_sewa']        = (int)$harga_disc_sewa;

if ($data) {
    $response->code = 200;
    $response->message = 'success';
    $response->data = $data1;
    $response->json();
    die();
} else {
    $response->code = 200;
    $response->message = mysqli_error($conn);
    $response->data = [];
    $response->json();
    die();
}

mysqli_close($conn);
