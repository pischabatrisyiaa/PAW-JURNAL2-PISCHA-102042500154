<?php
session_start();

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function token_cocok($kirim)
{
    return is_string($kirim) && $kirim === $_SESSION['token'];
}

function isi_token()
{
    echo '<input type="hidden" name="token" value="' . e($_SESSION['token']) . '">';
}

function ambil_id($nilai)
{
    if (!is_string($nilai) || !ctype_digit($nilai) || strlen($nilai) > 10) {
        return 0;
    }
    return (int) $nilai;
}

function bersihkan($nilai)
{
    if (!is_string($nilai)) {
        return '';
    }
    return trim(str_replace("\r\n", "\n", $nilai));
}

function panjang($teks)
{
    return mb_strlen($teks, 'UTF-8');
}

function ambil_cari()
{
    return bersihkan($_GET['cari'] ?? '');
}

function kata_like($cari)
{
    return '%' . addcslashes($cari, '%_\\') . '%';
}

function format_tanggal($tanggal)
{
    $waktu = strtotime($tanggal);
    return $waktu ? date('d/m/Y', $waktu) : $tanggal;
}

function alihkan($url)
{
    header('Location: ' . $url, true, 303);
    exit;
}

function set_pesan($jenis, $teks)
{
    $_SESSION['pesan'] = ['jenis' => $jenis, 'teks' => $teks];
}

function ambil_pesan()
{
    if (!isset($_SESSION['pesan'])) {
        return null;
    }
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']);
    return $pesan;
}

function tampil_header($judul)
{
    $pesan = ambil_pesan();
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?> - LMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="halaman">
    <nav class="menu">
        <a href="index.php">Beranda</a>
        <a href="daftar_mata_kuliah.php">Mata kuliah</a>
        <a href="daftar_materi.php">Materi</a>
        <a href="daftar_tugas.php">Tugas</a>
    </nav>
    <main class="kartu">
<?php if ($pesan !== null): ?>
        <div class="pesan pesan-<?= e($pesan['jenis']) ?>"><?= e($pesan['teks']) ?></div>
<?php endif; ?>
<?php
}

function tampil_footer()
{
    ?>
    </main>
</div>
</body>
</html>
<?php
}

function tampil_kesalahan($kode_http, $teks, $kembali)
{
    http_response_code($kode_http);
    tampil_header('Terjadi kesalahan');
    echo '<h1>Terjadi kesalahan</h1>';
    echo '<div class="pesan pesan-galat">' . e($teks) . '</div>';
    echo '<p><a class="tombol" href="' . e($kembali) . '">Kembali</a></p>';
    tampil_footer();
    exit;
}

function tampil_galat($galat, $kolom)
{
    if (isset($galat[$kolom])) {
        echo '<div class="galat">' . e($galat[$kolom]) . '</div>';
    }
}

function ambil_daftar_mata_kuliah($koneksi)
{
    $stmt = $koneksi->prepare('SELECT id, kode, nama FROM mata_kuliah ORDER BY nama');
    $stmt->execute();
    $hasil = $stmt->get_result();
    $daftar = [];
    while ($baris = $hasil->fetch_assoc()) {
        $daftar[] = $baris;
    }
    $stmt->close();
    return $daftar;
}

function mata_kuliah_ada($koneksi, $id)
{
    $stmt = $koneksi->prepare('SELECT id FROM mata_kuliah WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $ada = $stmt->get_result()->num_rows === 1;
    $stmt->close();
    return $ada;
}

function pilihan_mata_kuliah($daftar, $terpilih, $label_kosong)
{
    echo '<option value="">' . e($label_kosong) . '</option>';
    foreach ($daftar as $mk) {
        $pilih = ((string) $mk['id'] === (string) $terpilih) ? ' selected' : '';
        echo '<option value="' . (int) $mk['id'] . '"' . $pilih . '>'
            . e($mk['kode'] . ' - ' . $mk['nama']) . '</option>';
    }
}

function validasi_mata_kuliah($post)
{
    $d = [
        'kode'     => bersihkan($post['kode'] ?? ''),
        'nama'     => bersihkan($post['nama'] ?? ''),
        'dosen'    => bersihkan($post['dosen'] ?? ''),
        'semester' => bersihkan($post['semester'] ?? ''),
    ];
    $galat = [];

    if (!preg_match('/^[A-Z0-9-]{3,20}$/D', $d['kode'])) {
        $galat['kode'] = 'Kode 3-20 karakter: huruf kapital, angka, atau tanda -.';
    }
    if (panjang($d['nama']) < 3 || panjang($d['nama']) > 100) {
        $galat['nama'] = 'Nama harus 3-100 karakter.';
    }
    if (panjang($d['dosen']) < 3 || panjang($d['dosen']) > 100) {
        $galat['dosen'] = 'Dosen harus 3-100 karakter.';
    }
    $semester = filter_var(
        $d['semester'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 14]]
    );
    if ($semester === false) {
        $galat['semester'] = 'Semester harus bilangan bulat 1-14.';
    }
    return [$d, $galat];
}

