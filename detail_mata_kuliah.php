<?php
require 'koneksi.php';
require 'fungsi.php';

$id = ambil_id($_GET['id'] ?? '');
if ($id === 0) {
    tampil_kesalahan(400, 'ID tidak valid.', 'daftar_mata_kuliah.php');
}

$stmt = $koneksi->prepare('SELECT id, kode, nama, dosen, semester FROM mata_kuliah WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$mk = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$mk) {
    tampil_kesalahan(404, 'Data tidak ditemukan.', 'daftar_mata_kuliah.php');
}

$stmt = $koneksi->prepare('SELECT id, judul FROM materi WHERE mata_kuliah_id = ? ORDER BY judul');
$stmt->bind_param('i', $id);
$stmt->execute();
$hasil_materi = $stmt->get_result();

$stmt2 = $koneksi->prepare('SELECT id, judul, tenggat, status FROM tugas WHERE mata_kuliah_id = ? ORDER BY tenggat');
$stmt2->bind_param('i', $id);
$stmt2->execute();
$hasil_tugas = $stmt2->get_result();

tampil_header('Detail mata kuliah');
?>
<h1>Mata kuliah</h1>
<h2><?= e($mk['nama']) ?></h2>
<dl class="detail">
    <dt>Kode</dt><dd><?= e($mk['kode']) ?></dd>
    <dt>Nama</dt><dd><?= e($mk['nama']) ?></dd>
    <dt>Dosen</dt><dd><?= e($mk['dosen']) ?></dd>
    <dt>Semester</dt><dd><?= (int) $mk['semester'] ?></dd>
</dl>
<div class="aksi">
    <a class="tombol tombol-utama" href="edit_mata_kuliah.php?id=<?= (int) $mk['id'] ?>">Edit data</a>
    <a class="tombol" href="daftar_mata_kuliah.php">Kembali</a>
</div>

<h2>Materi</h2>
<?php if ($hasil_materi->num_rows === 0): ?>
    <div class="kosong">Belum ada materi.</div>
<?php else: ?>
    <ul>
    <?php while ($m = $hasil_materi->fetch_assoc()): ?>
        <li><a href="detail_materi.php?id=<?= (int) $m['id'] ?>"><?= e($m['judul']) ?></a></li>
    <?php endwhile; ?>
    </ul>
<?php endif; ?>

<h2>Tugas</h2>
<?php if ($hasil_tugas->num_rows === 0): ?>
    <div class="kosong">Belum ada tugas.</div>
<?php else: ?>
    <ul>
    <?php while ($t = $hasil_tugas->fetch_assoc()): ?>
        <li>
            <a href="detail_tugas.php?id=<?= (int) $t['id'] ?>"><?= e($t['judul']) ?></a>
            (tenggat <?= e(format_tanggal($t['tenggat'])) ?>, <?= e($t['status']) ?>)
        </li>
    <?php endwhile; ?>
    </ul>
<?php endif; ?>
<?php
$stmt->close();
$stmt2->close();
tampil_footer();
