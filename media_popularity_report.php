<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("CALL GetMostPopularMoviesAndTVShows()");
$stmt->execute();

$popularMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->nextRowset();
$popularTVShows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->nextRowset();
$mostLikedMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->nextRowset();
$mostLikedTVShows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Popularity Report</title>
</head>
<body>
<h1>Media Popularity Report</h1>

<h2>Most Popular Movies</h2>
<table>
    <tr>
        <th>Title</th>
        <th>Number of Users</th>
    </tr>
    <?php foreach ($popularMovies as $movie): ?>
        <tr>
            <td><?php echo htmlspecialchars($movie['title']); ?></td>
            <td><?php echo $movie['num_users']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Most Popular TV Shows</h2>
<table>
    <tr>
        <th>Title</th>
        <th>Number of Users</th>
    </tr>
    <?php foreach ($popularTVShows as $tv_show): ?>
        <tr>
            <td><?php echo htmlspecialchars($tv_show['title']); ?></td>
            <td><?php echo $tv_show['num_users']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Most Liked Movies</h2>

<table>
    <tr>
        <th>Title</th>
        <th>Number of Likes</th>
    </tr>
    <?php foreach ($mostLikedMovies as $movie): ?>
        <tr>
            <td><?php echo htmlspecialchars($movie['title']); ?></td>
            <td><?php echo $movie['num_likes']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<h2>Most Liked TV Shows</h2>
<table>
    <tr>
        <th>Title</th>
        <th>Number of Likes</th>
    </tr>
    <?php foreach ($mostLikedTVShows as $tv_show): ?>
        <tr>
            <td><?php echo htmlspecialchars($tv_show['title']); ?></td>
            <td><?php echo $tv_show['num_likes']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<a href="landing.php">Back to Dashboard</a>

</body>
</html>
