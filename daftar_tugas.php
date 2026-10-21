<?php
require 'koneksi.php';
require 'fungsi.php';

$cari = ambil_cari();
$kata = kata_like($cari);
$filter = ambil_id($_GET['mata_kuliah_id'] ?? '');
$daftar_mk = ambil_daftar_mata_kuliah($koneksi);

$stmt = $koneksi->prepare(
    'SELECT t.id, t.judul, t.tenggat, t.status, mk.kode, mk.nama
     FROM tugas t
     JOIN mata_kuliah mk ON mk.id = t.mata_kuliah_id
     WHERE t.judul LIKE ? AND (? = 0 OR t.mata_kuliah_id = ?)
     ORDER BY t.id DESC'
);
$stmt->bind_param('sii', $kata, $filter, $filter);
$stmt->execute();
$hasil = $stmt->get_result();
$jumlah = $hasil->num_rows;

tampil_header('Tugas');
?>
<h1>Tugas</h1>
<div class="baris-atas">
    <span class="ket">Menampilkan <?= $jumlah ?> data.</span>
    <a class="tombol tombol-utama" href="tambah_tugas.php">Tambah tugas</a>
</div>

<form class="cari" method="get" action="daftar_tugas.php">
    <input type="text" name="cari" value="<?= e($cari) ?>" placeholder="Cari judul tugas">
    <select name="mata_kuliah_id">
        <?php pilihan_mata_kuliah($daftar_mk, $filter, 'Semua mata kuliah'); ?>
    </select>
    <button class="tombol tombol-utama" type="submit">Cari</button>
    <a class="tombol" href="daftar_tugas.php">Reset</a>
</form>

<?php if ($jumlah === 0): ?>
    <div class="kosong">Data tidak ditemukan.</div>
<?php else: ?>
    <table class="tabel">
        <tr>
            <th>Judul</th>
            <th>Mata kuliah</th>
            <th>Tenggat</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    <?php while ($t = $hasil->fetch_assoc()): ?>
        <tr>
            <td><?= e($t['judul']) ?></td>
            <td><?= e($t['kode'] . ' - ' . $t['nama']) ?></td>
            <td><?= e(format_tanggal($t['tenggat'])) ?></td>
            <td><span class="status"><?= e($t['status']) ?></span></td>
            <td>
                <div class="aksi">
                    <a class="tombol tombol-kecil" href="detail_tugas.php?id=<?= (int) $t['id'] ?>">Detail</a>
                    <a class="tombol tombol-kecil" href="edit_tugas.php?id=<?= (int) $t['id'] ?>">Edit</a>
                    <a class="tombol tombol-kecil tombol-bahaya" href="hapus_tugas.php?id=<?= (int) $t['id'] ?>">Hapus</a>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
    </table>
<?php endif; ?>
<?php
$stmt->close();
tampil_footer();
