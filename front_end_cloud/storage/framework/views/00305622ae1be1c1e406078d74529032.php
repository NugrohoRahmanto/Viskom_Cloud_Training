<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klasifikasi Gambar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>">
</head>
<body>
    <div class="upload-container">
        <div class="card p-4 shadow-lg">
            <h2 class="text-center">Klasifikasi Gambar</h2>
            
            <form action="<?php echo e(url('/classify')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <label for="image" class="upload-box d-block mt-3" id="drop-area">
                    <input type="file" name="image" id="image" class="d-none" accept="image/*" required onchange="previewImage(event)">
                    <p>📤 Drag & drop gambar di sini atau klik untuk memilih</p>
                </label>

                <!-- Preview Gambar -->
                <div id="preview-container">
                    <p>Preview Gambar:</p>
                    <img id="preview" src="#" alt="Preview Gambar">
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-danger btn-custom" onclick="resetForm()">Reset</button>
                    <button type="submit" class="btn btn-primary btn-custom">Klasifikasi</button>
                </div>
            </form>

            <?php if(isset($class_name)): ?>
                <div class="alert alert-info mt-3 result-container text-center">
                    <h4>Hasil Prediksi:</h4>
                    
                    <!-- Menampilkan gambar hasil upload -->
                    <img src="<?php echo e(asset('storage/' . $imagePath)); ?>" alt="Hasil Upload" class="img-fluid rounded shadow-sm" style="max-width: 300px; margin-top: 10px;">
                    
                    <p id="kelas"><strong>Tipe penyakit:</strong> <?php echo e($class_name); ?></p>
                    <p><strong>Confidence:</strong> <?php echo e(number_format($confidence * 100, 2)); ?>%</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            const reader = new FileReader();
            reader.onload = function () {
                const imgElement = document.getElementById('preview');
                imgElement.src = reader.result;
                document.getElementById('preview-container').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }

        function resetForm() {
            document.getElementById('image').value = "";
            document.getElementById('preview-container').style.display = "none";
        }

        // Drag & Drop Functionality
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('image');

        dropArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropArea.style.border = '2px solid #0056b3';
            dropArea.style.backgroundColor = '#e9ecef';
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.style.border = '2px dashed #007bff';
            dropArea.style.backgroundColor = '#ffffff';
        });

        dropArea.addEventListener('drop', (event) => {
            event.preventDefault();
            dropArea.style.border = '2px dashed #007bff';
            dropArea.style.backgroundColor = '#ffffff';

            if (event.dataTransfer.files.length > 0) {
                fileInput.files = event.dataTransfer.files;
                previewImage({ target: fileInput });
            }
        });
    </script>
</body>
</html>
<?php /**PATH D:\KULIAHH\S2 SMT2\TREN VISKOM\tugas_deploy\front_end_cloud\resources\views/upload.blade.php ENDPATH**/ ?>