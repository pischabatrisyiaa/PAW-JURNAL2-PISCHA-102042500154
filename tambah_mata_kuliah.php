<?php
require 'koneksi.php';
require 'fungsi.php';

$data = ['kode' => '', 'nama' => '', 'dosen' => '', 'semester' => ''];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!token_cocok($_POST['token'] ?? '')) {
        tampil_kesalahan(403, 'Token tidak valid. Data tidak disimpan.', 'tambah_mata_kuliah.php');
    }

    [$data, $galat] = validasi_mata_kuliah($_POST);

    if (!isset($galat['kode']) && kode_dipakai($koneksi, $data['kode'], 0)) {
        $galat['kode'] = 'Kode mata kuliah sudah digunakan.';
    }

    if (!$galat) {
        $stmt = $koneksi->prepare(
            'INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)'
        );
        $semester = (int) $data['semester'];
        $stmt->bind_param('sssi', $data['kode'], $data['nama'], $data['dosen'], $semester);
        $stmt->execute();
        set_pesan('sukses', 'Mata kuliah berhasil ditambahkan.');
        alihkan('daftar_mata_kuliah.php');
    }
}

$aksi = 'tambah_mata_kuliah.php';
$batal = 'daftar_mata_kuliah.php';
tampil_header('Tambah mata kuliah');
?>
<h1>Tambah mata kuliah</h1>
<?php if ($galat): ?>
    <div class="pesan pesan-galat">Data belum tersimpan. Periksa isian yang ditandai.</div>
<?php endif; ?>
<?php require 'form_mata_kuliah.php'; ?>
<?php tampil_footer(); ?>
