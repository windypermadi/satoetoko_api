<?php
require_once('../config/koneksi.php');
include "response.php";
$response = new Response();

$id_kategori = $_GET['id_kategori'] ?? '';
if (empty($id_kategori)) {
    $q = $_GET['q'] ?? '';
    if (empty($q)) {
        $query = mysqli_query($conn, "SELECT * FROM kategori WHERE jenis_kategori = '0' AND status_tampil = 'Y' AND status_hapus = 'N' ORDER BY nama_kategori ASC");
    } else {
        $query = mysqli_query($conn, "SELECT * FROM kategori WHERE jenis_kategori = '0' AND status_tampil = 'Y' AND status_hapus = 'N' AND nama_kategori LIKE '%$q%' ORDER BY nama_kategori ASC");
    }
    foreach ($query as $key => $value) {
        $result[] = [
            'id_kategori'    => $value['id_kategori'],
            'kode_kategori'    => $value['kode_kategori'],
            'nama_kategori'     => $value['nama_kategori'],
            'icon_apps'     => $value['icon_apps'],
        ];
    }
} else {
    $idsub = $_GET['id_sub'] ?? '';
    if (!empty($idsub)) {
        $query = mysqli_query($conn, "SELECT a.id_master, a.image_master, a.judul_master, a.harga_master, a.diskon_rupiah, a.diskon_persen,
        a.total_dibeli, a.total_disukai, SUM(b.jumlah) as jumlah FROM master_item a JOIN stok b ON a.id_master = b.id_barang WHERE a.status_aktif = 'Y' AND a.status_approve = '2' AND a.status_hapus = 'N' AND a.id_sub_kategori = '$idsub' GROUP BY a.id_master ORDER BY a.tanggal_approve DESC");
        foreach ($query as $key => $value) {
            //! untuk varian harga diskon atau enggak
            $varian_harga = 'N';
            if ($varian_harga == 'N') {

                if ($value['diskon_persen'] != 0) {
                    $status_diskon = 'Y';
                    (float)$harga_disc = $value['harga_master'] - $value['diskon_rupiah'];
                } else {
                    $status_diskon = 'N';
                    (float)$harga_disc = $value['harga_master'];
                }

                $harga_produk = "Rp" . number_format($value['harga_master'], 0, ',', '.');
                $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.');
            } else {

                if ($value['diskon_persen'] != 0) {
                    $status_diskon = 'Y';
                    (float)$harga_disc = $value['harga_master'] - $value['diskon_rupiah'];
                } else {
                    $status_diskon = 'N';
                    (float)$harga_disc = $value['harga_master'];
                }

                $harga_produk = "Rp" . number_format($value['harga_master'], 0, ',', '.') . " - " . "Rp" . number_format($value['harga_master'], 0, ',', '.');
                $harga_tampil = "Rp" . number_format($harga_disc, 0, ',', '.') . " - " . "Rp" . number_format($harga_disc, 0, ',', '.');
            }

            $varian_diskon = 'N';
            if ($varian_diskon == 'N') {
                $status_varian_diskon = 'OFF';
            } else {
                $status_varian_diskon = 'UPTO';
            }

            $status_jenis_harga = '1';

            $result2[] = [
                'id_master' => $value['id_master'],
                'judul_master' => $value['judul_master'],
                'image_master' => $getimagefisik . $value['image_master'],
                'harga_produk' => $harga_produk,
                'harga_tampil' => $harga_tampil,
                'status_diskon' => $status_diskon,
                'status_varian_diskon' => $status_varian_diskon,
                'status_jenis_harga' => $status_jenis_harga,
                'status_stok' => $value['jumlah'] > 0 ? 'Y' : 'N',
                'diskon' => $value['diskon_persen'] . "%",
                'total_dibeli' => $value['total_dibeli'] . " terjual",
                'rating_item' => 0,
            ];
        }
    } else {
        $query = mysqli_query($conn, "SELECT * FROM kategori_sub WHERE status_tampil = 'Y' AND status_aktif = 'N' AND parent_kategori = '$id_kategori' ORDER BY nama_kategori ASC");
        foreach ($query as $key => $value) {
            $result[] = [
                'id_kategori'    => $value['id_sub'],
                'kode_kategori'    => $value['kode_kategori'],
                'nama_kategori'     => $value['nama_kategori'],
                'icon_apps'     => $value['icon'],
            ];
        }
    }
}

if (isset($result[0])) {
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
