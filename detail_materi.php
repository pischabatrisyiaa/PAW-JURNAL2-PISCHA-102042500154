<?php
require 'koneksi.php';
require 'fungsi.php';

$id = ambil_id($_GET['id'] ?? '');
if ($id === 0) {
    tampil_kesalahan(400, 'ID tidak valid.', 'daftar_materi.php');
}

$stmt = $koneksi->prepare(
    'SELECT m.id, m.judul, m.isi, m.tautan, mk.kode, mk.nama
     FROM materi m
     JOIN mata_kuliah mk ON mk.id = m.mata_kuliah_id
     WHERE m.id = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$m = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$m) {
    tampil_kesalahan(404, 'Data tidak ditemukan.', 'daftar_materi.php');
}

tampil_header('Detail materi');
?>
<h1>Materi</h1>
<h2><?= e($m['judul']) ?></h2>
<dl class="detail">
    <dt>Mata kuliah</dt><dd><?= e($m['kode'] . ' - ' . $m['nama']) ?></dd>
    <dt>Judul materi</dt><dd><?= e($m['judul']) ?></dd>
    <dt>Isi materi</dt><dd><?= nl2br(e($m['isi'])) ?></dd>
    <dt>Tautan referensi</dt>
    <dd><a href="<?= e($m['tautan']) ?>" target="_blank" rel="noopener noreferrer"><?= e($m['tautan']) ?></a></dd>
</dl>
<div class="aksi">
    <a class="tombol tombol-utama" href="edit_materi.php?id=<?= (int) $m['id'] ?>">Edit data</a>
    <a class="tombol" href="daftar_materi.php">Kembali</a>
</div>
<?php tampil_footer(); ?>
