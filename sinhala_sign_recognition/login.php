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

// Sanitize input data
$name = mysqli_real_escape_string($conn, $_POST['name']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// Fetch user data from the database
$sql = "SELECT * FROM users WHERE name='$name'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Check password
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // Session management if needed
        session_start();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_type'] = $row['user_type'];

        // Redirect based on user type
        if ($row['user_type'] == 'children') {
            header("Location: Children/child-home.html");
        } else if ($row['user_type'] == 'parent') {
            header("Location: Parent/parent-home.html");
        }
        exit; // Ensure no further code is executed after redirect
    } else {
        // Redirect with error for invalid password
        header("Location: index.html?error=invalid_password");
        exit;
    }
} else {
    // Redirect with error for user not found
    header("Location: index.html?error=user_not_found");
    exit;
}

$conn->close();
?>

