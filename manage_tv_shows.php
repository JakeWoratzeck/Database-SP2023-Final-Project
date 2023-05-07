<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV Show CRUD</title>
    <script>
    function handleUpdateButtonClick() {
    const checkboxes = document.getElementsByName("selected_tv_shows[]");
    let selectedShowId;
    let selectedCount = 0;

    for (let i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            selectedShowId = checkboxes[i].value;
            selectedCount++;
            if (selectedCount > 1) {
                alert("Please select only one TV show to update.");
                return;
            }
        }
    }

    if (selectedShowId) {
        window.location.href = "manage_tv_shows.php?update_id=" + selectedShowId;
    } else {
        alert("Please select a TV show to update.");
    }
}

    function toggleAddNewTVShowForm() {
        const form = document.getElementById("addNewTVShowForm");
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    function handleDeleteButtonClick() {
        const confirmed = confirm("Are you sure you want to delete the selected TV shows?");
        if (confirmed) {
            const form = document.getElementById("tvShowListForm");
            const actionInput = document.getElementById("tvShowListFormAction");
            actionInput.value = "delete";
            form.submit();
        }
    }

    function handleManageEpisodesClick() {
        const checkboxes = document.getElementsByName("selected_tv_shows[]");
        let selectedShowId;
        let selectedCount = 0;

        for (let i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                selectedShowId = checkboxes[i].value;
                selectedCount++;
                if (selectedCount > 1) {
                    alert("Please select only one TV show to manage episodes.");
                    return;
                }
            }
        }

        if (selectedShowId) {
            window.location.href = "manage_episodes.php?tv_show_id=" + selectedShowId;
        } else {
            alert("Please select a TV show to manage episodes.");
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
    <div class="navbar-center"><h2>Manage TV Shows</h2></div>
    <div style="float: right;"><a href="logout.php">Logout</a></div>
</div>
    <form action="process_tv_shows.php" method="post">
        <table>
            <tr>
                <th>Select</th>
                <th>Poster</th>
                <th>Title</th>
                <th>Release Date</th>
                <th>Description</th>
                <th>No. of Seasons</th>
                <th>No. of Episodes</th>
            </tr>
            <?php
                require 'config.php';

                $sql = "SELECT * FROM tv_shows";
                $stmt = $conn->query($sql);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                    <td><input type='checkbox' name='selected_tv_shows[]' value='{$row['tv_show_id']}'></td>
                    <td><img src='{$row['poster_image_url']}' alt='Poster' style='max-width: 100px; max-height: 150px;'></td>
                    <td>{$row['title']}</td>
                    <td>{$row['release_date']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['seasons']}</td>
                    <td>{$row['episodes']}</td>
                    <td><a href='manage_episodes.php?tv_show_id={$row['tv_show_id']}'>Manage Episodes</a></td>
                </tr>";
                }

                if ($stmt->rowCount() == 0) {
                    echo "<tr><td colspan='8'>No TV shows found.</td></tr>";
                }

                $conn = null;
            ?>
        </table>
        <button onclick="handleDeleteButtonClick()">Delete Selected TV Shows</button>
        <button type="button" onclick="handleUpdateButtonClick()">Update Selected TV Show</button>
        <input type="hidden" name="action" id="tvShowListFormAction">
    </form>
    <button type="button" onclick="toggleAddNewTVShowForm()">Add New TV Show</button>

    <?php
    if (isset($_GET['update_id'])) {
        require 'config.php';
        $update_id = $_GET['update_id'];

        $sql = "SELECT * FROM tv_shows WHERE tv_show_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $update_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($tv_show = $stmt->fetch(PDO::FETCH_ASSOC)) {
    ?>
        <h2>Update TV Show</h2>
        <form action="process_tv_shows.php" method="post">
            <input type="hidden" name="tv_show_id" value="<?php echo $tv_show['tv_show_id']; ?>">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?php echo $tv_show['title']; ?>" required>
            <br>
            <label for="release_date">Release Date:</label>
            <input type="date" name="release_date" id="release_date" value="<?php echo $tv_show['release_date']; ?>" required>
            <br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?php echo $tv_show['description']; ?></textarea>
            <br>
            <label for="poster_image_url">Poster URL:</label>
            <input type="text" name="poster_image_url" id="poster_image_url" value="<?php echo $tv_show['poster_image_url']; ?>">
            <br>
            <label for="seasons">Seasons:</label>
            <input type="number" name="seasons" id="seasons" value="<?php echo $tv_show['seasons']; ?>" required>
            <br>
            <button type="submit" name="action" value="update_confirm">Update TV Show</button>
        </form>
    <?php
    }
    $conn = null;
    }
    ?>

    <div id="addNewTVShowForm" style="display: none;">
    <h2>Add New TV Show</h2>
    <form action="process_tv_shows.php" method="post">
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
        <label for="seasons">Seasons:</label>
        <input type="number" name="seasons" id="seasons" required>
        <br>
        <button type="submit" name="action" value="create">Add TV Show</button>
    </form>
    </div>
</body>
</html>
