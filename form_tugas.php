<?php
$pilihan_status = ['Draf', 'Aktif', 'Selesai'];
?>
<form method="post" action="<?= e($aksi) ?>">
    <?php isi_token(); ?>
    <div class="grid">
        <div>
            <label for="mata_kuliah_id">Mata kuliah</label>
            <select id="mata_kuliah_id" name="mata_kuliah_id">
                <?php pilihan_mata_kuliah($daftar_mk, $data['mata_kuliah_id'], 'Pilih Mata kuliah'); ?>
            </select>
            <?php tampil_galat($galat, 'mata_kuliah_id'); ?>
        </div>
        <div>
            <label for="judul">Judul tugas</label>
            <input type="text" id="judul" name="judul" value="<?= e($data['judul']) ?>">
            <?php tampil_galat($galat, 'judul'); ?>
        </div>
        <div class="penuh">
            <label for="instruksi">Instruksi tugas</label>
            <textarea id="instruksi" name="instruksi"><?= e($data['instruksi']) ?></textarea>
            <?php tampil_galat($galat, 'instruksi'); ?>
        </div>
        <div>
            <label for="tenggat">Tenggat</label>
            <input type="date" id="tenggat" name="tenggat" value="<?= e($data['tenggat']) ?>">
            <?php tampil_galat($galat, 'tenggat'); ?>
        </div>
        <div>
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach ($pilihan_status as $status): ?>
                    <option value="<?= e($status) ?>"<?= $data['status'] === $status ? ' selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
            <?php tampil_galat($galat, 'status'); ?>
        </div>
    </div>
    <div class="aksi">
        <button class="tombol tombol-utama" type="submit">Simpan data</button>
        <a class="tombol" href="<?= e($batal) ?>">Batal</a>
    </div>
</form>
