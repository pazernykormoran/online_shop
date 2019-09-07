<?php
	include "includes/czy-zalogowany.php";

	require_once "classes-and-functions.php";
//----------------------------------------------- kategorie --------------------------------------------------
//-----------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------

	$kategorie = array();
	$tempPodkategorie = array();

	require_once "connect.php";
	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if($rezultat = @$polaczenie->query("SELECT * FROM kategorie"))
	{

		if ($rezultat->num_rows > 0) {
			//echo " ------------------KATEGORIE --------------- </br>";
		    while($row = $rezultat->fetch_assoc()) {
		    	if($row["id_ojca"] == 1){
		    		$kategorie[$row["id"]] = new Kategoria($row["id"],$row["nazwa"],$row["id_ojca"]);
		    	}
		    	else{
		    		array_push($tempPodkategorie, new Kategoria($row["id"],$row["nazwa"],$row["id_ojca"]));
		    	}
		    }

		    foreach ($tempPodkategorie as $tempPodkategoria) {
		    	array_push($kategorie[$tempPodkategoria->getIdOjca()]->podkategorie, $tempPodkategoria);
		    }


		    // foreach ($kategorie as $kat){
		    // 	echo $kat->getNazwa() . " ---- id kategorii: " . $kat->getId();
		    // 	echo "</br>";
		    // 	foreach ($kat->podkategorie as $podkat) {
		    // 		echo "-----".$podkat->getNazwa() . " ---- id kategorii: " . $podkat->getId();
		    // 		echo "</br>";
		    // 	}
		    // }

		} else {
		    echo "0 results";
		}
		
	}
	else{
		echo "Błąd przy wykonywaniu zapytania";
	}

	echo "</br>";

//----------------------------------------------- produkty --------------------------------------------------
//-----------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------


	function czy_to_podkategoria($kategorie, $id){
		foreach ($kategorie as $kat){
			if($kat->getId() == $id){
				return false;
			}
	    }
	    return true;
	}

	function utworz_string_z_kategoriami($kategorie, $id){
		$string="";
		foreach ($kategorie as $kat){
	    	if($kat->getId()==$id){
	    		foreach ($kat->podkategorie as $podkat) {
	    			if($string != "") { $string = $string . ", "; }
		    		$string = $string . $podkat->getId();
		    	}
	    	}
	    }
	    echo "string pomocniczy: " . $string . "</br>";

		return $string;
	}

	//echo " ------------------PRODUKTY --------------- </br>";

	//przygotuj zapytanie na podstawie parametrów z geta
	$produkty = array();
	$zapytanie = "SELECT * FROM produkty;";
	$get_id_kategorii;
	if (isset($_GET['kategoria']) && $_GET['kategoria'] != 1)
	{
		$get_id_kategorii=$_GET["kategoria"];
		echo " szukam produktów dla kategorii: ". $get_id_kategorii . "</br>";
		if(czy_to_podkategoria($kategorie, $get_id_kategorii)){
			$zapytanie="SELECT * FROM produkty WHERE id_kategorii=". $get_id_kategorii .";";
		}
		else{
			$string_pomocniczy=utworz_string_z_kategoriami($kategorie, $get_id_kategorii);
			$zapytanie="SELECT * FROM produkty WHERE id_kategorii IN(" . $string_pomocniczy . ");";
		}
	}
	
	echo "zapytanie: " . $zapytanie . "</br></br>";

	if($rezultat = @$polaczenie->query($zapytanie))
	{
		if ($rezultat->num_rows > 0) {
		    while($row = $rezultat->fetch_assoc()) {
		    	array_push($produkty, new Produkt($row["id"],$row["cena"],$row["nazwa"],$row["id_kategorii"]));
		    	//echo "NAZWA PRODUKTU: ". $row["nazwa"] . " ID KATEGORII: " . $row["id_kategorii"] . "</br>";
		    }

		} else {
		    echo "0 results";
		}

	}else{
		echo "Błąd przy wykonywaniu zapytania";
	}

//
	// foreach ($produkty as $prod){
 //    	echo $prod->getNazwa() . " ---- id kategorii: " . $prod->getId();
 //    	echo "</br>";
 //    }
	

