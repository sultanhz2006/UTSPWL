<section class="hero-panel mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <span class="badge rounded-pill text-bg-primary px-3 py-2 mb-3"><?= is_admin() ? 'Dashboard Admin' : 'Dashboard Peminjaman' ?></span>
            <h1 class="display-6 fw-bold mb-2">Inventaris Buku Perpustakaan</h1>
            <p class="text-secondary mb-0"><?= is_admin() ? 'Kelola koleksi buku, pantau stok, dan simpan sampul buku dalam tampilan yang rapi dan responsif.' : 'Lihat ketersediaan buku dan pinjam buku yang masih tersedia.' ?></p>
        </div>
        <?php if (is_admin()): ?>
        <div class="col-lg-4 text-lg-end">
            <a href="index.php?route=books.create" class="btn btn-info btn-lg btn-soft fw-semibold">
                <i class="bi bi-plus-circle me-2"></i>Tambah Buku
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card p-4 h-100">
            <div class="text-secondary text-uppercase small fw-semibold mb-2">Total Buku</div>
            <div class="display-6 fw-bold"><?= e((string) $totalBooks) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-4 h-100">
            <div class="text-secondary text-uppercase small fw-semibold mb-2">Total Stok</div>
            <div class="display-6 fw-bold"><?= e((string) $totalStock) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-4 h-100">
            <div class="text-secondary text-uppercase small fw-semibold mb-2">Status Tersedia</div>
            <div class="display-6 fw-bold"><?= e((string) $availableBooks) ?></div>
        </div>
    </div>
</section>

<section class="table-dark-shell p-3 p-lg-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
        <div>
            <h2 class="h4 mb-1">Daftar Buku</h2>
            <p class="text-secondary mb-0"><?= is_admin() ? 'Data judul, pengarang, tahun, stok, status, dan sampul buku.' : 'Cek stok buku yang tersedia lalu pinjam jika masih ada.' ?></p>
        </div>
    </div>

    <?php if (empty($books)): ?>
        <div class="empty-panel">
            Belum ada data buku. Klik tombol "Tambah Buku" untuk menambahkan data pertama.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark align-middle">
                <thead>
                    <tr class="text-secondary">
                        <th>Sampul</th>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Tahun Terbit</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr class="book-card">
                            <td>
                                <?php if (!empty($book['thumbpath'])): ?>
                                    <img src="<?= e($book['thumbpath']) ?>" alt="<?= e($book['judul']) ?>" class="book-cover">
                                <?php else: ?>
                                    <div class="book-cover d-flex align-items-center justify-content-center text-secondary">
                                        <i class="bi bi-book"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= e($book['judul']) ?></div>
                                <div class="text-secondary small">Ditambahkan <?= e(date('d M Y', strtotime($book['created_at']))) ?></div>
                            </td>
                            <td><?= e($book['pengarang']) ?></td>
                            <td><?= e((string) $book['tahun']) ?></td>
                            <td><?= e((string) $book['stok']) ?></td>
                            <td>
                                <?php $statusClass = $book['status'] === 'Tersedia' ? 'status-tersedia' : 'status-dipinjam'; ?>
                                <span class="status-badge <?= e($statusClass) ?>">
                                    <?= $book['status'] === 'Tersedia' ? '&#10004;' : '&#10006;' ?>
                                    <?= e($book['status']) ?>
                                </span>
                                <?php if (!empty($book['dipinjam_oleh'])): ?>
                                    <div class="text-secondary small mt-2">Terakhir dipinjam: <?= e($book['dipinjam_oleh']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <?php if (is_admin()): ?>
                                    <a href="index.php?route=books.edit&id=<?= e((string) $book['id']) ?>" class="btn btn-outline-info btn-sm btn-soft">
                                        Edit
                                    </a>
                                    <form method="POST" action="index.php?route=books.delete" onsubmit="return confirm('Yakin ingin menghapus?');" class="d-inline">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $book['id']) ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm btn-soft">Hapus</button>
                                    </form>
                                    <?php else: ?>
                                    <form method="POST" action="index.php?route=books.borrow" class="d-inline">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $book['id']) ?>">
                                        <button type="submit" class="btn btn-outline-success btn-sm btn-soft" <?= (int) $book['stok'] <= 0 ? 'disabled' : '' ?>>
                                            Pinjam
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (($totalPages ?? 1) > 1): ?>
            <nav class="mt-4" aria-label="Pagination buku">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?route=books.index&page=<?= max(1, $currentPage - 1) ?>">Sebelumnya</a>
                    </li>
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                        <li class="page-item <?= $page === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?route=books.index&page=<?= $page ?>"><?= $page ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?route=books.index&page=<?= min($totalPages, $currentPage + 1) ?>">Berikutnya</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>
