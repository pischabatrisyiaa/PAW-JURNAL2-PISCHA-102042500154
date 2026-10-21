<?php
require 'koneksi.php';
require 'fungsi.php';

$id = ambil_id($_GET['id'] ?? '');
if ($id === 0) {
    tampil_kesalahan(400, 'ID tidak valid.', 'daftar_tugas.php');
}

$stmt = $koneksi->prepare(
    'SELECT mata_kuliah_id, judul, instruksi, tenggat, status FROM tugas WHERE id = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) {
    tampil_kesalahan(404, 'Data tidak ditemukan.', 'daftar_tugas.php');
}

$daftar_mk = ambil_daftar_mata_kuliah($koneksi);
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!token_cocok($_POST['token'] ?? '')) {
        tampil_kesalahan(403, 'Token tidak valid. Data tidak disimpan.', 'daftar_tugas.php');
    }

    [$data, $galat] = validasi_tugas($_POST, $koneksi);

    if (!$galat) {
        $id_mk = (int) $data['mata_kuliah_id'];
        $stmt = $koneksi->prepare(
            'UPDATE tugas
             SET mata_kuliah_id = ?, judul = ?, instruksi = ?, tenggat = ?, status = ?
             WHERE id = ?'
        );
        $stmt->bind_param(
            'issssi',
            $id_mk,
            $data['judul'],
            $data['instruksi'],
            $data['tenggat'],
            $data['status'],
            $id
        );
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            set_pesan('info', 'Tidak ada perubahan pada data.');
        } else {
            set_pesan('sukses', 'Tugas berhasil diubah.');
        }
        alihkan('daftar_tugas.php');
    }
}

$aksi = 'edit_tugas.php?id=' . $id;
$batal = 'daftar_tugas.php';
tampil_header('Edit tugas');
?>
<h1>Edit tugas</h1>
<?php if ($galat): ?>
    <div class="pesan pesan-galat">Data belum tersimpan. Periksa isian yang ditandai.</div>
<?php endif; ?>
<?php require 'form_tugas.php'; ?>
<?php tampil_footer(); ?>
