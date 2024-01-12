<script type="text/javascript">
    document.cookie = "rozdzielczosc = " + screen.width + "x" + screen.height;
    document.cookie = "okno = " + window.innerWidth + "x" + window.innerHeight;
    document.cookie = "kolory = " + screen.colorDepth;
    document.cookie = "ciastka= " + navigator.cookieEnabled;
    document.cookie = "java= " + navigator.javaEnabled();
    document.cookie = "jezyk= " + navigator.language;
</script>
<?php session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<BODY>
Logowanie
<form method="post" action="weryfikuj3.php">
 Login:<input type="text" name="user" maxlength="20" size="20"><br>
 Hasło:<input type="password" name="pass" maxlength="20" size="20"><br>
 <input type="submit" value="Send"/>
</form>
<a href ="rejestruj.php">Rejestracja</a>

</BODY>
</HTML>