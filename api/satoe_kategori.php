<?php 
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$query = mysqli_query($conn, "SELECT * FROM kategori_sub ORDER BY nama_kategori ASC");

$result = array();
while($row = mysqli_fetch_array($query)){
	array_push($result,array(
		'id_sub'			=> $row['id_sub'],
		'kode_kategori'		=> $row['kode_kategori'],
		'nama_kategori'	    => $row['nama_kategori'],
		'icon'		        => $row['icon'],
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