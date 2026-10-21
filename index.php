<?php
require 'koneksi.php';
require 'fungsi.php';

$tabel = [
    'mata_kuliah' => 'Mata kuliah',
    'materi'      => 'Materi',
    'tugas'       => 'Tugas',
];

$jumlah = [];
foreach ($tabel as $nama => $label) {
    $stmt = $koneksi->prepare('SELECT COUNT(*) AS jumlah FROM ' . $nama);
    $stmt->execute();
    $baris = $stmt->get_result()->fetch_assoc();
    $jumlah[$nama] = (int) $baris['jumlah'];
    $stmt->close();
}

$daftar = ambil_daftar_mata_kuliah($koneksi);

tampil_header('Beranda');
?>
<h1>Learning Management System</h1>
<p class="ket">Pengelolaan mata kuliah, materi pembelajaran, dan tugas.</p>
<hr>
<div class="ringkasan">
<?php foreach ($tabel as $nama => $label): ?>
    <div>
        <span><?= e($label) ?></span>
        <strong><?= $jumlah[$nama] ?></strong>
    </div>
<?php endforeach; ?>
</div>
<hr>
<h2>Mata kuliah tersedia</h2>
<?php if (count($daftar) === 0): ?>
    <div class="kosong">Belum ada mata kuliah.</div>
<?php else: ?>
    <ul>
    <?php foreach ($daftar as $mk): ?>
        <li><a href="detail_mata_kuliah.php?id=<?= (int) $mk['id'] ?>"><?= e($mk['kode'] . ' - ' . $mk['nama']) ?></a></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
<hr>
<div class="aksi">
    <a class="tombol tombol-utama" href="daftar_mata_kuliah.php">Kelola mata kuliah</a>
    <a class="tombol tombol-utama" href="daftar_materi.php">Kelola materi</a>
    <a class="tombol tombol-utama" href="daftar_tugas.php">Kelola tugas</a>
</div>
<?php tampil_footer(); ?>
