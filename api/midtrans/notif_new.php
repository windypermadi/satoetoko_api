<?php
require_once('../../config/koneksi.php');

$get_raw = file_get_contents('php://input');
$query = mysqli_query($conn, "INSERT into midtrans_callback set data = '$get_raw'");
die();

$data = json_decode($get_raw, true);

$date_now = date('Y-m-d H:i:s');
$order_id = $data['order_id'];
$status_code = $data['status_code'];
$gross_amount = $data['gross_amount'];
$server_key = MTRANS_SERVER_KEY;
$signature_key = hash('sha512', $order_id.$status_code.$gross_amount.$server_key);

if ($signature_key != $data['signature_key']) {
	respon_json_status_400('signature not valid');
}

$data_kepala = $conn->query("SELECT * FROM ebook_transaksi a JOIN data_user b ON a.id_user = b.id_login WHERE a.invoice = '$order_id'")->fetch_assoc();
if (empty($data_kepala)) {
	respon_json_status_400('Invoice tidak ditemukan');
}

if ($data['transaction_status'] == "settlement" OR $data['transaction_status'] == "capture" OR $data['transaction_status'] == "accept") {

	$conn->begin_transaction();
    $sql    = mysqli_query($conn,"SELECT a.id_transaksi_detail, a.status_pembelian, d.lama_sewa FROM ebook_transaksi_detail a 
    JOIN ebook_transaksi b ON a.id_transaksi = b.id_transaksi
    JOIN master_item c ON a.id_master = c.id_master
    JOIN master_ebook_detail d ON c.id_master = d.id_master
    WHERE b.invoice = '$order_id'");
    while($row = mysqli_fetch_array($sql)){
        if ($row['status_pembelian'] == '1'){
            $lama = 3650;
        } else if ($row['status_pembelian'] == '2'){
            $lama = $row['lama_sewa'];
        } 
         $stmt[]    = mysqli_query($conn,"UPDATE ebook_transaksi_detail SET tgl_expired = DATE_ADD(NOW(), 
         INTERVAL '$lama' DAY) WHERE id_transaksi_detail = '$row[id_transaksi_detail]'");
    }

    $stmt[] = $conn->query("UPDATE ebook_transaksi SET status_transaksi= '7', tanggal_dibayar = NOW(), tgl_aktif = DATE_ADD(NOW(), INTERVAL '$lama' DAY), payment_type='".$data['payment_type']."' WHERE invoice = '$invoice'");
    if (in_array(false, $stmt) OR in_array(0, $stmt)) {
        $conn->rollback();
        http_response_code(400);
        $respon['pesan'] = "Gagal transaksi.\nKlik `Mengerti` untuk menutup pesan ini";
        die(json_encode($respon)); 
    }else{
        $conn->commit();
        $respon['pesan'] = "Yeyee berhasil melakukan transaksi dengan kode invoice ".$invoice;
        die(json_encode($respon));
    }



} else if($data['transaction_status'] == "pending"){
	$query = mysqli_query($conn, "UPDATE ebook_transaksi SET status_transaksi= '1', tgl_dibayar = NOW(), payment_type='".$data['payment_type']."' WHERE invoice = '$order_id'");
} else if ($data['transaction_status'] == "expire"){
	$query = mysqli_query($conn, "UPDATE ebook_transaksi SET status_transaksi= '8', tgl_dibayar = NOW(), payment_type='".$data['payment_type']."' WHERE invoice = '$order_id'");
} else if ($data['transaction_status'] == "deny"){
	$query = mysqli_query($conn, "UPDATE ebook_transaksi SET status_transaksi= '1', tgl_dibayar = NOW(), payment_type='".$data['payment_type']."' WHERE invoice = '$order_id'");
} else {
	$query = mysqli_query($conn, "UPDATE ebook_transaksi SET status_transaksi= '9', tgl_dibayar = NOW(), payment_type='".$data['payment_type']."' WHERE invoice = '$order_id'");
}

mysqli_close($conn);
?>