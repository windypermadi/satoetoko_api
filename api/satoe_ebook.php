<?php 
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$query = mysqli_query($conn, "SELECT * FROM master_item a JOIN master_ebook_detail b 
ON a.id_master = b.id_master ORDER BY a.judul_master ASC");

$result = array();
while($row = mysqli_fetch_array($query)){
	array_push($result,array(
		'id_master'			=> $row['id_master'],
		'judul_master'		=> $row['judul_master'],
		'image_master'	    => $row['image_master'],
		'harga_master'		        => $row['harga_master'],
		'diskon_rupiah'		        => $row['diskon_rupiah'],
		'diskon_persen'		        => $row['diskon_persen'],
		'harga_sewa'		        => $row['harga_sewa'],
		'diskon_sewa_rupiah'		        => $row['diskon_sewa_rupiah'],
		'diskon_sewa_persen'		        => $row['diskon_sewa_persen'],
	));
}

if (isset($result[0])){
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
?>