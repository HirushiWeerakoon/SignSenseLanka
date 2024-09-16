SignSense Lanka System

Project Overview
    The SignSense Lanka System is a web-based application designed to assist children and parents in learning Sinhala Sign Language through an interactive, machine learning-based gesture recognition system. The system targets early childhood learners and focuses on basic alphabet letters and numbers using hand signs. It offers real-time feedback and predictions for gestures, creating a user-friendly environment for both parents and children to practice and enhance their sign language skills.

Features:
    User Authentication: Secure login and registration with "Remember Me" functionality for convenience.
    Parent and Child Modes: Separate interfaces for parents and children, allowing parents to learn signs and teach their children while children practice and receive feedback.
    Gesture Recognition: Real-time hand gesture detection using a machine learning model, which identifies and predicts Sinhala sign language numbers and alphabet letters.
    Matching Game: An engaging activity for children to reinforce their learning by matching gestures with the corresponding signs.
    Responsive Design: The user interface is designed to be accessible and easy to navigate for users of all tech literacy levels.

Technology Stack
    Frontend: HTML, CSS, JavaScript
    Backend: Flask (Python)
    Machine Learning: TensorFlow, OpenCV, MediaPipe
    Database: MySQL (for user login data)
    Development Tools: Visual Studio Code, Google Colab, Git

System Architecture
The system consists of several components:

    Web Browser: Interface for users to interact with the system.
    Frontend: Handles UI/UX, including login/registration forms, prediction pages, and games.
    Backend: Manages user sessions, routes, database connections, and prediction logic.
    Machine Learning Model: Processes video feeds to recognize hand gestures.
    MySQL Database: Stores user credentials securely.

Setup Instructions:
1. Install XAMPP
The project requires XAMPP to run the MySQL database and the Apache server.

Download and install XAMPP from https://www.apachefriends.org/index.html.
Once installed, open XAMPP Control Panel and start both Apache and MySQL services.

2. Create the MySQL Database
The project will not work if the database is not set up correctly. Follow these steps to create the database:

    1. Open the phpMyAdmin panel in your web browser by going to:http://localhost/phpmyadmin/

    2. In phpMyAdmin, create a new database called user:
    Click on the Databases tab.
    Enter user as the database name.
    Click Create.

    3. Import the provided SQL file to set up the users table: 
    In the user database, go to the Import tab.
    Select the SQL file located at /database/user.sql in the project folder.
    Click Go to import the schema. This will create the users table with fields for id, name, email, password, and user type.

3. Clone the Repository
Clone the project repository using Git:

git clone https://github.com/HirushiWeerakoon/SignSenseLanka.git
cd sinhala_sign_recognition

4. Install Dependencies
Install the necessary Python dependencies: pip install -r requirements.txt

5. Run the Flask Application
Run the Flask app to start the backend server: python main.py

6. Access the Application
Open your web browser and go to: http://127.0.0.1:5000

Ensure that XAMPP is running and the MySQL database has been created. The system will not work if these prerequisites are not met.

Usage:

1.Login/Registration: On the login page, users can either sign in with their existing credentials or register as a new parent or child.
2.Parent Interface: After logging in, parents can choose between learning numbers or alphabet signs. They can practice signs and receive real-time feedback on their gestures.
3.Child Interface: Children can also select numbers or alphabet signs to practice and play matching games to test their knowledge.
4.Gesture Prediction: Users can initiate a video feed where they perform signs. The machine learning model will predict the gesture and display the corresponding letter or number.

Folder Structure:

├── /static                # CSS, JS, and image files for the frontend
├── /templates             # HTML files for the web interface
├── /models                # Pre-trained machine learning models
├── /notebooks             # Machine learning model notebook
├── /data                  # Data collection and processing scripts
├── /database              # SQL scripts for MySQL database setup
├── /utils                 # Utility scripts and front image
├── /main.py               # Core Flask application files (routes, logic)
├── requirements.txt       # Python dependencies
└── README.md              # Project documentation

Contributor
Hirushi Weerakoon
