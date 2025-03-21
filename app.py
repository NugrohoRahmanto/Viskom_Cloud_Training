from flask import Flask, request, jsonify
import tensorflow as tf
import numpy as np
import io
from PIL import Image
from tensorflow.keras.applications import MobileNetV3Small

app = Flask(__name__)

# Load model with custom_objects
model = tf.keras.models.load_model(
    'MobileNetV3_GrapePlantVillage.keras',
    custom_objects={'MobileNetV3Small': MobileNetV3Small}
)

def preprocess_image(image_file, target_size=(224, 224)):
    """Convert uploaded file to preprocessed numpy array"""
    image = Image.open(image_file).convert("RGB")
    image = image.resize(target_size)
    # image = np.array(image) / 255.0  # Normalize
    image = np.expand_dims(image, axis=0)
    return image

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # Periksa apakah file dikirim
        if 'image' not in request.files:
            return jsonify({'error': 'No file uploaded'}), 400
        
        image_file = request.files['image']  # Ambil file dari request
        image_array = preprocess_image(image_file)
        prediction = model.predict(image_array)
        predicted_class = int(np.argmax(prediction, axis=1)[0])
        confidence = float(np.max(prediction))

        return jsonify({
            'predicted_class': predicted_class,
            'confidence': confidence
        })

    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    print("🔥 Starting Flask API...")
    print("🚀 Endpoint available at: http://localhost:5000/predict [POST]")
    app.run(debug=True)
