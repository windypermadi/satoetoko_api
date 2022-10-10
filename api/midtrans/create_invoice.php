<?php
require_once('../../config/koneksi.php');
include "../response.php";
$response = new Response();

$id_transaksi = $_POST['id_transaksi'];
$pembayaran = $_POST['pembayaran'];

$query = mysqli_query($conn, "SELECT * FROM ebook_transaksi a 
JOIN data_user b ON a.id_user = b.id_login
WHERE a.id_transaksi = '$id_transaksi'")->fetch_assoc();
$invoice = $query['invoice'];
$nama_user = $query['nama_user'];
$payer_email = $query['email'];
$no_telp = $query['notelp'];

$mtrans['transaction_details']['order_id'] = $invoice;
$mtrans['transaction_details']['gross_amount'] = $pembayaran;
$mtrans['credit_card']['secure'] = true;
$mtrans['customer_details']['first_name'] = $nama_user;
$mtrans['customer_details']['last_name'] = '';
$mtrans['customer_details']['email'] = $payer_email;
$mtrans['customer_details']['phone'] = $no_telp;
$mtrans_json = json_encode($mtrans);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => MTRANS_URL,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $mtrans_json,
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic '.base64_encode(MTRANS_SERVER_KEY),
    'Content-Type: application/json'
  ),
));

$response_curl = curl_exec($curl);
curl_close($curl);

$response = json_decode($response_curl, true);
$res['token'] = $response['token'];
$res['redirect_url'] = $response['redirect_url'];

if (!isset($response['token'])) {
  respon_json_status_500();
  $respon['pesan'] = "Sorry, we encountered internal server error. We will fix this soon.";
  die(json_encode($respon)); 
}

$payment_url = $response['redirect_url'];
$payment_token = $response['token'];

$query = mysqli_query($conn, "UPDATE ebook_transaksi SET token_payment = '$payment_token', url_payment = '$payment_url' WHERE id_transaksi= '$id_transaksi'");


$res2[] = $res;
echo json_encode($res2);

mysqli_close($conn);
?>