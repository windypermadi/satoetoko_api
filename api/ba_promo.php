<?php 
require_once('koneksi.php');

$query = mysqli_query($conn, "SELECT * FROM ba_promo WHERE tanggal_acara_event >= NOW() AND status_rmv = 'N' ORDER BY tgl_posting DESC");

$result = array();
while($row = mysqli_fetch_array($query)){
	array_push($result,array(
		'id_promo'			=> $row['id_promo'],
		'nama_promo'		=> $row['nama_promo'],
		'file_promo'		=> $urlpromo.$row['file_promo'],
		'link_promo'		=> $row['link_promo'],
		'tgl_posting'		=> $row['tgl_posting'],
		'tgl_habispromo'	=> $row['tgl_habispromo'],
		'status_rmv'		=> $row['status_rmv'],
	));
}

if (isset($result[0])){
	echo json_encode($result);
} else {
	http_response_code(400);
	$respon['pesan'] = "Tidak ada data yang ditampilkan!\nKlik `Mengerti` untuk menutup pesan ini";
	echo json_encode($respon);
}
mysqli_close($conn);
?>