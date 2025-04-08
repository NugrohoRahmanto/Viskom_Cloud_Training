<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klasifikasi Gambar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .upload-box {
            border: 2px dashed #007bff;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        .btn-custom {
            width: 150px;
        }
        #preview-container {
            display: none;
            margin-top: 15px;
            text-align: center;
        }
        #preview {
            max-width: 100%;
            max-height: 250px;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="card p-4 shadow-lg">
            <h2 class="text-center">Klasifikasi Gambar</h2>
            
            <form action="<?php echo e(url('/classify')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <label for="image" class="upload-box d-block mt-3">
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

            <?php if(isset($result)): ?>
                <div class="alert alert-info mt-3">
                    <h4>Hasil Prediksi:</h4>
                    <p><strong>Kelas:</strong> <?php echo e($result['predicted_class']); ?></p>
                    <p><strong>Confidence:</strong> <?php echo e(number_format($result['confidence'] * 100, 2)); ?>%</p>
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

        document.querySelector('.upload-box').addEventListener('click', function () {
            document.getElementById('image').click();
        });
    </script>
</body>
</html>
<?php /**PATH D:\KULIAHH\S2 SMT2\TREN VISKOM\tugas_deploy\front_end_cloud\resources\views/index.blade.php ENDPATH**/ ?>