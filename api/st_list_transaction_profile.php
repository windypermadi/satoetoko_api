<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_login         = $_GET['id_login'];
$tag              = $_GET['tag'];

$exp_date = date("Y-m-d H:i:s", strtotime("+24 hours"));

if (isset($id_login)) {

    switch ($tag) {
        case 'sebelum':
            $data = $conn->query("SELECT a.id_transaksi, e.nama_cabang, c.judul_master, c.image_master, a.invoice, a.tanggal_transaksi, c.harga_master, a.total_harga_setelah_diskon, a.status_transaksi, a.kurir_code, f.keterangan_varian FROM transaksi a
            JOIN transaksi_detail b ON a.id_transaksi = b.id_transaksi
            JOIN master_item c ON b.id_barang = c.id_master 
            JOIN stok d ON c.id_master = d.id_barang 
            JOIN cabang e ON d.id_warehouse = e.id_cabang
            LEFT JOIN variant f ON b.id_barang = f.id_variant
            WHERE a.id_user = '$id_login' AND a.status_transaksi = '1' GROUP BY a.id_transaksi ORDER BY a.tanggal_transaksi DESC");

            //status transaksi | total produk | batas transaksi
            $status_transaksi = 'Menunggu Pembayaran';

            foreach ($data as $key) {

                $cek_jumlah = $conn->query("SELECT sum(jumlah_beli) FROM `transaksi_detail` WHERE `id_transaksi` LIKE '$key[id_transaksi]'")->fetch_assoc();

                $date = date_create($key['tanggal_transaksi']);
                date_add($date,  date_interval_create_from_date_string("3 days"));
                $exp_date = date_format($date, "Y-m-d H:i:s");

                $ambilditempat = $key['kurir_code'] == '00' ? 'Ambil Ditempat' : '';

                $jumlah = $cek_jumlah['jumlah_beli'];

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'exp_date' => $exp_date,
                    'total' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                    'status_ambil_ditempat' => $ambilditempat,
                    'nama_cabang' => $key['nama_cabang'],
                    'judul_master' => $key['judul_master'],
                    'harga_master' => rupiah($key['harga_master']),
                    'image_master' => $getimagefisik . $key['image_master'],
                    'keterangan_varian' => $key['keterangan_varian'],
                    'jumlah_produk' => $jumlah
                ];
            }

            if ($result) {
                $response->data = $result;
                $response->sukses(200);
            } else {
                $response->data = [];
                $response->sukses(200);
            }
            break;
        case 'dikemas':
            $data = $conn->query("SELECT a.id_transaksi, e.nama_cabang, c.judul_master, c.image_master, a.invoice, a.tanggal_transaksi, c.harga_master, a.total_harga_setelah_diskon, a.status_transaksi, a.kurir_code, f.keterangan_varian FROM transaksi a
            JOIN transaksi_detail b ON a.id_transaksi = b.id_transaksi
            JOIN master_item c ON b.id_barang = c.id_master 
            JOIN stok d ON c.id_master = d.id_barang 
            JOIN cabang e ON d.id_warehouse = e.id_cabang
            LEFT JOIN variant f ON b.id_barang = f.id_variant
            WHERE a.id_user = '$id_login' AND a.status_transaksi = '3' GROUP BY a.id_transaksi ORDER BY a.tanggal_transaksi DESC;");

            //status transaksi | total produk | batas transaksi
            $status_transaksi = 'Dikemas';

            foreach ($data as $key) {

                $cek_jumlah = $conn->query("SELECT sum(jumlah_beli) FROM `transaksi_detail` WHERE `id_transaksi` LIKE '$key[id_transaksi]'")->fetch_assoc();

                $date = date_create($key['tanggal_transaksi']);
                date_add($date,  date_interval_create_from_date_string("3 days"));
                $exp_date = date_format($date, "Y-m-d H:i:s");

                $ambilditempat = $key['kurir_code'] == '00' ? 'Ambil Ditempat' : '';

                $jumlah = $cek_jumlah['jumlah_beli'];

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'exp_date' => $exp_date,
                    'total' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                    'status_ambil_ditempat' => $ambilditempat,
                    'nama_cabang' => $key['nama_cabang'],
                    'judul_master' => $key['judul_master'],
                    'harga_master' => rupiah($key['harga_master']),
                    'image_master' => $getimagefisik . $key['image_master'],
                    'keterangan_varian' => $key['keterangan_varian'],
                    'jumlah_produk' => $jumlah
                ];
            }

            if ($result) {
                $response->data = $result;
                $response->sukses(200);
            } else {
                $response->data = [];
                $response->sukses(200);
            }
            break;
        case 'dikirim':
            $data = $conn->query("SELECT a.id_transaksi, e.nama_cabang, c.judul_master, c.image_master, a.invoice, a.tanggal_transaksi, c.harga_master, a.total_harga_setelah_diskon, a.status_transaksi, a.kurir_code, f.keterangan_varian FROM transaksi a
            JOIN transaksi_detail b ON a.id_transaksi = b.id_transaksi
            JOIN master_item c ON b.id_barang = c.id_master 
            JOIN stok d ON c.id_master = d.id_barang 
            JOIN cabang e ON d.id_warehouse = e.id_cabang
            LEFT JOIN variant f ON b.id_barang = f.id_variant
            WHERE a.id_user = '$id_login' AND a.status_transaksi = '5' GROUP BY a.id_transaksi ORDER BY a.tanggal_transaksi DESC");

            //status transaksi | total produk | batas transaksi
            $status_transaksi = 'Dikirim';

            foreach ($data as $key) {

                $cek_jumlah = $conn->query("SELECT sum(jumlah_beli) FROM `transaksi_detail` WHERE `id_transaksi` LIKE '$key[id_transaksi]'")->fetch_assoc();

                $date = date_create($key['tanggal_transaksi']);
                date_add($date,  date_interval_create_from_date_string("3 days"));
                $exp_date = date_format($date, "Y-m-d H:i:s");

                $ambilditempat = $key['kurir_code'] == '00' ? 'Ambil Ditempat' : '';

                $jumlah = $cek_jumlah['jumlah_beli'];

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'exp_date' => $exp_date,
                    'total' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                    'status_ambil_ditempat' => $ambilditempat,
                    'nama_cabang' => $key['nama_cabang'],
                    'judul_master' => $key['judul_master'],
                    'harga_master' => rupiah($key['harga_master']),
                    'image_master' => $getimagefisik . $key['image_master'],
                    'keterangan_varian' => $key['keterangan_varian'],
                    'jumlah_produk' => $jumlah
                ];
            }

            if ($result) {
                $response->data = $result;
                $response->sukses(200);
            } else {
                $response->data = [];
                $response->sukses(200);
            }
            break;
        case 'selesai':
            $data = $conn->query("SELECT a.id_transaksi, e.nama_cabang, c.judul_master, c.image_master, a.invoice, a.tanggal_transaksi, c.harga_master, a.total_harga_setelah_diskon, a.status_transaksi, a.kurir_code, f.keterangan_varian FROM transaksi a
            JOIN transaksi_detail b ON a.id_transaksi = b.id_transaksi
            JOIN master_item c ON b.id_barang = c.id_master 
            JOIN stok d ON c.id_master = d.id_barang 
            JOIN cabang e ON d.id_warehouse = e.id_cabang
            LEFT JOIN variant f ON b.id_barang = f.id_variant
            WHERE a.id_user = '$id_login' AND a.status_transaksi = '7' GROUP BY a.id_transaksi ORDER BY a.tanggal_transaksi DESC;");

            //status transaksi | total produk | batas transaksi
            $status_transaksi = 'Transaksi Selesai';

            foreach ($data as $key) {

                $cek_jumlah = $conn->query("SELECT sum(jumlah_beli) FROM `transaksi_detail` WHERE `id_transaksi` LIKE '$key[id_transaksi]'")->fetch_assoc();

                $date = date_create($key['tanggal_transaksi']);
                date_add($date,  date_interval_create_from_date_string("3 days"));
                $exp_date = date_format($date, "Y-m-d H:i:s");

                $ambilditempat = $key['kurir_code'] == '00' ? 'Ambil Ditempat' : '';

                $jumlah = $cek_jumlah['jumlah_beli'];

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'exp_date' => $exp_date,
                    'total' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                    'status_ambil_ditempat' => $ambilditempat,
                    'nama_cabang' => $key['nama_cabang'],
                    'judul_master' => $key['judul_master'],
                    'harga_master' => rupiah($key['harga_master']),
                    'image_master' => $getimagefisik . $key['image_master'],
                    'keterangan_varian' => $key['keterangan_varian'],
                    'jumlah_produk' => $jumlah
                ];
            }

            if ($result) {
                $response->data = $result;
                $response->sukses(200);
            } else {
                $response->data = [];
                $response->sukses(200);
            }
            break;
        case 'dibatalkan':
            $data = $conn->query("SELECT a.id_transaksi, e.nama_cabang, c.judul_master, c.image_master, a.invoice, a.tanggal_transaksi, c.harga_master, a.total_harga_setelah_diskon, a.status_transaksi, a.kurir_code, f.keterangan_varian FROM transaksi a
            JOIN transaksi_detail b ON a.id_transaksi = b.id_transaksi
            JOIN master_item c ON b.id_barang = c.id_master 
            JOIN stok d ON c.id_master = d.id_barang 
            JOIN cabang e ON d.id_warehouse = e.id_cabang
            LEFT JOIN variant f ON b.id_barang = f.id_variant
            WHERE a.id_user = '$id_login' AND a.status_transaksi = '9' GROUP BY a.id_transaksi ORDER BY a.tanggal_transaksi DESC;");

            //status transaksi | total produk | batas transaksi
            $status_transaksi = 'Transaksi Dibatalkan';

            foreach ($data as $key) {

                $cek_jumlah = $conn->query("SELECT sum(jumlah_beli) FROM `transaksi_detail` WHERE `id_transaksi` LIKE '$key[id_transaksi]'")->fetch_assoc();

                $date = date_create($key['tanggal_transaksi']);
                date_add($date,  date_interval_create_from_date_string("3 days"));
                $exp_date = date_format($date, "Y-m-d H:i:s");

                $ambilditempat = $key['kurir_code'] == '00' ? 'Ambil Ditempat' : '';

                $jumlah = $cek_jumlah['jumlah_beli'];

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'exp_date' => $exp_date,
                    'total' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                    'status_ambil_ditempat' => $ambilditempat,
                    'nama_cabang' => $key['nama_cabang'],
                    'judul_master' => $key['judul_master'],
                    'harga_master' => rupiah($key['harga_master']),
                    'image_master' => $getimagefisik . $key['image_master'],
                    'keterangan_varian' => $key['keterangan_varian'],
                    'jumlah_produk' => $jumlah
                ];
            }

            if ($result) {
                $response->data = $result;
                $response->sukses(200);
            } else {
                $response->data = [];
                $response->sukses(200);
            }
            break;
        case 'dibatalkan':
            $data = $conn->query("SELECT a.id_transaksi, e.nama_cabang, c.judul_master, c.image_master, a.invoice, a.tanggal_transaksi, c.harga_master, a.total_harga_setelah_diskon, a.status_transaksi, a.kurir_code, f.keterangan_varian FROM transaksi a
            JOIN transaksi_detail b ON a.id_transaksi = b.id_transaksi
            JOIN master_item c ON b.id_barang = c.id_master 
            JOIN stok d ON c.id_master = d.id_barang 
            JOIN cabang e ON d.id_warehouse = e.id_cabang
            LEFT JOIN variant f ON b.id_barang = f.id_variant
            WHERE a.id_user = '$id_login' AND a.status_transaksi = '11' GROUP BY a.id_transaksi ORDER BY a.tanggal_transaksi DESC;");

            //status transaksi | total produk | batas transaksi
            $status_transaksi = 'Pengembalian';

            foreach ($data as $key) {

                $cek_jumlah = $conn->query("SELECT sum(jumlah_beli) FROM `transaksi_detail` WHERE `id_transaksi` LIKE '$key[id_transaksi]'")->fetch_assoc();

                $date = date_create($key['tanggal_transaksi']);
                date_add($date,  date_interval_create_from_date_string("3 days"));
                $exp_date = date_format($date, "Y-m-d H:i:s");

                $ambilditempat = $key['kurir_code'] == '00' ? 'Ambil Ditempat' : '';

                $jumlah = $cek_jumlah['jumlah_beli'];

                $result[] = [
                    'id_transaksi' => $key['id_transaksi'],
                    'exp_date' => $exp_date,
                    'total' => rupiah($key['total_harga_setelah_diskon']),
                    'status' => $key['status_transaksi'],
                    'status_transaksi' => $status_transaksi,
                    'status_ambil_ditempat' => $ambilditempat,
                    'nama_cabang' => $key['nama_cabang'],
                    'judul_master' => $key['judul_master'],
                    'harga_master' => rupiah($key['harga_master']),
                    'image_master' => $getimagefisik . $key['image_master'],
                    'keterangan_varian' => $key['keterangan_varian'],
                    'jumlah_produk' => $jumlah
                ];
            }

            if ($result) {
                $response->data = $result;
                $response->sukses(200);
            } else {
                $response->data = [];
                $response->sukses(200);
            }
            break;
        case 'detail':
            $id_transaksi         = $_GET['id_transaksi'];

            if ($id_transaksi) {
                $cektransaksi = $conn->query("SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi'")->num_rows;

                if ($cektransaksi > 0) {
                    $data = $conn->query("SELECT * FROM `transaksi` WHERE id_user = '$id_login' AND id_transaksi = '$id_transaksi'")->fetch_object();

                    $status_transaksi = $data->status_transaksi;
                    $kurir_code = $data->kurir_code;

                    if ($status_transaksi == '1') {
                        $status = 'Menunggu Pembayaran';
                    } else if ($status_transaksi == '2') {
                        $status = 'Menunggu Verifikasi Pembayaran';
                    } else if ($status_transaksi == '3') {
                        $status = 'Pembayaran Berhasil';
                    } else if ($status_transaksi == '4') {
                        $status = 'Pembayaran Tidak Lengkap';
                    } else if ($status_transaksi == '5') {
                        $status = 'Dikirim';
                    } else if ($status_transaksi == '6') {
                        $status = 'Diterima';
                    } else if ($status_transaksi == '7') {
                        $status = 'Transaksi Selesai';
                    } else if ($status_transaksi == '8') {
                        $status = 'Expired';
                    } else if ($status_transaksi == '9') {
                        $status = 'Dibatalkan';
                    } else if ($status_transaksi == '10') {
                        $status = 'Pembayaran Ditolak';
                    } else if ($status_transaksi == '11') {
                        $status = 'PengembalianBarang';
                    } else {
                        $status = 'Expired';
                    }

                    if ($kurir_code == '00') {
                        $status_kurir = $data->kurir_pengirim;
                        $ambil_ditempat = $data->ambil_ditempat;

                        if ($ambil_ditempat == '1') {
                            $ambil_ditempat_ket = 'Belum Dipacking';
                        } else if ($ambil_ditempat == '2') {
                            $ambil_ditempat_ket = 'Masih Diproses';
                        } else if ($ambil_ditempat == '3') {
                            $ambil_ditempat_ket = 'Siap Diambil';
                        } else if ($ambil_ditempat == '4') {
                            $ambil_ditempat_ket = 'Sudah Diambil';
                        } else {
                            $ambil_ditempat_ket = 'Batal';
                        }
                    } else {
                        $status_kurir = $data->kurir_pengirim;
                        $ambil_ditempat = "";
                    }

                    if ($data->metode_pembayaran == '1') {
                        $metode_pembayaran = 'Pembayaran Manual';
                    } else if ($data->metode_pembayaran == '2') {
                        $metode_pembayaran = 'Pembayaran Otomatis Midtrans';
                    } else {
                        $metode_pembayaran = 'Belum Memilih Metode Pembayaran';
                    }

                    //? ADDRESS
                    $query_alamat = "SELECT * FROM user_alamat WHERE status_alamat_utama = 'Y' AND id_user = '$dataraw[id_user]'";
                    $getalamat = $conn->query($query_alamat);
                    $data_alamat = $getalamat->fetch_object();
                    $gabung_alamat = $data_alamat->nama_penerima . " | " . $data_alamat->telepon_penerima . " " . $data_alamat->alamat
                        . "," . $data_alamat->kelurahan . "," . $data_alamat->kecamatan . "," . $data_alamat->kota . "," . $data_alamat->provinsi . "," . $data_alamat->kodepos;
                    $address =
                        [
                            'id_address' => $data_alamat->id,
                            'address' => $gabung_alamat,
                        ];

                    $getproduk = $conn->query("SELECT b.total_harga_sebelum_diskon, b.harga_ongkir, b.total_harga_setelah_diskon, b.voucher_harga, c.judul_master, c.image_master, a.jumlah_beli, a.harga_barang, a.diskon_barang, a.harga_diskon, b.invoice, d.id_variant, d.keterangan_varian, d.diskon_rupiah_varian, d.image_varian, b.status_transaksi, b.kurir_pengirim, b.kurir_code, b.kurir_service, b.metode_pembayaran, b.ambil_ditempat, b.midtrans_transaction_status, b.midtrans_payment_type, b.midtrans_token, b.midtrans_redirect_url FROM transaksi_detail a 
                JOIN transaksi b ON a.id_transaksi = b.id_transaksi
                LEFT JOIN master_item c ON a.id_barang = c.id_master
                LEFT JOIN variant d ON a.id_barang = d.id_variant WHERE a.id_transaksi = '$id_transaksi';");

                    foreach ($getproduk as $key => $value) {
                        if ($value['id_variant'] != NULL) {
                            $judul_master = $value['keterangan_varian'];
                            $image = $value['image_varian'];
                        } else {
                            $judul_master = $value['judul_master'];
                            $image = $value['image_master'];
                        }

                        $getprodukcoba[] = [
                            'id_transaksi' => $id_transaksi,
                            'judul_master' => $judul_master,
                            'image_master' => $image,
                            'jumlah_beli' => "x" . $value['jumlah_beli'],
                            'harga_produk' => rupiah($value['harga_barang']),
                            'harga_tampil' => rupiah($value['harga_barang'])
                        ];
                    }

                    if ($data->metode_pembayaran == '1') {
                        $payment = 'Pembayaran Manual';
                    } else {
                        $payment = $data->midtrans_payment_type;
                    }

                    $data1['id_transaksi'] = $data->id_transaksi;
                    $data1['invoice'] = $data->invoice;
                    $data1['total_harga'] = $data->total_harga_setelah_diskon + $data->harga_ongkir;
                    $data1['metode_pembayaran'] = $metode_pembayaran;
                    $data1['status_transaksi'] = $status_transaksi;
                    $data1['status_transaksi_ket'] = $status;
                    $data1['tanggal_transaksi'] = $data->tanggal_transaksi;
                    $data1['ambil_ditempat'] = $ambil_ditempat;
                    $data1['ambil_ditempat_ket'] = $ambil_ditempat_ket;
                    $data1['midtrans_payment_type'] = $data->midtrans_payment_type;
                    $data1['midtrans_transaction_status'] = $data->midtrans_transaction_status;
                    $data1['midtrans_token'] = $data->midtrans_token;
                    $data1['midtrans_redirect_url'] = $data->midtrans_redirect_url;
                    $data1['data_product'] = $getprodukcoba;

                    $data1['data_address_buyer'] = $data->alamat_penerima;
                    $data1['payment'] = $payment;

                    if ($data1) {
                        $response->data = $data1;
                        $response->sukses(200);
                    } else {
                        $response->data = [];
                        $response->sukses(200);
                    }
                } else {
                    $response->data = null;
                    $response->message = 'nomor transaksi ini tidak ditemukan.';
                    $response->error(400);
                }
            } else {
                $response->data = null;
                $response->message = 'nomor transaksi tidak ada.';
                $response->error(400);
            }
            break;
    }
} else {
    $response->data = null;
    $response->error(400);
}
die();
mysqli_close($conn);
