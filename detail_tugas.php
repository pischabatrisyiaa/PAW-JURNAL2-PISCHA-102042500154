<?php
require 'koneksi.php';
require 'fungsi.php';

$id = ambil_id($_GET['id'] ?? '');
if ($id === 0) {
    tampil_kesalahan(400, 'ID tidak valid.', 'daftar_tugas.php');
}

$stmt = $koneksi->prepare(
    'SELECT t.id, t.judul, t.instruksi, t.tenggat, t.status, mk.kode, mk.nama
     FROM tugas t
     JOIN mata_kuliah mk ON mk.id = t.mata_kuliah_id
     WHERE t.id = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$t) {
    tampil_kesalahan(404, 'Data tidak ditemukan.', 'daftar_tugas.php');
}

tampil_header('Detail tugas');
?>
<h1>Tugas</h1>
<h2><?= e($t['judul']) ?></h2>
<dl class="detail">
    <dt>Mata kuliah</dt><dd><?= e($t['kode'] . ' - ' . $t['nama']) ?></dd>
    <dt>Judul tugas</dt><dd><?= e($t['judul']) ?></dd>
    <dt>Instruksi tugas</dt><dd><?= nl2br(e($t['instruksi'])) ?></dd>
    <dt>Tenggat</dt><dd><?= e(format_tanggal($t['tenggat'])) ?></dd>
    <dt>Status</dt><dd><?= e($t['status']) ?></dd>
</dl>
<div class="aksi">
    <a class="tombol tombol-utama" href="edit_tugas.php?id=<?= (int) $t['id'] ?>">Edit data</a>
    <a class="tombol" href="daftar_tugas.php">Kembali</a>
</div>
<?php tampil_footer(); ?>
