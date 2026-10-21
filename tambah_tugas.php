<?php
require 'koneksi.php';
require 'fungsi.php';

$daftar_mk = ambil_daftar_mata_kuliah($koneksi);
$data = [
    'mata_kuliah_id' => '',
    'judul'          => '',
    'instruksi'      => '',
    'tenggat'        => '',
    'status'         => 'Draf',
];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!token_cocok($_POST['token'] ?? '')) {
        tampil_kesalahan(403, 'Token tidak valid. Data tidak disimpan.', 'tambah_tugas.php');
    }

    [$data, $galat] = validasi_tugas($_POST, $koneksi);

    if (!$galat) {
        $id_mk = (int) $data['mata_kuliah_id'];
        $stmt = $koneksi->prepare(
            'INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'issss',
            $id_mk,
            $data['judul'],
            $data['instruksi'],
            $data['tenggat'],
            $data['status']
        );
        $stmt->execute();
        set_pesan('sukses', 'Tugas berhasil ditambahkan.');
        alihkan('daftar_tugas.php');
    }
}

$aksi = 'tambah_tugas.php';
$batal = 'daftar_tugas.php';
tampil_header('Tambah tugas');
?>
<h1>Tambah tugas</h1>
<?php if ($galat): ?>
    <div class="pesan pesan-galat">Data belum tersimpan. Periksa isian yang ditandai.</div>
<?php endif; ?>
<?php require 'form_tugas.php'; ?>
<?php tampil_footer(); ?>
