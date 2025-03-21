<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
Route::get('/', function () {
    return redirect('/upload');
});
Route::get('/upload', function () {
    return view('upload');
});

Route::post('/classify', function (Request $request) {
    // Validasi input
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Definisi kelas sesuai ketentuan
    $classes = [
        0 => 'Background (Gambar Bukan Daun)',
        1 => 'Black Rot',
        2 => 'Esca',
        3 => 'Leaf Blight',
        4 => 'Healthy'
    ];

    // Ambil file gambar dan simpan ke folder public/uploads
    $image = $request->file('image');
    $imagePath = $image->store('uploads', 'public');

    // Kirim gambar ke API eksternal
    $response = Http::attach(
        'image', file_get_contents($image->getRealPath()), $image->getClientOriginalName()
    )->post('http://model.grape.albirr.web.id/predict');

    // Ambil hasil dari API
    $result = $response->json();

    // Ambil class yang sesuai
    $predicted_class = $result['predicted_class'] ?? null;
    $class_name = $predicted_class !== null ? ($classes[$predicted_class] ?? 'Unknown') : 'Unknown';
    $confidence = $result['confidence'] ?? 0;

    return view('upload', compact('class_name', 'confidence', 'imagePath'));
});
