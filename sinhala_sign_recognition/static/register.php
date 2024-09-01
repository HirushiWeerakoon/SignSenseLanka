<?php
// Debugging code to display posted data (Remove this in production)
echo "<pre>";
print_r($_POST);
echo "</pre>";
?>

<?php
$servername = "localhost";
$username = "root"; // Adjust if needed
$password = ""; // Adjust if needed
$dbname = "user"; // Adjusted database name
$port = 3307; // Updated port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: index.html?error=exists");
        exit();
    } else {
        // Insert the new user into the database
        $sql = "INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password_hash, $user_type);
        
        if ($stmt->execute()) {
            // Redirect based on user type
            if ($user_type == "Children") {
                header("Location: child-home.html");
            } else {
                header("Location: parent-home.html");
            }
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    
    $stmt->close();
}

$conn->close();
?>