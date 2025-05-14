<?php
session_start();
require 'functions.php';

$conn = connection(); // Menggunakan fungsi connection()

// Redirect ke login jika belum login
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

// Validasi session username dan user_id
if (!isset($_SESSION["username"]) || !isset($_SESSION["user_id"])) {
    echo "<script>alert('Session tidak valid. Silakan login kembali.');</script>";
    header("Location: login.php");
    exit;
}

$username = $_SESSION["username"];
$userId = $_SESSION["user_id"];

// Tambah aktivitas
if (isset($_POST["add"])) {
    $activity = $_POST["activity"];
    addActivity($username, $userId, $activity); 
}

// Tandai selesai
if (isset($_GET["done"])) {
    $id = $_GET["done"];
    markAsDone($username, $id);
}

// Hapus aktivitas
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    deleteActivity($username, $id);
}

// Paginasi
$limit = 5;
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$start = ($page - 1) * $limit;
$totalActivities = countActivities($conn, $username);
$totalPages = ceil($totalActivities / $limit);

$activities = getActivities($username, $start, $limit); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="public/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light gray background */
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: #ffffff; /* White background */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .btn-primary {
            background-color: #007bff; /* Elegant blue */
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .done {
            text-decoration: line-through;
            color: gray;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">To-Do List</h1>
        <form action="" method="post" class="mb-3">
            <div class="input-group">
                <input type="text" name="activity" class="form-control" placeholder="Teks to do" required>
                <button type="submit" name="add" class="btn btn-primary">Tambah</button>
            </div>
        </form>

        <ul class="list-group mb-3">
            <?php foreach ($activities as $activity): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="<?= $activity['is_done'] ? 'done' : '' ?>">
                        <?= htmlspecialchars($activity['activity']) ?>
                    </span>
                    <div>
                        <?php if (!$activity['is_done']): ?>
                            <a href="?done=<?= $activity['id'] ?>" class="btn btn-success btn-sm">Selesai</a>
                        <?php endif; ?>
                        <a href="?delete=<?= $activity['id'] ?>" class="btn btn-danger btn-sm">Hapus</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Paginasi -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a href="?page=<?= $i ?>" class="page-link"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <div class="text-center">
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>
</body>
</html>