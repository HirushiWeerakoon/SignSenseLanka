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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = $_POST['name']; // Field name should be 'name'
    $password = $_POST['password'];

    // Fetch user data based on the username
    $sql = "SELECT * FROM users WHERE name = ?"; // Use 'name' as in HTML
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // Bind username
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];

            // Redirect based on user type
            if ($user['user_type'] == "Children") {
                header("Location: child-home.html");
            } else {
                header("Location: parent-home.html");
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with this name.";
    }

    $stmt->close();
}

$conn->close();
?>
