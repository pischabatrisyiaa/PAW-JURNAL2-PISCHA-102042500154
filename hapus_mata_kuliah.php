<?php
require 'koneksi.php';
require 'fungsi.php';

$metode_post = ($_SERVER['REQUEST_METHOD'] === 'POST');

if ($metode_post && !token_cocok($_POST['token'] ?? '')) {
    tampil_kesalahan(403, 'Token tidak valid. Data tidak dihapus.', 'daftar_mata_kuliah.php');
}

$id = ambil_id($metode_post ? ($_POST['id'] ?? '') : ($_GET['id'] ?? ''));
if ($id === 0) {
    tampil_kesalahan(400, 'ID tidak valid.', 'daftar_mata_kuliah.php');
}

$stmt = $koneksi->prepare('SELECT id, kode, nama FROM mata_kuliah WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) {
    tampil_kesalahan(404, 'Data tidak ditemukan.', 'daftar_mata_kuliah.php');
}

$galat = '';

if ($metode_post) {
    $stmt = $koneksi->prepare(
        'SELECT (SELECT COUNT(*) FROM materi WHERE mata_kuliah_id = ?)
              + (SELECT COUNT(*) FROM tugas WHERE mata_kuliah_id = ?) AS jumlah'
    );
    $stmt->bind_param('ii', $id, $id);
    $stmt->execute();
    $dipakai = (int) $stmt->get_result()->fetch_assoc()['jumlah'];
    $stmt->close();

    if ($dipakai > 0) {
        $galat = 'Data belum tersimpan. Mata kuliah masih memiliki materi atau tugas. '
               . 'Hapus data terkait terlebih dahulu.';
    } else {
        $stmt = $koneksi->prepare('DELETE FROM mata_kuliah WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        set_pesan('sukses', 'Mata kuliah berhasil dihapus.');
        alihkan('daftar_mata_kuliah.php');
    }
}

tampil_header('Hapus mata kuliah');
?>
<h1>Mata kuliah</h1>
<?php if ($galat !== ''): ?>
    <div class="pesan pesan-galat"><?= e($galat) ?></div>
<?php endif; ?>
<p>Hapus <strong><?= e($data['nama']) ?></strong>?</p>
<p class="ket">Data baru dihapus setelah tombol konfirmasi dikirim.</p>
<form method="post" action="hapus_mata_kuliah.php">
    <?php isi_token(); ?>
    <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">
    <div class="aksi">
        <button class="tombol tombol-bahaya" type="submit">Ya, hapus data</button>
        <a class="tombol" href="daftar_mata_kuliah.php">Batal</a>
    </div>
</form>
<?php tampil_footer(); ?>
