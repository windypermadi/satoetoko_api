<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$tag = $_GET['tag'];

switch ($tag) {
	case "detail":
		$id_master = $_GET['id_master'];

		break;
	case "home":
		$limit = $_GET['limit'];
		$offset = $_GET['offset'];
		$query = mysqli_query($conn, "SELECT * FROM master_item a JOIN master_ebook_detail b 
ON a.id_master = b.id_master ORDER BY a.judul_master ASC LIMIT $offset, $limit");

		$result = array();
		while ($row = mysqli_fetch_array($query)) {
			if ($row['diskon_persen'] != 0) {
				(float)$harga_potongan = (float)$row['harga_master'] * ((float)$row['diskon_persen'] / 100);
				(float)$harga_disc = $row['harga_master'] - $harga_potongan;
				$jumlah_diskon = $row['diskon_rupiah'];
			} else if ($row['diskon_rupiah'] != 0) {
				(float)$harga_disc = $row['harga_master'] - $row['diskon_rupiah'];
				$jumlah_diskon = $row['diskon_rupiah'];
			} else {
				$harga_potongan = 0;
				$jumlah_diskon = "0";
				$harga_disc = (int)$row['harga_master'];
			}

			// if ($row['diskon_sewa_persen'] != 0) {
			// 	(float)$harga_potongan_sewa = (float)$row['harga_sewa'] * ((float)$row['diskon_sewa_persen'] / 100);
			// 	(float)$harga_disc_sewa = $row['harga_sewa'] - $harga_potongan_sewa;
			// 	$jumlah_diskon = $row['diskon_sewa_persen'];
			// } else if ($row['diskon_sewa_persen'] != 0) {
			// 	(float)$harga_disc_sewa = $row['harga_sewa'] - $row['diskon_sewa_rupiah'];
			// 	$jumlah_diskon = $row['diskon_sewa_rupiah'];
			// } else {
			// 	$harga_potongan_sewa = 0;
			// 	$jumlah_diskon = "0";
			// 	$harga_disc_sewa = 0;
			// }

			//beli
			if ($row['status_ebook'] == '1') {
				$status_ebook = '1';
			} else if ($row['status_ebook'] == '2') {
				//sewa
				$status_ebook = '2';
			} else if ($row['status_ebook'] == '3') {
				//beli dan sewa
				$status_ebook = '3';
			} else {
				$status_ebook = '1';
			}
			array_push($result, array(
				'id_master'			=> $row['id_master'],
				'judul_master'		=> $row['judul_master'],
				'image_master'	    => $row['image_master'],
				'rating_ebook' 		=> 0,
				// 'harga_sewa'		=> $row['harga_sewa'],
				// 'diskon_sewa_rupiah' => $row['diskon_sewa_rupiah'],
				// 'diskon_sewa_persen' => $row['diskon_sewa_persen'],
				'status_ebook' 		=> $status_ebook,
				'harga_master'		=> (int)$row['harga_master'],
				'diskon'			=> (int)$row['diskon_persen'],
				// 'harga_beli_potongan' 		=> $harga_potongan,
				'harga_beli_disc' 		=> $harga_disc,
				// 'harga_sewa_potongan' 		=> $harga_potongan_sewa,
				// 'harga_sewa_disc' 		=> $harga_disc_sewa,
			));
		}

		if (isset($result[0])) {
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
		break;
}
mysqli_close($conn);
