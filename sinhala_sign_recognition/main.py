from flask import (
    Flask,
    render_template,
    request,
    redirect,
    url_for,
    session,
    Response,
    jsonify,
)
import cv2
import tensorflow as tf
import numpy as np
import mediapipe as mp
from sklearn.preprocessing import LabelEncoder
import mysql.connector
from werkzeug.security import generate_password_hash, check_password_hash

app = Flask(__name__)
app.secret_key = "admin"  # Secret key for session management

# Load the model and label encoder
model = tf.keras.models.load_model("model/hand_sign_model.h5")
label_encoder = LabelEncoder()
label_encoder.classes_ = np.load("data/classes.npy", allow_pickle=True)

mp_hands = mp.solutions.hands
hands = mp_hands.Hands(min_detection_confidence=0.8, min_tracking_confidence=0.5)

cap = cv2.VideoCapture(0)
frame = None
prediction = None

sinhala_letters = {
    1: '1',
    2: '2',
    3: '3',
    4: '4',
    5: '5',
    6: '6',
    7: '7',
    8: '8',
    9: '9',
    0: '10',
    10: 'අ',
    11: 'ආ',
    12: 'ඇ',
    13: 'ඉ',
    14: 'ඊ',
    15: 'උ',
    16: 'ග',
    17: 'ය',
    18: 'ල',
    19: 'හ'
}

def preprocess_landmarks(landmarks):
    landmarks = np.array(landmarks).flatten()
    landmarks = landmarks.reshape(1, 21, 3, 1)
    return landmarks


def generate_frames():
    global frame, prediction
    while True:
        success, frame = cap.read()
        if not success:
            break
        else:
            image = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            results = hands.process(image)
            if results.multi_hand_landmarks:
                for hand_landmarks in results.multi_hand_landmarks:
                    mp.solutions.drawing_utils.draw_landmarks(
                        frame, hand_landmarks, mp_hands.HAND_CONNECTIONS
                    )
                    landmarks = [[lm.x, lm.y, lm.z] for lm in hand_landmarks.landmark]
                    landmarks = preprocess_landmarks(landmarks)
                    prediction = model.predict(landmarks)
            ret, buffer = cv2.imencode(".jpg", frame)
            frame = buffer.tobytes()
            yield (b"--frame\r\n" b"Content-Type: image/jpeg\r\n\r\n" + frame + b"\r\n")

@app.route('/index')
def index():
    return render_template('index.html')

# Database connection
def get_db_connection():
    connection = mysql.connector.connect(
        host='localhost',
        user='root',
        password='',
        database='user',
        port=3307          
    )
    return connection

@app.route("/", methods=["GET", "POST"])
def login():
    error = None
    if request.method == "POST":
        username = request.form.get('name')
        password = request.form.get('password')

        try:
            connection = get_db_connection()
            cursor = connection.cursor(dictionary=True)

            # Fetch user details from the database
            cursor.execute("SELECT * FROM users WHERE name = %s", (username,))
            user = cursor.fetchone()

            if user:
                if check_password_hash(user['password'], password):
                    session['logged_in'] = True
                    session['user_id'] = user['id']
                    session['user_type'] = user['user_type']

                    # Redirect based on user type
                    if user['user_type'] == 'Parent':
                        return redirect(url_for('parent_home'))
                    elif user['user_type'] == 'Children':
                        return redirect(url_for('child_home'))
                else:
                    error = 'invalid_password'
            else:
                error = 'user_not_exist'

        except mysql.connector.Error as e:
            print(f"Database Error: {e}")
            error = 'database_error'

        finally:
            if cursor:
                cursor.close()
            if connection:
                connection.close()

    return render_template("index.html", error=error)

@app.route("/register", methods=["POST"])
def register():
    username = request.form['username']
    email = request.form['email']
    password = request.form['password']
    user_type = request.form['user_type']
    
    try:
        connection = get_db_connection()
        cursor = connection.cursor()
        
        # Check if the user already exists
        cursor.execute("SELECT * FROM users WHERE email = %s", (email,))
        if cursor.fetchone():
            return redirect(url_for('index') + "?error=exists")

        # Insert new user
        password_hash = generate_password_hash(password)
        cursor.execute(
            "INSERT INTO users (name, email, password, user_type) VALUES (%s, %s, %s, %s)",
            (username, email, password_hash, user_type)
        )
        connection.commit()

        # Redirect based on user type
        if user_type == 'Children':
            return redirect(url_for('child_home'))
        elif user_type == 'Parent':
            return redirect(url_for('parent_home'))
        else:
            return redirect(url_for('index'))

    except mysql.connector.Error:
        return redirect(url_for('index') + "?error=register")

    finally:
        if cursor:
            cursor.close()
        if connection:
            connection.close()


@app.route("/parent-home")
def parent_home():
    if not session.get('logged_in'):
        return redirect(url_for('login'))
    return render_template("parent-home.html")

@app.route("/child-home")
def child_home():
    if not session.get('logged_in'):
        return redirect(url_for('login'))
    return render_template("child-home.html")


# Main menu page
@app.route("/main_menu")
def main_menu():
    if not session.get("logged_in"):
        return redirect("login")
    return render_template("main_menu.html")


# Prediction page
@app.route("/prediction_page")
def prediction_page():
    if not session.get("logged_in"):
        return redirect("login")
    return render_template("prediction_page.html")


# Video feed route
@app.route("/video_feed")
def video_feed():
    return Response(
        generate_frames(), mimetype="multipart/x-mixed-replace; boundary=frame"
    )


# Prediction route
@app.route("/predict", methods=["POST"])
def predict():
    global prediction
    if prediction is not None:
        predicted_label = label_encoder.inverse_transform([np.argmax(prediction)])[0]
        predicted_number = int(predicted_label)
        predicted_letter = sinhala_letters.get(predicted_number, "Unknown")
        return jsonify({"prediction": predicted_letter})
    return jsonify({"prediction": "No hand detected"})


# Logout route
@app.route("/logout")
def logout():
    session.pop("logged_in", None)
    return redirect(url_for("index"))


if __name__ == "__main__":
    app.run(debug=True)