// SORTOWANIE PRODUKTOW-------------------
$sort="none";
if (isset($_GET['sort']))
{
	$sort=$_GET['sort'];
}

if($sort == "nazwa"){
	function cmp($a, $b)
	{
	    return strcmp($a->getNazwa(), $b->getNazwa());
	}

	usort($produkty, "cmp");
}else if($sort == "cena"){
	function cmp($a, $b)
	{
	    return strcmp($a->getCena(), $b->getCena());
	}

	usort($produkty, "cmp");
}



echo "<p>Witaj ".$_SESSION['user'].'! [ <a href="logout.php">Wyloguj się!</a> ]</p>';

	
?>



<!DOCTYPE HTML>
<html lang="pl">
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Sklep internetowy</title>
</head>

<body>
<?php
include "header.php"
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 shadow-sm">
  <div class="container">
    <a href="#" class="navbar-brand font-weight-bold">Wybierz filtry</a>
    <button type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler">
              <span class="navbar-toggler-icon"></span>
          </button>


    <div id="navbarContent" class="collapse navbar-collapse">
      <ul class="navbar-nav mr-auto">
        <!-- Level one dropdown -->
        <li class="nav-item dropdown">
          <a id="dropdownMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Kategorie</a>
          <ul aria-labelledby="dropdownMenu1" class="dropdown-menu border-0 shadow">
          <?php foreach ($kategorie as $kat){ ?>

		  	<li class="dropdown-submenu">
              	<a id="dropdownMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><?php echo $kat->getNazwa(); ?></a>
			  	<ul aria-labelledby="dropdownMenu2" class="dropdown-menu border-0 shadow">  
			    	<li><a href=" <?php echo "?kategoria=" . $kat->getId(); ?> " class="dropdown-item">Wszystkie</a></li>  

          	<?php foreach ($kat->podkategorie as $podkat) { ?>

					<div class="dropdown-divider"></div>             
                	<li><a href=" <?php echo "?kategoria=" . $podkat->getId(); ?> " class="dropdown-item"><?php echo $podkat->getNazwa(); ?></a></li>

		  	<?php } ?>

              	</ul>
            </li>

		  <?php } ?>

         
          </ul>
        </li>
        <!-- End Level one -->
<li>

</li>

        <li  class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Sortuj po
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

  	<?php 
  	if (isset($get_id_kategorii))
	{
		$url1="?kategoria=".$get_id_kategorii."&sort=cena";
	}else{
		$url1="?sort=cena";
	}
	if (isset($get_id_kategorii))
	{
		$url2="?kategoria=".$get_id_kategorii."&sort=nazwa";
	}else{
		$url2="?sort=nazwa";
	}
  	?>
    <a class="dropdown-item" href=" <?php echo $url1 ?> ">Cenie</a>
    <a class="dropdown-item" href=" <?php echo $url2 ?> ">Nazwie</a>
 
</div></li>


     
      </ul>
    </div>
  </div>
</nav>



<?php
include "info-blad.php"
?>



<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Nazwa</th>
      <th scope="col">Cena</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>

<?php 
$kat_pom="";
if(isset($_GET['kategoria'])) {
	$kat_pom="&kategoria=".$_GET['kategoria']; 
} 
?>

	<?php  foreach ($produkty as $prod){ ?>
	<tr>
      <th scope="row"> <?php echo $prod->getId(); ?></th>
      <td><?php echo $prod->getNazwa(); ?></td>
      <td><?php echo $prod->getCena(); ?></td>

      <td><button   type="button" class="btn btn-secondary"> <a href=" <?php echo "functions/dodaj-do-koszyka.php?id_produktu=". $prod->getId()."&cena=". $prod->getCena(). $kat_pom; ?> ">Dodaj do koszyka</a> </button></td>
    </tr>
    <?php } ?>


  </tbody>
</table>
	


<!-- End -->


</body>


</html>

<script>
$(function() {
  // ------------------------------------------------------- //
  // Multi Level dropdowns
  // ------------------------------------------------------ //
  $("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
    event.preventDefault();
    event.stopPropagation();

    $(this).siblings().toggleClass("show");


    if (!$(this).next().hasClass('show')) {
      $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
    }
    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
      $('.dropdown-submenu .show').removeClass("show");
    });

  });
});
</script>