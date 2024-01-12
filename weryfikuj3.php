<?php
session_start();

// Function to check and handle failed login attempts
function handleFailedAttempts($username) {
    $maxAttempts = 3; // Maximum number of consecutive failed attempts allowed
    $blockDuration = 60; // Duration to block the account in seconds (1 minute in this example)

    if (!isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username] = 0;
    }

    $_SESSION['login_attempts'][$username]++;

    if ($_SESSION['login_attempts'][$username] >= $maxAttempts) {
        // Log the failed attempt to the break_ins table with IP address
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        logFailedAttempt($username, $ipAddress);

        // Block the account for a specified duration
        $_SESSION['account_blocked'][$username] = time() + $blockDuration;
        echo "Konto zablokowane na 1 minutę po trzech błędnych próbach logowania.";
        echo'<br> <a href ="loginForm.php">Zaloguj się</a>';
        exit;
    }

}

// Clear login attempts after a successful login
function clearFailedAttempts($username) {
    if (isset($_SESSION['login_attempts'][$username])) {
        unset($_SESSION['login_attempts'][$username]);
    }
}
// Function to log failed login attempts
function logFailedAttempt($username, $ipAddress) {
    $link = mysqli_connect('mysql01.desineverbum.beep.pl', 'np_z5', 'Norbert1!@', 'np_z5');
    
    if (!$link) {
        echo "Błąd: " . mysqli_connect_errno() . " " . mysqli_connect_error();
        exit; // Handle connection error
    }

    mysqli_query($link, "SET NAMES 'utf8'");

    $username = mysqli_real_escape_string($link, $username);
    $ipAddress = mysqli_real_escape_string($link, $ipAddress);

    // Insert a record into the break_ins table
    $query = "INSERT INTO break_ins (username, ip_address) VALUES ('$username', '$ipAddress')";
    mysqli_query($link, $query);

    mysqli_close($link);
}

// Check if the account is currently blocked
function isAccountBlocked($username) {
    return isset($_SESSION['account_blocked'][$username]) && $_SESSION['account_blocked'][$username] > time();
}

$user = htmlentities($_POST['user'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $user
$pass = htmlentities($_POST['pass'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $pass
$_SESSION['who'] = $user;

if (!empty($user) && !empty($pass)) {
    // Check if the account is currently blocked
    if (isAccountBlocked($user)) {
        echo "Konto jest obecnie zablokowane. Spróbuj ponownie później.";
        echo'<br> <a href ="loginForm.php">Zaloguj się</a>';
        exit;
    }

    $link = mysqli_connect('mysql01.desineverbum.beep.pl', 'np_z5', 'Norbert1!@', 'np_z5');
    if (!$link) {
        echo "Błąd: " . mysqli_connect_errno() . " " . mysqli_connect_error();
        exit; // obsługa błędu połączenia z BD
    }
    
    mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków
    $result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'"); // wiersza, w którym login=login z formularza
    $rekord = mysqli_fetch_array($result); // wiersza z BD, struktura zmiennej jak w BD 

    if (!$rekord) {
        // Jeśli brak, to nie ma użytkownika o podanym loginie
        handleFailedAttempts($user);
        mysqli_close($link); // zamknięcie połączenia z BD
        echo "Brak użytkownika o takim loginie!<br>"; // UWAGA nie wyświetlamy takich podpowiedzi dla hakerów
        include("loginForm.php");
        exit();
    } else {
        // jeśli $rekord istnieje
        if ($rekord['password'] == $pass) {
            // czy hasło zgadza się z BD
            clearFailedAttempts($user);
            $_SESSION['loggedin'] = true;

            if ($rekord['username'] == 'admin') {
                header("Location: index2.php");
            } else {
                header("Location: index2.php");
            }
            exit();
        } else {
            // Błędne hasło
            $_SESSION['loggedin'] = false;
            handleFailedAttempts($user);
            mysqli_close($link);
            header("Location: loginForm.php");
            echo "Błąd w haśle!<br>"; // UWAGA nie wyświetlamy takich podpowiedzi dla hakerów
            exit();
        }
    }
} else {
    header("loginForm.php");
    echo "Nie masz uprawnień by przeglądać tą stronę!";
    exit();
}
?>
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
</body>
</html>
