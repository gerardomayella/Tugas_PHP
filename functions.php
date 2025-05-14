<?php

// Koneksi ke database
function connection() {
    $conn = mysqli_connect("localhost", "root", "", "tugasphp");
    if (!$conn) {
        die("Koneksi ke database gagal: " . mysqli_connect_error());
    }
    return $conn;
}

// Fungsi untuk membuat tabel To-Do List untuk user baru
function createTodoTable($username) {
    $conn = connection();
    $tableName = "todo_" . $username;

    // Query untuk membuat tabel dengan foreign key
    $query = "CREATE TABLE IF NOT EXISTS $tableName (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        activity TEXT NOT NULL,
        is_done BOOLEAN DEFAULT 0,
        CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
    )";

    if (!mysqli_query($conn, $query)) {
        die("Error creating table: " . mysqli_error($conn));
    }
}

// Fungsi untuk menambahkan aktivitas
function addActivity($username, $userId, $activity) {
    $conn = connection();
    $tableName = "todo_" . $username;
    $query = "INSERT INTO $tableName (user_id, activity) VALUES ('$userId', '$activity')";
    mysqli_query($conn, $query);
}

// Fungsi untuk menandai aktivitas selesai
function markAsDone($username, $id) {
    $conn = connection();
    $tableName = "todo_" . $username;
    $query = "UPDATE $tableName SET is_done = 1 WHERE id = $id";
    mysqli_query($conn, $query);
}

// Fungsi untuk menghapus aktivitas
function deleteActivity($username, $id) {
    $conn = connection();
    $tableName = "todo_" . $username;
    $query = "DELETE FROM $tableName WHERE id = $id";
    mysqli_query($conn, $query);
}

// Fungsi untuk mendapatkan aktivitas dengan paginasi
function getActivities($username, $start, $limit) {
    $conn = connection();
    $tableName = "todo_" . $username;
    $query = "SELECT * FROM $tableName LIMIT $start, $limit";
    $result = mysqli_query($conn, $query);
    $activities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $activities[] = $row;
    }
    return $activities;
}

// Fungsi untuk menghitung total aktivitas
function countActivities($conn, $username) {
    if (!$username) {
        return 0; // Jika username tidak valid, kembalikan 0
    }
    $tableName = "todo_" . $username;
    $query = "SELECT COUNT(*) AS total FROM $tableName";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        return 0; // Jika query gagal, kembalikan 0
    }
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

function register($data) {
    $conn = connection();

    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);

    // Cek apakah username sudah ada
    $result = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");
    if (mysqli_fetch_assoc($result)) {
        echo "<script>
                alert('Username sudah terdaftar!');
              </script>";
        return false;
    }

    // Cek konfirmasi password
    if ($password !== $password2) {
        echo "<script>
                alert('Konfirmasi password tidak sesuai!');
              </script>";
        return false;
    }

    // Enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Tambahkan user baru ke database
    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}
?>