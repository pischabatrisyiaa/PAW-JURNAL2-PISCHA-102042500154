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
            <label for="judul">Judul materi</label>
            <input type="text" id="judul" name="judul" value="<?= e($data['judul']) ?>">
            <?php tampil_galat($galat, 'judul'); ?>
        </div>
        <div class="penuh">
            <label for="isi">Isi materi</label>
            <textarea id="isi" name="isi"><?= e($data['isi']) ?></textarea>
            <?php tampil_galat($galat, 'isi'); ?>
        </div>
        <div class="penuh">
            <label for="tautan">Tautan referensi</label>
            <input type="text" id="tautan" name="tautan" value="<?= e($data['tautan']) ?>">
            <?php tampil_galat($galat, 'tautan'); ?>
        </div>
    </div>
    <div class="aksi">
        <button class="tombol tombol-utama" type="submit">Simpan data</button>
        <a class="tombol" href="<?= e($batal) ?>">Batal</a>
    </div>
</form>
