<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$limit = $_GET['limit'];
$offset = $_GET['offset'];
		$q = $_GET['q'] ?? '';
		if (empty($q)) {
			$datalist = array();
			$data = $conn->query("SELECT a.id_master, a.judul_master, a.image_master, c.nama_kategori, a.harga_master, a.diskon_rupiah, 
				a.diskon_persen, a.harga_sewa, a.diskon_sewa_rupiah, a.diskon_sewa_persen, b.sinopsis,
				b.penerbit, b.tahun_terbit, b.tahun_terbit, b.edisi, b.isbn, b.status_ebook, b.lama_sewa FROM master_item a 
				JOIN master_ebook_detail b ON a.id_master = b.id_master
				JOIN kategori_sub c ON a.id_sub_kategori = c.id_sub WHERE a.status_master_detail = '1' AND a.status_approve = '2' AND a.status_aktif = 'Y' AND a.status_hapus = 'N' ORDER BY a.judul_master ASC LIMIT $offset, $limit");
		} else {
			$datalist = array();
			$data = $conn->query("SELECT a.id_master, a.judul_master, a.image_master, c.nama_kategori, a.harga_master, a.diskon_rupiah, 
				a.diskon_persen, a.harga_sewa, a.diskon_sewa_rupiah, a.diskon_sewa_persen, b.sinopsis,
				b.penerbit, b.tahun_terbit, b.tahun_terbit, b.edisi, b.isbn, b.status_ebook, b.lama_sewa FROM master_item a 
				JOIN master_ebook_detail b ON a.id_master = b.id_master
				JOIN kategori_sub c ON a.id_sub_kategori = c.id_sub WHERE a.status_master_detail = '1' AND a.status_approve = '2' AND a.judul_master LIKE '%$q%' AND a.status_aktif = 'Y' AND a.status_hapus = 'N' ORDER BY a.judul_master ASC LIMIT $offset, $limit");
		}

		foreach ($data as $key => $value) {
			if ($value['status_ebook'] == '1') {
				//beli
				$status_ebook = '1';
			} else if ($value['status_ebook'] == '2') {
				//sewa
				$status_ebook = '2';
			} else if ($value['status_ebook'] == '3') {
				//beli dan sewa
				$status_ebook = '3';
			} else {
				$status_ebook = '1';
			}

			if ($value['diskon_persen'] != 0) {
				(float)$harga_potongan = (float)$value['harga_master'] * ((float)$value['diskon_persen'] / 100);
				(float)$harga_disc = $value['harga_master'] - $harga_potongan;
				$jumlah_diskon = $value['diskon_persen'];
			} else if ($value['diskon_rupiah'] != 0) {
				(float)$harga_disc = $value['harga_master'] - $value['diskon_rupiah'];
				$jumlah_diskon = $value['diskon_rupiah'];
			} else {
				$harga_potongan = 0;
				$jumlah_diskon = "0";
				$harga_disc = (int)$value['harga_master'];
			}

			if ($value['diskon_sewa_persen'] != 0) {
				(float)$harga_potongan_sewa = (float)$value['harga_sewa'] * ((float)$value['diskon_sewa_persen'] / 100);
				(float)$harga_disc_sewa = $value['harga_sewa'] - $harga_potongan;
				$jumlah_diskon_sewa = $value['diskon_sewa_persen'];
			} else if ($value['diskon_sewa_rupiah'] != 0) {
				(float)$harga_disc_sewa = $value['harga_sewa'] - $value['diskon_sewa_rupiah'];
				$jumlah_diskon_sewa = $value['diskon_sewa_rupiah'];
			} else {
				$harga_potongan_sewa = 0;
				$jumlah_diskon_sewa = "0";
				$harga_disc_sewa = (int)$value['harga_sewa'];
			}

			if ($value['harga_sewa'] == '0') {
				$harga_tampil = "Rp" . number_format($value['harga_master'], 0, ',', '.');
			} else {
				$harga_tampil = "Rp" . number_format($value['harga_sewa'], 0, ',', '.') . "-" . "Rp" . number_format($value['harga_master'], 0, ',', '.');
			}

			array_push($datalist, array(
				'id_master' => $value['id_master'],
				'judul_master' => $value['judul_master'],
				'image_master' => $urlimg . $value['image_master'],
				'status_ebook' => $value['status_ebook'],
				'rating_ebook' => 0,
				'nama_kategori' => $value['nama_kategori'],
				'sinopsis' => $value['sinopsis'],
				'lama_sewa' => $value['lama_sewa'],
				'harga_beli' => (int)$value['harga_master'],
				'diskon_beli' => (int)$jumlah_diskon,
				'harga_diskon_beli' => (int)$harga_disc,
				'harga_sewa' => (int)$value['harga_sewa'],
				'diskon_sewa' => (int)$jumlah_diskon_sewa,
				'harga_diskon_sewa' => (int)$harga_disc_sewa,
				'harga_tampil' => $harga_tampil,
			));
		}

$result['nama_list'] = 'Produk Unggulan';
$result['url'] = 'localhost/satoetoko_api/st_item_unggulan.php';
$result['produk_list'] = $result2;

if (isset($result2[0])) {
	$response->code = 200;
	$response->message = 'result';
	$response->data = $result;
	$response->json();
	die();
} else {
	$response->code = 200;
	$response->message = 'Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini';
	$response->data = [];
	$response->json();
	die();
}
mysqli_close($conn);
