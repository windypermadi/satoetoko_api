<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$query = mysqli_query($conn, "SELECT * FROM banner_promo WHERE tanggal_mulai <= NOW() AND 
tanggal_selesai >= NOW() AND status_aktif = 'Y' AND status_banner != '1' ORDER BY tanggal_mulai ASC;");

$result = array();
while ($row = mysqli_fetch_array($query)) {
	array_push($result, array(
		'idbanner'			=> $row['idbanner'],
		'nama_banner'		=> $row['nama_banner'],
		'deskripsi_banner'	=> $row['deskripsi_banner'],
		'link_banner'		=> $row['link_banner'],
		'tanggal_mulai'		=> $row['tanggal_mulai'],
		'tanggal_selesai'	=> $row['tanggal_selesai'],
		'gambar_banner'     => $urlbanner . $row['gambar_banner'],
	));
}

$data[] = array(
	'idbanner'			=> '0',
	'nama_banner'		=> 'default',
	'deskripsi_banner'	=> 'default',
	'link_banner'		=> 'default',
	'tanggal_mulai'		=> '',
	'tanggal_selesai'	=> 'default',
	'gambar_banner'     => $urlbanner . 'default.png',
);

if (isset($result[0])) {
	$response->code = 200;
	$response->message = 'result';
	$response->data = $result;
	$response->json();
	die();
} else {
	$response->code = 200;
	$response->message = 'Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini';
	$response->data = $data;
	$response->json();
	die();
}
mysqli_close($conn);
