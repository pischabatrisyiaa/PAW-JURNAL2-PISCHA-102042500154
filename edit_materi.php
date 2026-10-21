<?php
require 'koneksi.php';
require 'fungsi.php';

$id = ambil_id($_GET['id'] ?? '');
if ($id === 0) {
    tampil_kesalahan(400, 'ID tidak valid.', 'daftar_materi.php');
}

$stmt = $koneksi->prepare('SELECT mata_kuliah_id, judul, isi, tautan FROM materi WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) {
    tampil_kesalahan(404, 'Data tidak ditemukan.', 'daftar_materi.php');
}

$daftar_mk = ambil_daftar_mata_kuliah($koneksi);
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!token_cocok($_POST['token'] ?? '')) {
        tampil_kesalahan(403, 'Token tidak valid. Data tidak disimpan.', 'daftar_materi.php');
    }

    [$data, $galat] = validasi_materi($_POST, $koneksi);

    if (!$galat) {
        $id_mk = (int) $data['mata_kuliah_id'];
        $stmt = $koneksi->prepare(
            'UPDATE materi SET mata_kuliah_id = ?, judul = ?, isi = ?, tautan = ? WHERE id = ?'
        );
        $stmt->bind_param('isssi', $id_mk, $data['judul'], $data['isi'], $data['tautan'], $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            set_pesan('info', 'Tidak ada perubahan pada data.');
        } else {
            set_pesan('sukses', 'Materi berhasil diubah.');
        }
        alihkan('daftar_materi.php');
    }
}

$aksi = 'edit_materi.php?id=' . $id;
$batal = 'daftar_materi.php';
tampil_header('Edit materi');
?>
<h1>Edit materi</h1>
<?php if ($galat): ?>
    <div class="pesan pesan-galat">Data belum tersimpan. Periksa isian yang ditandai.</div>
<?php endif; ?>
<?php require 'form_materi.php'; ?>
<?php tampil_footer(); ?>
