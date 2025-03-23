<?php
session_start();
$conn = new mysqli("localhost", "root", "", "velvera");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$showLoginModal = false; // Flag to show login modal after registration

// Handle Registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if user exists
    $checkUser = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $checkUser->bind_param("ss", $username, $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or Email already exists'); window.location.href='index.html';</script>";
    } else {
        // Insert new user
        $query = $conn->prepare("INSERT INTO users (full_name, email, username, password) VALUES (?, ?, ?, ?)");
        $query->bind_param("ssss", $full_name, $email, $username, $password);

        if ($query->execute()) {
            header("Location: index.html?registered=true"); // Redirect with flag
            exit();
        } else {
            echo "<script>alert('Registration failed'); window.location.href='index.html';</script>";
        }
    }
}

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username=? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row['username'];
            $_SESSION['user_id'] = $row['id']; // Store user ID in session
            header("Location: index.html"); // Redirect after login
            exit();
        } else {
            echo "<script>alert('Invalid credentials'); window.location.href='index.html';</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location.href='index.html';</script>";
    }

    $stmt->close();
}


?>