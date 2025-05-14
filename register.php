<?php 
// registrasi ada di function.php
require 'functions.php';
// koneksi ke database lewat functions.php
$conn = connection();
// tidak perlu session_start() di halaman ini

if (isset($_POST["register"])) {

    if (register($_POST) > 0) {
        $username = strtolower(stripslashes($_POST["username"]));
        createTodoTable($username); // Membuat tabel To-Do List untuk user baru
        echo "<script>
                alert('User baru berhasil ditambahkan!');
                window.location.href = 'login.php';
              </script>";
        exit; 
    } else {
        echo mysqli_error($conn);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Registrasi</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Halaman Registrasi</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password2" class="form-label">Konfirmasi Password:</label>
                <input type="password" name="password2" id="password2" class="form-control" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary w-100">Daftar</button>
        </form>
        <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Kembali ke Halaman Login</a></p>
    </div>
</body>
</html>