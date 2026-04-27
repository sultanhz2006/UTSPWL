<?php

class BookController
{
    private Book $bookModel;

    public function __construct(Book $bookModel)
    {
        $this->bookModel = $bookModel;
    }

    public function index(): void
    {
        require_login();

        $perPage = 10;
        $currentPage = max(1, (int) ($_GET['page'] ?? 1));
        $totalBooks = $this->bookModel->totalBooks();
        $totalPages = max(1, (int) ceil($totalBooks / $perPage));
        $currentPage = min($currentPage, $totalPages);
        $offset = ($currentPage - 1) * $perPage;

        render('books/index', [
            'title' => 'Inventaris Perpustakaan',
            'books' => $this->bookModel->paginate($perPage, $offset),
            'totalBooks' => $totalBooks,
            'totalStock' => $this->bookModel->totalStock(),
            'availableBooks' => $this->bookModel->availableBooks(),
            'bodyClass' => 'dashboard-page',
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }

    public function create(): void
    {
        require_admin();

        render('books/form', [
            'title' => 'Tambah Buku',
            'formTitle' => 'Tambah Buku',
            'buttonLabel' => 'Simpan',
            'action' => 'books.store',
            'book' => [
                'judul' => old('judul'),
                'pengarang' => old('pengarang'),
                'tahun' => old('tahun'),
                'stok' => old('stok', '1'),
                'status' => old('status', 'Tersedia'),
                'gambar' => null,
                'thumbpath' => null,
                'dipinjam_oleh' => null,
                'dipinjam_pada' => null,
            ],
        ]);
    }

    public function store(): void
    {
        require_admin();
        verify_csrf();

        $data = $this->validateBookInput($_POST);
        store_old_input($_POST);

        try {
            $imageData = upload_image($_FILES['gambar'] ?? []);
            $payload = array_merge($data, $imageData);
            $this->bookModel->create($payload);
            clear_old_input();
            set_flash('success', 'Data berhasil ditambahkan!');
            redirect('books.index');
        } catch (Throwable $exception) {
            set_flash('error', $exception->getMessage());
            redirect('books.create');
        }
    }

    public function edit(): void
    {
        require_admin();

        $id = (int) ($_GET['id'] ?? 0);
        $book = $this->bookModel->find($id);

        if (!$book) {
            set_flash('error', 'Data buku tidak ditemukan.');
            redirect('books.index');
        }

        render('books/form', [
            'title' => 'Edit Buku',
            'formTitle' => 'Edit Buku',
            'buttonLabel' => 'Simpan',
            'action' => 'books.update',
            'book' => [
                'id' => $book['id'],
                'judul' => old('judul', $book['judul']),
                'pengarang' => old('pengarang', $book['pengarang']),
                'tahun' => old('tahun', (string) $book['tahun']),
                'stok' => old('stok', (string) $book['stok']),
                'status' => old('status', $book['status']),
                'gambar' => $book['gambar'],
                'thumbpath' => $book['thumbpath'],
                'dipinjam_oleh' => $book['dipinjam_oleh'],
                'dipinjam_pada' => $book['dipinjam_pada'],
            ],
        ]);
    }

    public function update(): void
    {
        require_admin();
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $book = $this->bookModel->find($id);

        if (!$book) {
            set_flash('error', 'Data buku tidak ditemukan.');
            redirect('books.index');
        }

        $data = $this->validateBookInput($_POST);
        store_old_input($_POST);

        try {
            $imageData = [
                'gambar' => $book['gambar'],
                'thumbpath' => $book['thumbpath'],
                'dipinjam_oleh' => $book['dipinjam_oleh'],
                'dipinjam_pada' => $book['dipinjam_pada'],
            ];

            if (isset($_FILES['gambar']) && ($_FILES['gambar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $imageData = upload_image($_FILES['gambar']);
                delete_image_files($book['gambar'], $book['thumbpath']);
            }

            $payload = array_merge($data, $imageData);
            $this->bookModel->update($id, $payload);
            clear_old_input();
            set_flash('success', 'Data buku berhasil diperbarui!');
            redirect('books.index');
        } catch (Throwable $exception) {
            set_flash('error', $exception->getMessage());
            redirect('books.edit&id=' . $id);
        }
    }

    public function destroy(): void
    {
        require_admin();
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        $book = $this->bookModel->find($id);

        if (!$book) {
            set_flash('error', 'Data buku tidak ditemukan.');
            redirect('books.index');
        }

        delete_image_files($book['gambar'], $book['thumbpath']);
        $this->bookModel->delete($id);
        set_flash('success', 'Data buku berhasil dihapus.');
        redirect('books.index');
    }

    public function borrow(): void
    {
        require_login();
        verify_csrf();

        if (is_admin()) {
            set_flash('error', 'Admin tidak perlu memakai fitur pinjam.');
            redirect('books.index');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $book = $this->bookModel->find($id);

        if (!$book) {
            set_flash('error', 'Data buku tidak ditemukan.');
            redirect('books.index');
        }

        if ((int) $book['stok'] <= 0) {
            set_flash('error', 'Buku ini sedang tidak tersedia untuk dipinjam.');
            redirect('books.index');
        }

        if ($this->bookModel->borrow($id, $_SESSION['user']['username'])) {
            set_flash('success', 'Buku berhasil dipinjam.');
        } else {
            set_flash('error', 'Buku gagal dipinjam. Coba ulangi lagi.');
        }

        redirect('books.index');
    }

    private function validateBookInput(array $input): array
    {
        $judul = trim($input['judul'] ?? '');
        $pengarang = trim($input['pengarang'] ?? '');
        $tahun = (int) ($input['tahun'] ?? 0);
        $stok = (int) ($input['stok'] ?? 0);
        $status = trim($input['status'] ?? '');

        if ($judul === '' || $pengarang === '') {
            throw new InvalidArgumentException('Judul Buku dan Pengarang wajib diisi.');
        }

        $currentYear = (int) date('Y') + 1;
        if ($tahun < 1900 || $tahun > $currentYear) {
            throw new InvalidArgumentException('Tahun Terbit tidak valid.');
        }

        if ($stok < 0) {
            throw new InvalidArgumentException('Stok tidak boleh kurang dari 0.');
        }

        $allowedStatus = ['Tersedia', 'Dipinjam'];
        if (!in_array($status, $allowedStatus, true)) {
            throw new InvalidArgumentException('Status buku tidak valid.');
        }

        return [
            'judul' => $judul,
            'pengarang' => $pengarang,
            'tahun' => $tahun,
            'stok' => $stok,
            'status' => $status,
            'dipinjam_oleh' => $status === 'Tersedia' ? null : ($input['dipinjam_oleh'] ?? null),
            'dipinjam_pada' => $status === 'Tersedia' ? null : ($input['dipinjam_pada'] ?? null),
        ];
    }
}
