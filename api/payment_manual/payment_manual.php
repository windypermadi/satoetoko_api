<?php 
require_once('../koneksi.php');

$invoice = $_GET['invoice'];

$cekinvoice = mysqli_query($conn, "SELECT invoice FROM ba_transaksi_ebook WHERE invoice = '$invoice'")->num_rows;
if ($cekinvoice > 0){

    $conn->begin_transaction();
    $sql    = mysqli_query($conn,"SELECT A.id_transaksi_detail, A.status_pembelian, C.lama_sewa FROM ba_transaksi_ebook_detail A 
        JOIN ba_transaksi_ebook B ON A.id_transaksi = B.id_transaksi
        JOIN ba_buku C ON A.id_buku = C.id_buku
        WHERE B.invoice = '$invoice'");
    while($row = mysqli_fetch_array($sql)){
        if ($row['status_pembelian'] == '1'){
            $lama = 3650;
        } else if ($row['status_pembelian'] == '2'){
            $lama = $row['lama_sewa'];
        } 
         $stmt[]    = mysqli_query($conn,"UPDATE ba_transaksi_ebook_detail SET tgl_exp = DATE_ADD(NOW(), INTERVAL '$lama' DAY) WHERE id_transaksi_detail = '$row[id_transaksi_detail]'");
    }

    $stmt[] = $conn->query("UPDATE ba_transaksi_ebook SET status_transaksi= '7', tanggal_dibayar = NOW(), tgl_aktif = DATE_ADD(NOW(), INTERVAL '$lama' DAY), payment_type='".$data['payment_type']."' WHERE invoice = '$invoice'");
    if (in_array(false, $stmt) OR in_array(0, $stmt)) {
        $conn->rollback();
        http_response_code(400);
        $respon['pesan'] = "Gagal edit transaksi.\nKlik `Mengerti` untuk menutup pesan ini";
        die(json_encode($respon)); 
    }else{
        $conn->commit();
        $respon['pesan'] = "Yeyee berhasil edit transasi dengan kode invoice ".$invoice;
        die(json_encode($respon));
    }
} else {
    http_response_code(400);
    $respon['pesan'] = "Nomor Invoice tidak ada";
    die(json_encode($respon)); 
}

mysqli_close($conn);
?>