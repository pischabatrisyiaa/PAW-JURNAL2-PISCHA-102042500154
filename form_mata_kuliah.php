<form method="post" action="<?= e($aksi) ?>">
    <?php isi_token(); ?>
    <div class="grid">
        <div>
            <label for="kode">Kode mata kuliah</label>
            <input type="text" id="kode" name="kode" value="<?= e($data['kode']) ?>">
            <?php tampil_galat($galat, 'kode'); ?>
        </div>
        <div>
            <label for="nama">Nama mata kuliah</label>
            <input type="text" id="nama" name="nama" value="<?= e($data['nama']) ?>">
            <?php tampil_galat($galat, 'nama'); ?>
        </div>
        <div>
            <label for="dosen">Dosen pengampu</label>
            <input type="text" id="dosen" name="dosen" value="<?= e($data['dosen']) ?>">
            <?php tampil_galat($galat, 'dosen'); ?>
        </div>
        <div>
            <label for="semester">Semester</label>
            <input type="text" id="semester" name="semester" inputmode="numeric" value="<?= e($data['semester']) ?>">
            <?php tampil_galat($galat, 'semester'); ?>
        </div>
    </div>
    <div class="aksi">
        <button class="tombol tombol-utama" type="submit">Simpan data</button>
        <a class="tombol" href="<?= e($batal) ?>">Batal</a>
    </div>
</form>
