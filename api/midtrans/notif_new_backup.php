<?php
require_once('../../config/koneksi.php');
include "../response.php";
$response = new Response();
$get_raw = file_get_contents('php://input');
$query = mysqli_query($conn, "INSERT into midtrans_callback set data = '$get_raw'");

$data = json_decode($get_raw, true);

$date_now = date('Y-m-d H:i:s');
$order_id = $data['order_id'];
$status_code = $data['status_code'];
$gross_amount = $data['gross_amount'];
$server_key = MTRANS_SERVER_KEY;
$signature_key = hash('sha512', $order_id . $status_code . $gross_amount . $server_key);

if ($signature_key != $data['signature_key']) {
    $response->code = 400;
    $response->message = 'signature not valid';
    $response->data = '';
    $response->json();
    die();
}

$data_kepala = $conn->query("SELECT * FROM ebook_transaksi a JOIN data_user b ON a.id_user = b.id_login WHERE a.invoice = '$order_id'")->fetch_assoc();
if (empty($data_kepala)) {
    $response->code = 400;
    $response->message = 'Invoice tidak ditemukan';
    $response->data = '';
    $response->json();
    die();
}

if ($data['transaction_status'] == "settlement" or $data['transaction_status'] == "capture" or $data['transaction_status'] == "accept") {
    $transaction = mysqli_fetch_object($conn->query("SELECT UUID_SHORT() as id"));
    $transaksidetail =  mysqli_query($conn, "SELECT a.id_transaksi, a.id_transaksi_detail, a.status_pembelian, d.lama_sewa, c.id_supplier, a.harga_normal, a.harga_diskon, a.fee_toko, a.sub_total
    FROM ebook_transaksi_detail a 
    JOIN ebook_transaksi b ON a.id_transaksi = b.id_transaksi
    JOIN master_item c ON a.id_master = c.id_master
    JOIN master_ebook_detail d ON c.id_master = d.id_master
    WHERE b.invoice = '$order_id'")->fetch_assoc();
    $cektemp = $conn->query("SELECT * FROM saldo WHERE id_supplier = '$transaksidetail[id_supplier]' ORDER BY tanggal_posting DESC")->num_rows;
    $temp = mysqli_fetch_object($conn->query("SELECT * FROM saldo WHERE id_supplier = '$transaksidetail[id_supplier]' ORDER BY tanggal_posting DESC"));

    if ($cektemp == 0){
        mysqli_query($conn, "INSERT INTO saldo SET 
        id_saldo = '$transaction->id',
        id_supplier = '$transaksidetail[id_supplier]',
        keterangan = 'SALDO MASUK TRANSAKSI CUSTOMER',
        id_transaksi = '$transaksidetail[id_transaksi]',
        saldo_masuk = '$transaksidetail[sub_total]',
        saldo_keluar = 0,
        saldo_awal = 0,
        saldo_akhir = '$transaksidetail[sub_total]'"
        );

    } else {
        mysqli_query($conn, "INSERT INTO saldo SET 
        id_saldo = '$transaction->id',
        id_supplier = '$transaksidetail[id_supplier]',
        keterangan = 'SALDO MASUK TRANSAKSI CUSTOMER',
        id_transaksi = '$transaksidetail[id_transaksi]',
        saldo_masuk = '$transaksidetail[sub_total]',
        saldo_keluar = 0,
        saldo_awal = '$temp[saldo_akhir]',
        saldo_akhir = '$temp[saldo_akhir] + $transaksidetail[sub_total]'"
        );
    }

    $conn->begin_transaction();
    $sql    = mysqli_query($conn, "SELECT a.id_transaksi_detail, a.status_pembelian, d.lama_sewa, a.harga_normal, a.harga_diskon, a.fee_toko, a.sub_total
    FROM ebook_transaksi_detail a 
    JOIN ebook_transaksi b ON a.id_transaksi = b.id_transaksi
    JOIN master_item c ON a.id_master = c.id_master
    JOIN master_ebook_detail d ON c.id_master = d.id_master
    WHERE b.invoice = '$order_id'");
    while ($row = mysqli_fetch_array($sql)) {
        if ($row['status_pembelian'] == '1') {
            $lama = 3650;
        } else if ($row['status_pembelian'] == '2') {
            $lama = $row['lama_sewa'];
        }
        $stmt[]    = mysqli_query($conn, "UPDATE ebook_transaksi_detail SET tgl_expired = DATE_ADD(NOW(), 
        INTERVAL '$lama' DAY), fee_toko = '$row[harga_normal]',  WHERE id_transaksi_detail = '$row[id_transaksi_detail]'");
    }
    $stmt[] = $conn->query("UPDATE ebook_transaksi SET status_transaksi= '7', tgl_dibayar = NOW(), payment_type='" . $data['payment_type'] . "' WHERE invoice = '$order_id'");

    // $stmt[] = $conn->query("UPDATE ebook_transaksi SET status_transaksi= '7', tgl_dibayar = NOW(), payment_type='" . $data['payment_type'] . "' WHERE invoice = '$order_id'");
    if (in_array(false, $stmt) or in_array(0, $stmt)) {
        $conn->rollback();
        $response->code = 400;
        $response->message = 'Gagal transaksi.\nKlik `Mengerti` untuk menutup pesan ini';
        $response->data = '';
        $response->json();
        die();
    } else {
        $conn->commit();
        $response->code = 200;
        $response->message = 'Yeyee berhasil melakukan transaksi dengan kode invoice ' . $invoice;
        $response->data = '';
        $response->json();
        die();
    }
} else if ($data['transaction_status'] == "pending") {
    $query = mysqli_query($conn, "UPDATE ebook_transaksi SET status_transaksi= '1', tgl_dibayar = NOW(), payment_type='" . $data['payment_type'] . "' WHERE invoice = '$order_id'");
} else if ($data['transaction_status'] == "expire") {
    $query = mysqli_query($conn, "UPDATE ebook_transaksi SET status_transaksi= '8', tgl_dibayar = NOW(), payment_type='" . $data['payment_type'] . "' WHERE invoice = '$order_id'");
} else if ($data['transaction_status'] == "deny") {
    $query = mysqli_query($conn, "UPDATE ebook_transaksi SET status_transaksi= '9', tgl_dibayar = NOW(), payment_type='" . $data['payment_type'] . "' WHERE invoice = '$order_id'");
} else {
    $query = mysqli_query($conn, "UPDATE ebook_transaksi SET status_transaksi= '9', tgl_dibayar = NOW(), payment_type='" . $data['payment_type'] . "' WHERE invoice = '$order_id'");
}

mysqli_close($conn);
