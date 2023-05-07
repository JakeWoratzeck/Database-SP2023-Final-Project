<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require 'config.php';

$sql = "SELECT * FROM movies";
$stmt = $conn->prepare($sql);
$stmt->execute();
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * 
        FROM user_movies_view 
        WHERE user_id = ? 
        ORDER BY movie_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$user_movies = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $user_movies[$row['movie_id']] = $row;
}

$status_options = ['watching', 'completed', 'want_to_watch'];
?>
<!DOCTYPE html>
<html>
<head>
<style>
        body {
            padding-top: 60px; 
        }

        .navbar {
            height: 50px; 
            overflow: hidden;
            background-color: #333;
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            color: white;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar-center {
            text-align: center;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="landing.php">Home</a>
        <div class="navbar-center"><h2>My Movies</h2></div>
        <div style="float: right;"><a href="logout.php">Logout</a></div>
    </div>
<?php
echo "<form method='post' action='process_user_movies.php'>";
echo "<table>";

echo "<tr>";
echo "<th>Poster</th>";
echo "<th>Title</th>";
echo "<th>Description</th>";
echo "<th>Release Date</th>";
echo "<th>Liked</th>";
echo "<th>Status</th>";
echo "</tr>";

foreach ($movies as $movie) {
    $liked = isset($user_movies[$movie['movie_id']]['liked']) ? $user_movies[$movie['movie_id']]['liked'] : 0;
    $status = isset($user_movies[$movie['movie_id']]['status']) ? $user_movies[$movie['movie_id']]['status'] : '';

    echo "<tr>";
    echo "<td><img src='{$movie['poster_image_url']}' height='100'></td>";
    echo "<td>{$movie['title']}</td>";
    echo "<td>{$movie['description']}</td>";
    echo "<td>{$movie['release_date']}</td>";
    
    if ($status == "completed") {
        echo "<td><input type='checkbox' name='liked[{$movie['movie_id']}]' value='1' " . ($liked ? 'checked' : '') . "></td>";
    } else {
        echo "<td></td>";
    }

    echo "<td>";
    echo "<select name='status[{$movie['movie_id']}]'>";
    echo "<option value=''></option>";
    foreach ($status_options as $option) {
        $selected = ($status == $option) ? 'selected' : '';
        echo "<option value='$option' $selected>$option</option>";
    }
    echo "</select>";
    echo "</td>";
    echo "<input type='hidden' name='movie_id[]' value='{$movie['movie_id']}'>";
    echo "</tr>";
}


echo "</table>
      <button type='submit'>Save</button>
    </form>";
?>
</body>
</html>