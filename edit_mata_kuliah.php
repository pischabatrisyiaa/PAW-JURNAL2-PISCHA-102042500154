<?php
require 'koneksi.php';
require 'fungsi.php';

$id = ambil_id($_GET['id'] ?? '');
if ($id === 0) {
    tampil_kesalahan(400, 'ID tidak valid.', 'daftar_mata_kuliah.php');
}

$stmt = $koneksi->prepare('SELECT kode, nama, dosen, semester FROM mata_kuliah WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) {
    tampil_kesalahan(404, 'Data tidak ditemukan.', 'daftar_mata_kuliah.php');
}

$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!token_cocok($_POST['token'] ?? '')) {
        tampil_kesalahan(403, 'Token tidak valid. Data tidak disimpan.', 'daftar_mata_kuliah.php');
    }

    [$data, $galat] = validasi_mata_kuliah($_POST);

    if (!isset($galat['kode']) && kode_dipakai($koneksi, $data['kode'], $id)) {
        $galat['kode'] = 'Kode mata kuliah sudah digunakan.';
    }

    if (!$galat) {
        $stmt = $koneksi->prepare(
            'UPDATE mata_kuliah SET kode = ?, nama = ?, dosen = ?, semester = ? WHERE id = ?'
        );
        $semester = (int) $data['semester'];
        $stmt->bind_param('sssii', $data['kode'], $data['nama'], $data['dosen'], $semester, $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            set_pesan('info', 'Tidak ada perubahan pada data.');
        } else {
            set_pesan('sukses', 'Mata kuliah berhasil diubah.');
        }
        alihkan('daftar_mata_kuliah.php');
    }
}

$aksi = 'edit_mata_kuliah.php?id=' . $id;
$batal = 'daftar_mata_kuliah.php';
tampil_header('Edit mata kuliah');
?>
<h1>Edit mata kuliah</h1>
<?php if ($galat): ?>
    <div class="pesan pesan-galat">Data belum tersimpan. Periksa isian yang ditandai.</div>
<?php endif; ?>
<?php require 'form_mata_kuliah.php'; ?>
<?php tampil_footer(); ?>
