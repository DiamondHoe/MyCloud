<?php session_start();?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>
<?php
 $user = htmlentities ($_POST['user'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $user
 $pass = htmlentities ($_POST['pass'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $pass
 $pass2 = htmlentities ($_POST['pass2'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $pass2

 $link = mysqli_connect('mysql01.desineverbum.beep.pl', 'np_z5', 'Norbert1!@', 'np_z5');
 if ($pass != $pass2){
     echo "Wprowadzone hasła różnią się od siebie!<br>";
     include("rejestruj.php");
     mysqli_close($link);
 }
 else {
 if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD
 mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków
 $result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'"); // wiersza, w którym login=login z formularza
 $rekord = mysqli_fetch_array($result); // wiersza z BD, struktura zmiennej jak w BD 
 if($rekord) 
 {
 mysqli_close($link); // zamknięcie połączenia z BD
 echo "Jest już taki login!";
 }
 else
 {
 $sql = "INSERT INTO users (username, password) VALUES ('$user', '$pass')";

 if (mysqli_query($link, $sql)) {
      echo "Stworzono nowego użytkownika!";
if (!is_dir("folders/".$user)) {
 mkdir("folders/".$user,0777);
}

echo '<br><a href ="loginForm.php">Zaloguj się</a><br />';

} else {
      echo "Error: " . $sql . "<br>" . mysqli_error($link);
}
mysqli_close($link);
}
}
?>
</BODY>
</HTML>