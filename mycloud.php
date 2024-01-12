<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: loginForm.php');
    exit();
}

$username = $_SESSION['who'];
$userdir = "folders/" . $username;

if (isset($_GET['dir'])) {
    $userdir .= '/' . $_GET['dir'];
}

echo "Aktualna ścieżka: " . $userdir . "<br>";

if ($userdir != "folders/" . $username) {
    echo "<a href=\"?folders=" . urlencode(dirname($_GET['dir'])) . "\">Poziom wyżej";
}

if (is_dir($userdir)) {
    $files = scandir($userdir);

    echo "<ul>";
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            if (is_dir($userdir . '/' . $file)) {
                echo "<li><span><a href=\"?dir=" . urlencode($file) . "\">" . $file . "</a></span><span>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='delete_dir' value='" . urlencode($file) . "'>";
                echo '<button>Usuń</button>';
                echo "</form></span></li>";
            } else {
                echo "<li><span><a href='" . $userdir . "/" . $file . "' download>" . $file . "</a></span><span>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='delete_file' value='" . urlencode($file) . "'>";
                echo '<button>Usuń</button>';
                echo "</form></span>";
                if (pathinfo($file, PATHINFO_EXTENSION) == "mp3") {
                    echo "<audio controls><source src='" . $userdir . "/" . $file . "' type='audio/mpeg'>Your browser does not support the audio element.</audio>";
                }
                if (pathinfo($file, PATHINFO_EXTENSION) == "mp4") {
                    echo "<video width='320' height='240' controls><source src='" . $userdir . "/" . $file . "' type='video/mp4'>Your browser does not support the video element.</video>";
                }
                if (pathinfo($file, PATHINFO_EXTENSION) == "jpg" || pathinfo($file, PATHINFO_EXTENSION) == "jpeg") {
                    echo "<img src='" . $userdir . "/" . $file . "' alt='" . $file . "' width='320' height='240'>";
                }
                if (pathinfo($file, PATHINFO_EXTENSION) == "pdf") {
                    echo "<i class='bi bi-file-pdf'></i>";
                }
                echo "</li>";
            }
        }
    }
    echo "</ul>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newdir'])) {
    $newdir = $userdir . "/" . $_POST['newdir'];

    if (!file_exists($newdir)) {
        mkdir($newdir, 0777, true);
        echo "<p>Utworzono nowy katalog: " . $_POST['newdir'] . "</p>";
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        echo "<p>Katalog o nazwie " . $_POST['newdir'] . " już istnieje.</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $encodedFileName = $_POST['delete_file'];
    $decodedFileName = urldecode($encodedFileName);
    $file = $userdir . '/' . $decodedFileName;

    if (file_exists($file)) {
        unlink($file);
        echo "<p>Usunięto plik: " . $decodedFileName . "</p>";
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_dir'])) {
    $dir = $userdir . '/' . $_POST['delete_dir'];
    if (is_dir($dir)) {
        rmdir($dir);
        echo "<p>Usunięto katalog: " . $_POST['delete_dir'] . "</p>";
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

?>

<?php if ($userdir == "folders/" . $username): ?>
    <form method="post" action="">
        <label for="newdir">Nowy podkatalog:</label>
        <input type="text" id="newdir" name="newdir" required>
        <input type="submit" value="Utwórz">
    </form>
<?php endif; ?>

<br>

<form action="" method="post" enctype="multipart/form-data">
    Select file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload" name="submit">
    <?php
    if (isset($_POST["submit"]) && isset($_FILES["fileToUpload"])) {
        $target_file = $userdir . "/" . basename($_FILES["fileToUpload"]["name"]);

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " uploaded.";
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            echo "Error uploading file.";
        }
    }
    ?>
</form>

<?php echo '<br><a href ="logout.php">Wyloguj się</a><br/> <br>'; ?>
</html>
