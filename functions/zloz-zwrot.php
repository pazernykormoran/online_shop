<?php 
include "../includes/czy-zalogowany.php";

require_once "../connect.php";
$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

$id_zam="";
$id_prod_bez_nr="";
$nr_ser="";
$nazwa_prod="";
$opis="";
$status="";

$message="";

if(isset($_GET['id_zamowienia'])){$id_zam=$_GET['id_zamowienia'];}
if(isset($_GET['id_prod_bez_nr_ser'])){$id_prod_bez_nr=$_GET['id_prod_bez_nr_ser'];}
if(isset($_GET['nr_ser'])){$nr_ser=$_GET['nr_ser'];}
if(isset($_GET['nazwa_prod'])){$nazwa_prod=$_GET['nazwa_prod'];}
if(isset($_GET['status'])){$status=$_GET['status'];}
if(isset($_POST['opis'])){$opis=$_POST['opis'];}

$zapytanie= "";

  if($id_zam!=""&&$opis!=""&&$id_prod_bez_nr!=""&&$status=="zrealizowane"){
  	if($nr_ser==""){
	    $zapytanie = "INSERT INTO `zwroty` (`id`, `id_zamowienia`, `id_produktu_bez_seryjnego`, `powod`, `status`)
	      VALUES (NULL, $id_zam, $id_prod_bez_nr, '$opis', 'zgloszono')";
  	}else{
  		$zapytanie = "INSERT INTO `zwroty` (`id`, `id_zamowienia`,  `nr_seryjny`, `powod`, `status`)
	      VALUES (NULL, $id_zam, $nr_ser, '$opis', 'zgloszono')";
  	}

  }

  if ($polaczenie->query($zapytanie) === TRUE) {
    echo "New record created successfully". "<br>";
    $message="info=Pomyślnie złożono zwrot";
  } else {
    $message="blad=Nie udało się zlozyc zwrotu";

  }
  header('Location: ../sklep.php?'.$message);
 ?>