function validasi_materi($post, $koneksi)
{
    $d = [
        'mata_kuliah_id' => bersihkan($post['mata_kuliah_id'] ?? ''),
        'judul'          => bersihkan($post['judul'] ?? ''),
        'isi'            => bersihkan($post['isi'] ?? ''),
        'tautan'         => bersihkan($post['tautan'] ?? ''),
    ];
    $galat = [];

    $id_mk = ambil_id($d['mata_kuliah_id']);
    if ($id_mk === 0 || !mata_kuliah_ada($koneksi, $id_mk)) {
        $galat['mata_kuliah_id'] = 'Pilih mata kuliah yang tersedia.';
    }
    if (panjang($d['judul']) < 3 || panjang($d['judul']) > 150) {
        $galat['judul'] = 'Judul harus 3-150 karakter.';
    }
    if (panjang($d['isi']) < 3 || panjang($d['isi']) > 2000) {
        $galat['isi'] = 'Isi harus 3-2000 karakter.';
    }
    if (panjang($d['tautan']) > 255 || !preg_match('#^https?://\S+$#i', $d['tautan'])) {
        $galat['tautan'] = 'Gunakan tautan http/https yang valid, maksimal 255 karakter.';
    }
    return [$d, $galat];
}

function validasi_tugas($post, $koneksi)
{
    $d = [
        'mata_kuliah_id' => bersihkan($post['mata_kuliah_id'] ?? ''),
        'judul'          => bersihkan($post['judul'] ?? ''),
        'instruksi'      => bersihkan($post['instruksi'] ?? ''),
        'tenggat'        => bersihkan($post['tenggat'] ?? ''),
        'status'         => bersihkan($post['status'] ?? ''),
    ];
    $galat = [];

    $id_mk = ambil_id($d['mata_kuliah_id']);
    if ($id_mk === 0 || !mata_kuliah_ada($koneksi, $id_mk)) {
        $galat['mata_kuliah_id'] = 'Pilih mata kuliah yang tersedia.';
    }
    if (panjang($d['judul']) < 3 || panjang($d['judul']) > 150) {
        $galat['judul'] = 'Judul harus 3-150 karakter.';
    }
    if (panjang($d['instruksi']) < 3 || panjang($d['instruksi']) > 2000) {
        $galat['instruksi'] = 'Instruksi harus 3-2000 karakter.';
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $d['tenggat'])) {
        $galat['tenggat'] = 'Tanggal tenggat tidak valid.';
    } else {
        $bagian = explode('-', $d['tenggat']);
        if ((int) $bagian[0] < 1000 || !checkdate((int) $bagian[1], (int) $bagian[2], (int) $bagian[0])) {
            $galat['tenggat'] = 'Tanggal tenggat tidak valid.';
        }
    }
    if (!in_array($d['status'], ['Draf', 'Aktif', 'Selesai'], true)) {
        $galat['status'] = 'Status harus Draf, Aktif, atau Selesai.';
    }
    return [$d, $galat];
}

function kode_dipakai($koneksi, $kode, $id_abaikan)
{
    $stmt = $koneksi->prepare('SELECT id FROM mata_kuliah WHERE kode = ? AND id <> ?');
    $stmt->bind_param('si', $kode, $id_abaikan);
    $stmt->execute();
    $dipakai = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $dipakai;
}
