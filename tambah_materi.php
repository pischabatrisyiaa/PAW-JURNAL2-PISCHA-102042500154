<?php
require 'koneksi.php';
require 'fungsi.php';

$daftar_mk = ambil_daftar_mata_kuliah($koneksi);
$data = ['mata_kuliah_id' => '', 'judul' => '', 'isi' => '', 'tautan' => ''];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!token_cocok($_POST['token'] ?? '')) {
        tampil_kesalahan(403, 'Token tidak valid. Data tidak disimpan.', 'tambah_materi.php');
    }

    [$data, $galat] = validasi_materi($_POST, $koneksi);

    if (!$galat) {
        $id_mk = (int) $data['mata_kuliah_id'];
        $stmt = $koneksi->prepare(
            'INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES (?, ?, ?, ?)'
        );
        $stmt->bind_param('isss', $id_mk, $data['judul'], $data['isi'], $data['tautan']);
        $stmt->execute();
        set_pesan('sukses', 'Materi berhasil ditambahkan.');
        alihkan('daftar_materi.php');
    }
}

$aksi = 'tambah_materi.php';
$batal = 'daftar_materi.php';
tampil_header('Tambah materi');
?>
<h1>Tambah materi</h1>
<?php if ($galat): ?>
    <div class="pesan pesan-galat">Data belum tersimpan. Periksa isian yang ditandai.</div>
<?php endif; ?>
<?php require 'form_materi.php'; ?>
<?php tampil_footer(); ?>
