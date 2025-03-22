# Klasifikasi Penyakit Tanaman Anggur Menggunakan MobileNet V3 Small

**CAK6HJB3 TREN PADA VISI KOMPUTER S2IF-48-01 [COK]**

## Anggota Kelompok

- Ahmad Taufiq Nur Rohman
- Nugroho Rahmanto

## Latar Belakang

Dalam era modern, layanan **cloud computing** telah menjadi salah satu solusi utama dalam pengembangan dan pelatihan model kecerdasan buatan. Dengan adanya layanan cloud, proses pelatihan deep learning dapat dilakukan secara lebih efisien tanpa terbatas oleh keterbatasan perangkat keras lokal.

Tugas ini bertujuan untuk memberikan pengalaman langsung dalam memanfaatkan layanan **Google Colab** dengan **GPU T4** untuk melatih model deep learning. Kami menggunakan **MobileNet V3 Small**, yang merupakan model ringan dan efisien, dengan studi kasus klasifikasi gambar penyakit pada daun tanaman anggur. Pemilihan studi kasus ini dilakukan sebagai contoh penerapan model deep learning untuk tugas klasifikasi yang relatif sederhana, sehingga mahasiswa dapat memahami proses pelatihan dan implementasi model dalam skala kecil.

Selain melatih model, kami juga mengembangkan antarmuka berbasis web agar hasil model dapat diakses dengan lebih mudah. Frontend dikembangkan menggunakan **Laravel**, sementara backend menggunakan **Python dengan Flask**. Proyek ini dihosting di **VPS dengan layanan Domainesia**, memberikan pengalaman langsung dalam mengintegrasikan deep learning dengan deployment berbasis cloud.

## Langkah-Langkah dalam Tugas Ini

1. **Persiapan Data** – Melakukan pre-processing dataset agar siap digunakan dalam pelatihan model.
2. **Pemilihan Model** – Menggunakan **MobileNet V3 Small** karena efisien untuk tugas klasifikasi gambar.
3. **Training Model** – Melatih model menggunakan dataset yang tersedia dengan berbagai teknik optimasi.
4. **Evaluasi Model** – Menganalisis performa model menggunakan metrik evaluasi seperti **akurasi dan loss**.
5. **Pembuatan UI Website** – Mengembangkan antarmuka berbasis web untuk menguji dan mengakses model secara langsung.

Melalui tugas ini, mahasiswa dapat memahami seluruh proses **pengembangan model deep learning berbasis cloud**, mulai dari **persiapan data, pelatihan model, evaluasi, hingga deployment**. Pengalaman ini diharapkan dapat menjadi dasar bagi proyek-proyek deep learning selanjutnya yang lebih kompleks.

## 1. Persiapan Data dan Preprocessing

### 1.1 Sumber Dataset

