<?php
// ambil koneksi dari file functions.php
require 'functions.php';
$conn = connection();
// set session
session_start();
// cek cookie
if (isset($_COOKIE['id']) && isset($_COOKIE['key'])) {
    $id = $_COOKIE['id'];
    $key = $_COOKIE['key'];

    error_log("Checking cookies: id=$id, key=$key");

    // ambil username berdasarkan id
    $result = mysqli_query($conn, "SELECT username FROM users WHERE id = '$id'");
    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // cek cookie dan username
        if ($key === hash('sha256', $row['username'])) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $row['username']; // Tambahkan username ke session
            $_SESSION['user_id'] = $id; // Tambahkan user_id ke session
        } else {
            // Hapus cookie jika tidak valid
            setcookie('id', '', time() - 3600, '/');
            setcookie('key', '', time() - 3600, '/');
            error_log("Invalid cookie detected. Cookies cleared.");
        }
    } else {
        // Hapus cookie jika tidak valid
        setcookie('id', '', time() - 3600, '/');
        setcookie('key', '', time() - 3600, '/');
        error_log("No matching user for cookie ID. Cookies cleared.");
    }
}

// Debugging log untuk memeriksa sesi
error_log("Session state before redirect: " . print_r($_SESSION, true));

// ketika sudah login
if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    // Debugging log
    error_log("User already logged in. Redirecting to index.php");
    if (basename($_SERVER['PHP_SELF']) !== 'index.php') { // Cegah pengalihan jika sudah di index.php
        header("Location: index.php");
        exit;
    }
}

// Debugging log untuk memeriksa cookie
error_log("Cookie state after processing: " . print_r($_COOKIE, true));

if (isset($_POST["login"])) {
    $username = mysqli_real_escape_string($conn, $_POST["username"]); // Hindari SQL Injection
    $password = $_POST["password"];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    // cek username
    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // cek password
        if (password_verify($password, $row["password"])) {
            $_SESSION["login"] = true;
            $_SESSION["username"] = $row["username"]; // Tambahkan username ke session
            $_SESSION["user_id"] = $row["id"]; // Tambahkan user_id ke session

            // cek remember me
            if (isset($_POST["remember"])) {
                // buat cookie
                setcookie('id', $row['id'], time() + 60 * 60 * 24); // Cookie berlaku 1 hari
                setcookie('key', hash('sha256', $row['username']), time() + 60 * 60 * 24);
            }
            // Debugging log
            error_log("Login successful. Redirecting to index.php");
            header("Location: index.php");
            exit;
        } else {
            error_log("Password incorrect for username: $username");
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        error_log("Username not found: $username");
        echo "<script>alert('Username tidak terdaftar!');</script>";
    }
}

// Debugging log untuk memeriksa cookie
error_log("Cookie state: " . print_r($_COOKIE, true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <link href="public/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            margin-top: 50px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .profile {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile">
            <img src="me.jpg" alt="Profile Picture">
            <h3>Gerardo Mayella Ardianta</h3>
            <p>NIM: 235314003</p>
        </div>
        <h1 class="text-center mb-4">Halaman Login</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label">Ingat saya</label>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar</a></p>
    </div>
</body>
</html>