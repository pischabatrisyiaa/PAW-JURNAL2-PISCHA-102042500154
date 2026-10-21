<?php
require 'koneksi.php';
require 'fungsi.php';

$cari = ambil_cari();
$kata = kata_like($cari);

$stmt = $koneksi->prepare(
    'SELECT id, kode, nama, dosen, semester
     FROM mata_kuliah
     WHERE kode LIKE ? OR nama LIKE ?
     ORDER BY nama'
);
$stmt->bind_param('ss', $kata, $kata);
$stmt->execute();
$hasil = $stmt->get_result();
$jumlah = $hasil->num_rows;

tampil_header('Mata kuliah');
?>
<h1>Mata kuliah</h1>
<div class="baris-atas">
    <span class="ket">Menampilkan <?= $jumlah ?> data.</span>
    <a class="tombol tombol-utama" href="tambah_mata_kuliah.php">Tambah mata kuliah</a>
</div>

<form class="cari" method="get" action="daftar_mata_kuliah.php">
    <input type="text" name="cari" value="<?= e($cari) ?>" placeholder="Cari kode atau nama">
    <button class="tombol tombol-utama" type="submit">Cari</button>
    <a class="tombol" href="daftar_mata_kuliah.php">Reset</a>
</form>

<?php if ($jumlah === 0): ?>
    <div class="kosong">Data tidak ditemukan.</div>
<?php else: ?>
    <table class="tabel">
        <tr>
            <th>Mata kuliah</th>
            <th>Dosen / semester</th>
            <th>Aksi</th>
        </tr>
    <?php while ($mk = $hasil->fetch_assoc()): ?>
        <tr>
            <td><?= e($mk['kode'] . ' - ' . $mk['nama']) ?></td>
            <td><?= e($mk['dosen']) ?> / Semester <?= (int) $mk['semester'] ?></td>
            <td>
                <div class="aksi">
                    <a class="tombol tombol-kecil" href="detail_mata_kuliah.php?id=<?= (int) $mk['id'] ?>">Detail</a>
                    <a class="tombol tombol-kecil" href="edit_mata_kuliah.php?id=<?= (int) $mk['id'] ?>">Edit</a>
                    <a class="tombol tombol-kecil tombol-bahaya" href="hapus_mata_kuliah.php?id=<?= (int) $mk['id'] ?>">Hapus</a>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
    </table>
<?php endif; ?>
<?php
$stmt->close();
tampil_footer();