Dataset yang digunakan dalam tugas ini berasal dari dataset **PlantVillage**, yang berisi berbagai gambar daun tanaman dengan berbagai kondisi kesehatan. Dataset ini tersedia dalam Google Drive dalam bentuk folder **Train** dan **Test**, yang masing-masing berisi gambar untuk pelatihan dan pengujian model. Dataset dapat diakses melalui tautan berikut: **[Dataset Google Drive](https://drive.google.com/file/d/1BvVDMzJZQsK8ORrMwl9pob1e5yUKzOwY/view?usp=drive_link)**.

![Sampel dataset](/image/Sampel-dataset.png)

Setelah diekstrak, dataset terdiri dari:

| Kategori            | Background | Grape Black Rot | Grape Esca | Grape Healthy | Grape Leaf Blight | Total |
| ------------------- | ---------- | --------------- | ---------- | ------------- | ----------------- | ----- |
| **Train Set** | 914        | 944             | 1,106      | 338           | 860               | 4,162 |
| **Test Set**  | 229        | 236             | 277        | 85            | 216               | 1,043 |

### 1.2 Preprocessing Data

Agar dataset siap digunakan dalam pelatihan model deep learning, dilakukan beberapa langkah preprocessing sebagai berikut:

1. **Split Dataset**

   - Dataset **Train** dibagi menjadi dua bagian: **Training Set (80%)** dan **Validation Set (20%)** menggunakan metode `train_test_split`.
   - Test set tetap dipertahankan secara terpisah untuk evaluasi akhir model.
2. **Oversampling pada Data Train**

   - Karena dataset memiliki distribusi kelas yang tidak seimbang, diterapkan teknik **oversampling** pada dataset **Training** agar jumlah sampel tiap kelas lebih seimbang.
   - Oversampling dilakukan dengan metode **resampling** gambar dari kelas minoritas hingga jumlahnya menyesuaikan kelas mayoritas.
3. **Normalisasi dan Augmentasi Data**

   - Setiap gambar dinormalisasi menggunakan fungsi `preprocess_input()` agar sesuai dengan format input MobileNet V3.
   - Augmentasi gambar diterapkan untuk meningkatkan variasi data dengan teknik berikut:

     - **Rotasi** (rotation_range=5°)
     - **Zoom** (zoom_range=0.1)
     - **Flip Horizontal**
4. **Pembuatan Data Generator**

   - Menggunakan **ImageDataGenerator** untuk membuat batch data secara otomatis selama pelatihan model.
   - Tiga generator data dibuat, yaitu:

     - **train_gen** untuk data latih
     - **valid_gen** untuk data validasi
     - **test_gen** untuk data uji

### 1.3 Distribusi Dataset Akhir

Setelah preprocessing, berikut adalah distribusi dataset:

| Kelas             | Training       | Validation    | Testing        |
| ----------------- | -------------- | ------------- | -------------- |
| Grape Leaf Blight | 872            | 192           | 216            |
| Grape Esca        | 872            | 219           | 277            |
| Background        | 872            | 198           | 229            |
| Grape Healthy     | 872            | 58            | 85             |
| Grape Black Rot   | 872            | 166           | 236            |
| **Total**   | **4360** | **833** | **1043** |

Dengan langkah-langkah ini, dataset telah siap untuk digunakan dalam pelatihan model deep learning berbasis cloud.

## 2. Pemilihan Model

### 2.1 Arsitektur Model

Pada tahap ini, digunakan arsitektur **MobileNetV3-Small** sebagai _base model_ untuk klasifikasi gambar daun anggur. MobileNetV3 merupakan model _deep learning_ yang ringan dan dioptimalkan untuk perangkat dengan keterbatasan daya komputasi. Model ini telah dilatih sebelumnya dengan dataset **ImageNet**, sehingga dapat dimanfaatkan sebagai fitur ekstraktor dalam proses transfer learning.

![Arsitektur MobileNet v4](/image/MobileNetV3-architecture.png)

#### **Struktur Model yang Digunakan**

- **MobileNetV3-Small** digunakan sebagai _feature extractor_.

```py
# Create Model Structure
input_tensor = Input(shape=(224, 224, 3))
class_count = len(list(train_gen.class_indices.keys()))

base_model = MobileNetV3Small(
    weights='imagenet',
    include_top=False,
    pooling=None,
    input_tensor=input_tensor
)

x = base_model.output

x = Dense(1028, activation='relu')(x)
x = AveragePooling2D(pool_size=(2, 2), strides=(2, 2))(x)
x = Dense(514, activation='relu')(x)
x = AveragePooling2D(pool_size=(2, 2), strides=(2, 2))(x)
x = Flatten()(x)

x = Dense(class_count, activation='softmax')(x)

model = Model(inputs=base_model.input, outputs=x)
```

- Lapisan tambahan ditambahkan setelah base model:
  - **Dense(1028, activation='relu')** → Lapisan _fully connected_ pertama untuk menangkap pola kompleks.
  - **AveragePooling2D(pool_size=(2,2))** → Mengurangi dimensi fitur untuk mencegah overfitting.
  - **Dense(514, activation='relu')** → Lapisan tambahan untuk pemrosesan fitur lebih lanjut.
  - **Flatten()** → Mengubah output menjadi format vektor satu dimensi.
  - **Dense(class_count, activation='softmax')** → Lapisan output untuk klasifikasi multi-kelas.

### 2.2 Kompilasi Model

**Detail kompilasi model:**

- **Optimizer:** Adam dengan learning rate `1e-5`, dipilih untuk stabilitas dalam _fine-tuning_.
- **Loss Function:** `categorical_crossentropy`, digunakan karena ini merupakan klasifikasi multi-kelas.
- **Metrics:**
  - `accuracy` untuk mengevaluasi persentase prediksi yang benar.
  - `f1_score` untuk menilai keseimbangan antara presisi dan recall.

### 2.3 Ringkasan Model

Model yang telah dibuat dapat dilihat menggunakan perintah berikut:

```python
model.summary()
```

Perintah ini akan menampilkan jumlah parameter, jumlah layer, serta struktur detail dari model yang telah dibuat.

Dengan pemilihan model ini, diharapkan performa klasifikasi gambar daun anggur dapat dioptimalkan secara efisien menggunakan MobileNetV3-Small sebagai dasar transfer learning.

## 3. Training Model

Setelah pemilihan model selesai, langkah berikutnya adalah melakukan **training** model menggunakan dataset yang telah diproses sebelumnya.

### 3.1 Hyperparameter yang Digunakan

Beberapa hyperparameter yang digunakan dalam proses training adalah sebagai berikut:

- **Epochs = 25** → Model akan dilatih selama 25 iterasi penuh terhadap dataset training.
- **Batch Size 32** → Jumlah sampel yang digunakan dalam satu iterasi training, ditentukan dalam data generator.
- **Optimizer = Adam** → Digunakan **Adam Optimizer** dengan learning rate `1e-5` untuk melakukan pembaruan bobot secara adaptif.
- **Loss Function = Categorical Crossentropy** → Fungsi loss yang digunakan untuk klasifikasi multi-kelas.
- **Metrics = Accuracy & F1 Score** → Evaluasi model menggunakan **akurasi** dan **F1-score** untuk mengukur keseimbangan antara precision dan recall.

### 3.2 Validasi Model

Dataset validasi digunakan untuk mengevaluasi performa model setelah setiap epoch. Hasil validasi ini membantu dalam mendeteksi **overfitting** atau **underfitting**, serta memastikan model memiliki generalisasi yang baik pada data baru.

### 3.3 Output Training

Selama proses training, model akan menampilkan:

- **Loss** dan **Akurasi** pada dataset training dan validasi.
- Perkembangan metrik **F1-score** sebagai indikator performa model.
- Tren perubahan loss dan akurasi selama training dalam bentuk grafik (dapat divisualisasikan menggunakan Matplotlib).

Berikut adalah hasil akhir dari proses training model:

![Hasil training](/image/Hasil-training.png)

| Metrik             | Training | Validation |
| ------------------ | -------- | ---------- |
| **Loss**     | 0.0211   | 0.0440     |
| **Accuracy** | 0.9915   | 0.9856     |
| **F1-Score** | 0.9929   | 0.9856     |

Setelah training selesai, model akan siap untuk dilakukan evaluasi dan pengujian menggunakan dataset testing.

## 4. Evaluasi Model

Setelah training selesai, model dievaluasi menggunakan **Test Set** untuk mengukur performanya pada data yang belum pernah dilihat sebelumnya.

### 4.1 Proses Evaluasi

Evaluasi dilakukan dengan menguji model pada dataset **Test Set** yang berisi gambar yang tidak termasuk dalam dataset training maupun validasi. Model akan memprediksi kelas dari setiap gambar dalam test set, dan hasilnya dibandingkan dengan label sebenarnya.

### 4.2 Analisis Confusion Matrix

Hasil evaluasi divisualisasikan menggunakan **Confusion Matrix**, yang menunjukkan jumlah prediksi benar dan salah untuk setiap kelas. Confusion matrix ini memberikan gambaran detail tentang bagaimana model melakukan klasifikasi dan kategori mana yang paling sering keliru diklasifikasikan.

![Confusion matrix](/image/)

Dari confusion matrix yang didapatkan, terlihat bahwa model memiliki akurasi tinggi dalam mengklasifikasikan semua kategori, dengan kesalahan klasifikasi yang sangat minim.

### 4.3 Evaluasi Metrik

Untuk menilai performa model secara lebih rinci, digunakan metrik berikut:

- **Precision**: Seberapa banyak prediksi yang benar dibandingkan total prediksi positif.
- **Recall**: Seberapa banyak prediksi benar dibandingkan total data yang seharusnya diprediksi benar.
- **F1-Score**: Rata-rata harmonis antara precision dan recall.
- **Akurasi**: Seberapa banyak prediksi yang benar dibandingkan total data.

Hasil evaluasi model berdasarkan metrik tersebut adalah sebagai berikut:

| Label              | Precision   | Recall      | F1-Score         | Support        |
| ------------------ | ----------- | ----------- | ---------------- | -------------- |
| Background         | 1.0000      | 1.0000      | 1.0000           | 229            |
| Grape Black Rot    | 0.9913      | 0.9703      | 0.9807           | 236            |
| Grape Esca         | 0.9821      | 0.9928      | 0.9874           | 277            |
| Grape Leaf Blight  | 0.9907      | 0.9907      | 0.9907           | 216            |
| Grape Healthy      | 0.9770      | 1.0000      | 0.9884           | 85             |
| **Accuracy** | **-** | **-** | **0.9895** | **1043** |

Hasil evaluasi menunjukkan bahwa model memiliki performa yang sangat baik dengan akurasi **98.95%** dan nilai F1-Score yang tinggi untuk semua kelas.

### 4.4 Penyimpanan Model

Setelah evaluasi selesai, model disimpan agar dapat digunakan kembali tanpa perlu dilatih ulang:

```python
model.save('pre_trained_model/MobileNetV3_GrapePlantVillage.h5')
model.save('pre_trained_model/MobileNetV3_GrapePlantVillage.keras')
```

Dengan demikian, model siap digunakan untuk mengklasifikasikan gambar baru secara otomatis.

## 5. Pembuatan UI Website

Untuk mempermudah penggunaan model klasifikasi penyakit tanaman anggur, dibuat sebuah UI berbasis web. Website ini memungkinkan pengguna untuk mengunggah gambar daun anggur dan mendapatkan hasil prediksi klasifikasi secara langsung.

### 5.1 Teknologi yang Digunakan

- **Front-end:** Laravel digunakan untuk membangun antarmuka pengguna yang responsif dan mudah digunakan.
- **Back-end:** Flask digunakan sebagai API server yang menghubungkan model deep learning dengan website.
- **Hosting:** Website di-deploy menggunakan VPS dengan domain dari DomaiNesia.

### 5.2 Tampilan Website

Berikut adalah tampilan antarmuka utama dari website klasifikasi penyakit tanaman anggur:
![Tampilan Website](/image/website-1.png)

### 5.3 Klasifikasi Gambar

Pengguna dapat mengunggah gambar daun anggur, dan sistem akan menampilkan hasil klasifikasi berdasarkan model yang telah dilatih. Berikut adalah tampilan hasil klasifikasi setelah gambar diunggah:
*(Gambar tampilan hasil klasifikasi akan ditambahkan di sini)*
