<?php
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "user";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and validate input data
$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$user_type = mysqli_real_escape_string($conn, $_POST['user_type']);

// Check if user already exists
$sql_check = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // User already exists
    header("Location: index.html?error=exists");
    exit;
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert data into the database
$sql = "INSERT INTO users (name, email, password, user_type) VALUES ('$name', '$email', '$hashed_password', '$user_type')";

if ($conn->query($sql) === TRUE) {
    // Redirect based on user type
    if ($user_type == 'children') {
        header("Location: Children/child-home.html");
    } else if ($user_type == 'parent') {
        header("Location: parent-home.html");
    }
    exit; // Ensure no further code is executed after redirect
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

