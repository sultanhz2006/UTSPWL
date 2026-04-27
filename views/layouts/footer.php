    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('gambar');
            const preview = document.getElementById('preview-cover');
            const emptyCoverBox = document.getElementById('empty-cover-box');

            if (fileInput && preview) {
                fileInput.addEventListener('change', function () {
                    const file = this.files[0];

                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (event) {
                        preview.src = event.target.result;
                        preview.classList.remove('d-none');
                        if (emptyCoverBox) {
                            emptyCoverBox.classList.add('d-none');
                        }
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    </script>
</body>
</html>
