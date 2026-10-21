<?php
require 'koneksi.php';
require 'fungsi.php';

$cari = ambil_cari();
$kata = kata_like($cari);
$filter = ambil_id($_GET['mata_kuliah_id'] ?? '');
$daftar_mk = ambil_daftar_mata_kuliah($koneksi);

$stmt = $koneksi->prepare(
    'SELECT m.id, m.judul, mk.kode, mk.nama
     FROM materi m
     JOIN mata_kuliah mk ON mk.id = m.mata_kuliah_id
     WHERE m.judul LIKE ? AND (? = 0 OR m.mata_kuliah_id = ?)
     ORDER BY m.id DESC'
);
$stmt->bind_param('sii', $kata, $filter, $filter);
$stmt->execute();
$hasil = $stmt->get_result();
$jumlah = $hasil->num_rows;

tampil_header('Materi');
?>
<h1>Materi</h1>
<div class="baris-atas">
    <span class="ket">Menampilkan <?= $jumlah ?> data.</span>
    <a class="tombol tombol-utama" href="tambah_materi.php">Tambah materi</a>
</div>

<form class="cari" method="get" action="daftar_materi.php">
    <input type="text" name="cari" value="<?= e($cari) ?>" placeholder="Cari judul materi">
    <select name="mata_kuliah_id">
        <?php pilihan_mata_kuliah($daftar_mk, $filter, 'Semua mata kuliah'); ?>
    </select>
    <button class="tombol tombol-utama" type="submit">Cari</button>
    <a class="tombol" href="daftar_materi.php">Reset</a>
</form>

<?php if ($jumlah === 0): ?>
    <div class="kosong">Data tidak ditemukan.</div>
<?php else: ?>
    <table class="tabel">
        <tr>
            <th>Judul</th>
            <th>Mata kuliah</th>
            <th>Aksi</th>
        </tr>
    <?php while ($m = $hasil->fetch_assoc()): ?>
        <tr>
            <td><?= e($m['judul']) ?></td>
            <td><?= e($m['kode'] . ' - ' . $m['nama']) ?></td>
            <td>
                <div class="aksi">
                    <a class="tombol tombol-kecil" href="detail_materi.php?id=<?= (int) $m['id'] ?>">Detail</a>
                    <a class="tombol tombol-kecil" href="edit_materi.php?id=<?= (int) $m['id'] ?>">Edit</a>
                    <a class="tombol tombol-kecil tombol-bahaya" href="hapus_materi.php?id=<?= (int) $m['id'] ?>">Hapus</a>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
    </table>
<?php endif; ?>
<?php
$stmt->close();
tampil_footer();
