<?php
require 'config.php';

$action = $_POST['action'];

if ($action === 'create') {
    $title = $_POST['title'];
    $release_date = $_POST['release_date'];
    $description = $_POST['description'];
    $poster_image_url = $_POST['poster_image_url'];

    $sql = "INSERT INTO movies (title, release_date, description, poster_image_url) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->errorInfo()[2]);
    }

    $stmt->execute([$title, $release_date, $description, $poster_image_url]);

} elseif ($action === 'delete') {
    $selected_movies = $_POST['selected_movies'];

    foreach ($selected_movies as $movie_id) {
        $sql = "DELETE FROM user_movies WHERE movie_id=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->errorInfo()[2]);
        }

        $stmt->execute([$movie_id]);

        $sql = "DELETE FROM movies WHERE movie_id=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->errorInfo()[2]);
        }

        $stmt->execute([$movie_id]);
    }
} elseif ($action === 'update_confirm') {
    $movie_id = $_POST['movie_id'];
    $title = $_POST['title'];
    $release_date = $_POST['release_date'];
    $description = $_POST['description'];
    $poster_image_url = $_POST['poster_image_url'];

    $sql = "UPDATE movies SET title=?, release_date=?, description=?, poster_image_url=? WHERE movie_id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->errorInfo()[2]);
    }

    $stmt->execute([$title, $release_date, $description, $poster_image_url, $movie_id]);
}

$conn = null;

header("Location: manage_movies.php");
?>
