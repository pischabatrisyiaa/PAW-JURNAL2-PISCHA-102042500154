<?php
require 'koneksi.php';
require 'fungsi.php';

$metode_post = ($_SERVER['REQUEST_METHOD'] === 'POST');

if ($metode_post && !token_cocok($_POST['token'] ?? '')) {
    tampil_kesalahan(403, 'Token tidak valid. Data tidak dihapus.', 'daftar_tugas.php');
}

$id = ambil_id($metode_post ? ($_POST['id'] ?? '') : ($_GET['id'] ?? ''));
if ($id === 0) {
    tampil_kesalahan(400, 'ID tidak valid.', 'daftar_tugas.php');
}

$stmt = $koneksi->prepare('SELECT id, judul FROM tugas WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) {
    tampil_kesalahan(404, 'Data tidak ditemukan.', 'daftar_tugas.php');
}

if ($metode_post) {
    $stmt = $koneksi->prepare('DELETE FROM tugas WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    set_pesan('sukses', 'Tugas berhasil dihapus.');
    alihkan('daftar_tugas.php');
}

tampil_header('Hapus tugas');
?>
<h1>Tugas</h1>
<p>Hapus <strong><?= e($data['judul']) ?></strong>?</p>
<p class="ket">Data baru dihapus setelah tombol konfirmasi dikirim.</p>
<form method="post" action="hapus_tugas.php">
    <?php isi_token(); ?>
    <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">
    <div class="aksi">
        <button class="tombol tombol-bahaya" type="submit">Ya, hapus data</button>
        <a class="tombol" href="daftar_tugas.php">Batal</a>
    </div>
</form>
<?php tampil_footer(); ?>