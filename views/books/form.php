<?php
$isEdit = isset($book['id']);
?>
<section class="row justify-content-center">
    <div class="col-xl-9">
        <div class="form-shell p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                <div>
                    <span class="badge rounded-pill text-bg-secondary px-3 py-2 mb-3"><?= e($formTitle) ?></span>
                    <h1 class="h2 fw-bold mb-2"><?= e($formTitle) ?></h1>
                    <p class="text-secondary mb-0">Lengkapi data inventaris buku dan unggah sampul jika tersedia.</p>
                </div>
                <a href="index.php?route=books.index" class="btn btn-outline-light btn-soft">Kembali</a>
            </div>

            <form method="POST" action="index.php?route=<?= e($action) ?>" enctype="multipart/form-data" class="row g-4">
                <?= csrf_input() ?>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= e((string) $book['id']) ?>">
                <?php endif; ?>

                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="judul" class="form-label">Judul Buku</label>
                            <input type="text" class="form-control form-control-lg" id="judul" name="judul" value="<?= e($book['judul']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pengarang" class="form-label">Pengarang</label>
                            <input type="text" class="form-control form-control-lg" id="pengarang" name="pengarang" value="<?= e($book['pengarang']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="tahun" class="form-label">Tahun Terbit</label>
                            <input type="number" class="form-control form-control-lg" id="tahun" name="tahun" min="1900" max="<?= e((string) ((int) date('Y') + 1)) ?>" value="<?= e((string) $book['tahun']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" class="form-control form-control-lg" id="stok" name="stok" min="0" value="<?= e((string) $book['stok']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select form-select-lg" id="status" name="status" required>
                                <option value="Tersedia" <?= selected((string) $book['status'], 'Tersedia') ?>>Tersedia</option>
                                <option value="Dipinjam" <?= selected((string) $book['status'], 'Dipinjam') ?>>Dipinjam</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="gambar" class="form-label">Gambar Sampul</label>
                            <input type="file" class="form-control form-control-lg" id="gambar" name="gambar" accept=".jpg,.jpeg,.png">
                            <div class="form-text text-secondary">Maksimal 2MB. Sistem otomatis membuat gambar utama dan thumbnail.</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="upload-zone h-100 d-flex flex-column gap-3 justify-content-center align-items-center text-center">
                        <div class="text-secondary small text-uppercase fw-semibold">Preview Sampul</div>
                        <?php if (!empty($book['thumbpath'])): ?>
                            <img src="<?= e($book['thumbpath']) ?>" alt="Thumbnail sampul" id="preview-cover" class="thumb-preview thumb-preview-lg">
                        <?php else: ?>
                            <img src="" alt="Preview sampul" id="preview-cover" class="thumb-preview thumb-preview-lg d-none">
                            <div class="thumb-preview thumb-preview-lg d-flex align-items-center justify-content-center text-secondary" id="empty-cover-box">
                                <i class="bi bi-image fs-2"></i>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($book['gambar'])): ?>
                            <a href="<?= e($book['gambar']) ?>" target="_blank" class="btn btn-outline-info btn-soft">Lihat Gambar Asli</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 d-flex flex-column flex-md-row gap-2 pt-2">
                    <button type="submit" class="btn btn-info btn-lg btn-soft fw-semibold"><?= e($buttonLabel) ?></button>
                    <a href="index.php?route=books.index" class="btn btn-outline-light btn-lg btn-soft">Batal</a>
                </div>
            </form>
        </div>
    </div>
</section>
