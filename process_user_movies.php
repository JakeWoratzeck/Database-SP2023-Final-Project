<?php
session_start();

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; 
    $liked_movies = isset($_POST['liked']) ? $_POST['liked'] : [];
    $status_movies = isset($_POST['status']) ? $_POST['status'] : [];

    foreach ($status_movies as $movie_id => $status) {
        $liked = isset($liked_movies[$movie_id]) ? 1 : 0;

        $sql = "SELECT * FROM user_movies WHERE user_id=? AND movie_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $movie_id]);
        $row = $stmt->fetch();

        if ($status == '') { 
            if ($row) { 
                $sql = "DELETE FROM user_movies WHERE user_id=? AND movie_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id, $movie_id]);
            }

        } else { 
            if ($row) { 
                $sql = "UPDATE user_movies SET liked=?, status=? WHERE user_id=? AND movie_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$liked, $status, $user_id, $movie_id]);
            } else { 
                $sql = "INSERT INTO user_movies (user_id, movie_id, liked, status) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user_id, $movie_id, $liked, $status]);
            }
        }
    }
}

header('Location: manage_user_movies.php');
exit();

?>
