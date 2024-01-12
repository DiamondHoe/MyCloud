<?php // włączenie typowania zmiennych w PHP >=7
session_start();
$u= $_SESSION['who'];
    $rozdzielczosc= $_COOKIE['rozdzielczosc'];
    $okno= $_COOKIE['okno'];
    $kolory= $_COOKIE['kolory'];
    $ciastka= $_COOKIE['ciastka'];
    $java= $_COOKIE['java'];
    $jezyk= $_COOKIE['jezyk'];
if ($_SESSION ['loggedin'] == false)
{
header("Location: loginForm.php");
exit();
}
if ($_SESSION ['loggedin'] == true){
$ipaddress = $_SERVER["REMOTE_ADDR"];
function ip_details($ip) {
$json = file_get_contents ("http://ipinfo.io/{$ip}/geo");
$details = json_decode ($json);
return $details;
}
$arr_browsers = ["Opera", "Edg", "Chrome", "Safari", "Firefox", "MSIE", "Trident"];
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $user_browser = '';
    foreach ($arr_browsers as $browser) {
        if (strpos($agent, $browser) !== false) {
            $user_browser = $browser;
            break;
        }   
    }
    switch ($user_browser) {
        case 'MSIE':
            $user_browser = 'Internet Explorer';
            break;
        case 'Trident':
            $user_browser = 'Internet Explorer';
            break;
        case 'Edg':
            $user_browser = 'Microsoft Edge';
            break;
    }
$details = ip_details($ipaddress);
$lokalizacja = $details -> loc;
$link = mysqli_connect('mysql01.desineverbum.beep.pl', 'np_z5', 'Norbert1!@', 'np_z5');
 if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD
 mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków
 $sql = "INSERT INTO goscieportalu(ipaddress, wspolrzedne, uzytkownik, ekran, okno, java, jezyk, ciasteczka, kolory, przegladarka) VALUES ('$ipaddress', '$lokalizacja', '$u', '$rozdzielczosc', '$okno', '$java', '$jezyk','$ciastka', '$kolory','$user_browser')";
 if (mysqli_query($link, $sql)) {
$_SESSION ['zalogowany'] = true;
}}?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>

<a href ="mycloud.php">Udaj się do MyCloud</a><br />
<div id=OstatnieBledneLogowanie></div>
<?php
 $link = mysqli_connect('mysql01.desineverbum.beep.pl', 'np_z5', 'Norbert1!@', 'np_z5');
 $recentBreakIn = "SELECT * FROM break_ins WHERE username = '$u' ORDER BY timestamp DESC LIMIT 1";
 $result = mysqli_query($link, $recentBreakIn);
 
 if ($result) {
     $row = mysqli_fetch_assoc($result);
 
     // Access the values from the most recent record
     $timestamp = $row['timestamp'];
     $ipAddress = $row['ip_address'];
 
     // Format the timestamp as needed
     $formattedTimestamp = date('Y-m-d H:i:s', strtotime($timestamp));
 
     // Echo the information with red font
     echo "<p style='color: red;'>Ostatnie błędne logowanie o: $formattedTimestamp z adresu IP: $ipAddress</p>";
 } else {
     // Handle query error
     echo "Error: " . mysqli_error($link);
 }
 
 mysqli_free_result($result);
?>
<div id="Tabela"></div>
<table border="1">
  <caption>Dane twoich logowań</caption>
  <thead>
    <tr>
     <th>Data</th>
     <th>Adres IP</th>
     <th>Przeglądarka</th>
     <th>Ekran</th>
     <th>Okno</th>
     <th>Kolory</th>
     <th>Ciasteczka</th>
     <th>Java</th>
     <th>Język</th>
     <th>Współrzędne</th>
    </tr>
  </thead>
  <tbody>
<?php
 $link = mysqli_connect('mysql01.desineverbum.beep.pl', 'np_z5', 'Norbert1!@', 'np_z5');
 if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD
 mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków
 $sql_get = "SELECT * FROM goscieportalu WHERE uzytkownik = '$u'"; 
 $result = $link->query($sql_get);
if (!$result){
die("Niepoprawne query" . $connection->error);
}
while ($row = $result->fetch_assoc()){
 echo "<tr>
      <td>".$row["datetime"]."</td>
      <td>".$row["ipaddress"]."</td>
      <td>".$row["przegladarka"]."</td>
      <td>".$row["ekran"]."</td>
      <td>".$row["okno"]."</td>
      <td>".$row["kolory"]."</td>     
      <td>".$row["ciasteczka"]."</td>
      <td>".$row["java"]."</td>
      <td>".$row["jezyk"]."</td>";
      echo '<td><a href="https://www.google.pl/maps/place/'.$row["wspolrzedne"].'">Link do współrzędnych</a></td>'; 
	  echo "</td>";
}
echo "</table>";
?>
</tbody>
</table>
<?php 
echo "<br>";
echo "Jesteś zalogowany jako: <b>$u</b><br>";
echo '<a href ="logout.php">Wyloguj się</a><br/> <br>'; ?>
</BODY>
</HTML>