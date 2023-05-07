<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie CRUD</title>
    <script>
    function handleUpdateButtonClick() {
    const checkboxes = document.getElementsByName("selected_movies[]");
    let selectedMovieId;
    let selectedCount = 0;

    for (let i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            selectedMovieId = checkboxes[i].value;
            selectedCount++;
            if (selectedCount > 1) {
                alert("Please select only one movie to update.");
                return;
            }
        }
    }

    if (selectedMovieId) {
        window.location.href = "manage_movies.php?update_id=" + selectedMovieId;
    } else {
        alert("Please select a movie to update.");
    }
}

function toggleAddNewMovieForm() {
    const form = document.getElementById("addNewMovieForm");
    if (form.style.display === "none") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}

function handleDeleteButtonClick() {
    const confirmed = confirm("Are you sure you want to delete the selected movies?");
    if (confirmed) {
        const form = document.getElementById("movieListForm");
        const actionInput = document.getElementById("movieListFormAction");
        actionInput.value = "delete";
        form.submit();
    }
}
</script>
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
<?php
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['admin'] !== '1') {
        header('Location: index.php');
        exit();
    }
?>

<div class="navbar">
    <a href="landing.php">Home</a>
    <div class="navbar-center"><h2>Manage Movies</h2></div>
    <div style="float: right;"><a href="logout.php">Logout</a></div>
</div>
    <form action="process_movies.php" method="post">
        <table>
            <tr>
                <th>Select</th>
                <th>Poster</th>
                <th>Title</th>
                <th>Release Date</th>
                <th>Description</th>
            </tr>
            <?php
                require 'config.php';

                $sql = "SELECT * FROM movies";
                $stmt = $conn->query($sql);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td><input type='checkbox' name='selected_movies[]' value='{$row['movie_id']}'></td>
                            <td><img src='{$row['poster_image_url']}' alt='Poster' style='max-width: 100px; max-height: 150px;'></td>
                            <td>{$row['title']}</td>
                            <td>{$row['release_date']}</td>
                            <td>{$row['description']}</td>
                        </tr>";
                }

                if ($stmt->rowCount() == 0) {
                    echo "<tr><td colspan='7'>No movies found.</td></tr>";
                }

                $conn = null;
                
            ?>
        </table>
        <button onclick="handleDeleteButtonClick()">Delete Selected Movies</button>
        <button type="button" onclick="handleUpdateButtonClick()">Update Selected Movie</button>
        <input type="hidden" name="action" id="movieListFormAction">
    </form>
    <button type="button" onclick="toggleAddNewMovieForm()">Add New Movie</button>

    <?php
    if (isset($_GET['update_id'])) {
        require 'config.php';
        $update_id = $_GET['update_id'];

        $sql = "SELECT * FROM movies WHERE movie_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $update_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($movie = $stmt->fetch(PDO::FETCH_ASSOC)) {
    ?>
        <h2>Update Movie</h2>
        <form action="process_movies.php" method="post">
            <input type="hidden" name="movie_id" value="<?php echo $movie['movie_id']; ?>">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?php echo $movie['title']; ?>" required>
            <br>
            <label for="release_date">Release Date:</label>
            <input type="date" name="release_date" id="release_date" value="<?php echo $movie['release_date']; ?>" required>
            <br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?php echo $movie['description']; ?></textarea>
            <br>
            <label for="poster_image_url">Poster URL:</label>
            <input type="text" name="poster_image_url" id="poster_image_url" value="<?php echo $movie['poster_image_url']; ?>">
            <br>
        <button type="submit" name="action" value="update_confirm">Update Movie</button>
    </form>
<?php
}
$conn = null;
}
?>

<div id="addNewMovieForm" style="display: none;">
<h2>Add New Movie</h2>
<form action="process_movies.php" method="post">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required>
    <br>
    <label for="release_date">Release Date:</label>
    <input type="date" name="release_date" id="release_date" required>
    <br>
    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea>
    <br>
    <label for="poster_image_url">Poster URL:</label>
    <input type="text" name="poster_image_url" id="poster_image_url">
    <br>
    <button type="submit" name="action" value="create">Add Movie</button>
</form>
</div> 
</body>
</html>