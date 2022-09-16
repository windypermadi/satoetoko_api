<?php

static $password_db = '';
static $IP = 'localhost';
static $user_db = 'root';

define('HOST',$IP);
define('USER',$user_db);
define('PASS',$password_db);
define('DB','sertidemi_db');
$conn = mysqli_connect(HOST,USER,PASS,DB) or die('Unable to Connect');

//database object adi
$hostsi = $IP;
$usersi = $user_db;
$passsi = $password_db;
$dbsi   = "andipubl_sertidemi";
$db = new mysqli($hostsi,$usersi,$passsi,$dbsi);
if ($db->connect_errno) {
	printf("Connect DB failed: %s\n", $db->connect_error);
	exit;die;
}

//mode tes
define('KEY_XENDIT_PUBLIC', 'eG5kX2RldmVsb3BtZW50X0dVTWI5VWh0Q0VIUXdZb3pLY2pseVh0eDcxcVg5RWVHaDFqbFp4YVhic1pwUmJoZk9VRFJoQktnbkFRTk5XUzo=');

//MIDTRANS
// define('MTRANS_MERCHANT_ID', 'G884156136'); // SANDBOX
// define('MTRANS_CLIENT_KEY', 'SB-Mid-client-Vpopf8qtZfYISDts'); // SANDBOX
// define('MTRANS_SERVER_KEY', 'SB-Mid-server-HiKzuWxAiKKktIpvfoSu5qCz'); // SANDBOX
// define('MTRANS_URL', 'https://app.sandbox.midtrans.com/snap/v1/transactions'); //SANDBOX

define('MTRANS_MERCHANT_ID', 'G884156136'); // PROD
define('MTRANS_CLIENT_KEY', 'Mid-client-rcekzDhxXgnaScLS'); // PROD
define('MTRANS_SERVER_KEY', 'Mid-server-g3ziVzUgD0RE2iKLSRxwnmi3'); // PROD
define('MTRANS_URL', 'https://app.midtrans.com/snap/v1/transactions'); //PROD

define('GETWA', '628112845216'); // NOMOR WHATSAPP ADMIN

// $urlmain = "http://103.137.254.78/andibook";
$urlimg = "http://103.137.254.78/andibook/image";
$urlpdf = "http://103.137.254.78/andibook/pdfs";
$urlpromo = "http://andipublisher.com/images/promo/";
$urliklan = "http://andipublisher.com/images/iklan/";
// $urladmin = "http://103.137.254.78/andibook/admin/";
$getprofile = "http://andipublisher.com/images/user/profile/";
$getfotobarang = "http://andipublisher.com/images/barang/";
$geticonkategori = "http://andipublisher.com/images/kategori/";
//sertifikat
$getsertifikat = "http://devsertidemi.andipublsiher.com/sertifikat/sertifikat-assessment.php";
$getsertifikat = "http://devsertidemi.andipublsiher.com/api/sertifikat/sertifikat-event.php";
$getimageevent = "https://devsertidemi.andipublisher.com/image/event/";
$getimageassessment = "https://devsertidemi.andipublisher.com/image/assessment/";
$getimagebanner = "https://devsertidemi.andipublisher.com/image/banner/";
$getimagesponsor = "https://devsertidemi.andipublisher.com/image/sponsor/";
$getimagesertifikat = "https://devsertidemi.andipublisher.com/image/sertifikat/";

$getkategoriicon = "https://devsertidemi.andipublisher.com/image/kategori/";

date_default_timezone_set("ASIA/JAKARTA");

function random_word($id = 20){
	$pool = '1234567890abcdefghijkmnpqrstuvwxyz';
	
	$word = '';
	for ($i = 0; $i < $id; $i++){
		$word .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
	}
	return $word; 
}

function tanggal_indo($tanggal) {
	$bulan = array (1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	$split = explode('-', $tanggal);
	return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
}
$hari_indo = array ( 1 =>    'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'
);

function createID($search, $table, $kode){
          // CREATE ID
	$id_primary = $GLOBALS['conn']->query("SELECT max($search) as maxKode FROM $table");
	$id_primary = $id_primary->fetch_assoc();
	$id_primary = $id_primary['maxKode'];
	
	if(substr($id_primary, 2, 8) != date('Ymd')){
		$noUrut = 0;
	} else {
		$noUrut = (int) substr($id_primary, 10, 10);
		if($noUrut == 9999999999){ $noUrut = 0; } 
		else { $noUrut++; }
	}
	$id_primary = $kode . date('Ymd') . sprintf("%010s", $noUrut);
	return $id_primary;
          // END CREATE ID
}

function id_ke_struk($string){
	$inisial = substr($string, 0,2);
	$tgl = substr($string, 4,6);
	$tgl = date_format(date_create($tgl),"dmy");
	$num = round(substr($string, 10));
	$no_nota = $inisial = $inisial."-".$tgl."-".$num;
	return $no_nota;
}

function respon_json_status_500($pesan = ""){
	http_response_code(500);
	$respon['status'] = "500";
	$word = 'pesan';
	$respon[$word] = $pesan;
	die(json_encode($respon));
	exit();
}

function respon_json_status_200($pesan = ""){
	http_response_code(200);
	$respon['status'] = "200";
	$word = 'pesan';
	$respon[$word] = $pesan;
	die(json_encode($respon));
	exit();
}

function respon_json_status_400($pesan = ""){
	http_response_code(400);
	$respon['status'] = "400";
	$word = 'pesan';
	$respon[$word] = $pesan;
	die(json_encode($respon));
	exit();
}


function generate_referal($string){
	$inisial = substr(MD5($string), 0, 7);
	return $inisial;
}

function generate_referal_lagi(){
	$inisial = substr(MD5(RAND()), 0, 7);
	return $inisial;
}


?>