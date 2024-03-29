<?php 
  function utworz_string_z_produktami($produkty){
    $string="";
    foreach ($produkty as $id){
      if($string != "") { $string = $string . ", "; }
        $string = $string . $id;
      }

    return $string;
  }


  //----------------------------------------------------

  //zmienne
  $produkty=array();
  $produkty_z_numerami_seryjnymi_ilosc=array();
  $produkty_do_wyswietlenia=array();
  
  if ($polaczenie->connect_errno!=0)
  {
    echo "Error: ".$polaczenie->connect_errno;
  }
  else{

    $zapytanie="";

    $string_pomocniczy="";

      $zapytanie="SELECT id, nazwa, cena, id_kategorii, ilosc_w_magazynie , nr_seryjny, id_zamowienia
      FROM produkty
      LEFT JOIN pr_bez_nr_seryjnego on produkty.id = pr_bez_nr_seryjnego.id_produktu 
      LEFT JOIN pr_nr_seryjny on produkty.id = pr_nr_seryjny.id_produktu
      WHERE pr_nr_seryjny.id_zamowienia IS NULL;";
      if ($rezultat = @$polaczenie->query($zapytanie)){
        if ($rezultat->num_rows > 0) {
            while($row = $rezultat->fetch_assoc()) {
              $count=0;
              if(isset($_SESSION['chart'])){
                $count = count(array_keys($_SESSION['chart'], $row['id']));
              }
              
              array_push($produkty, new ProduktKoszyk(new Produkt($row['id'],$row['cena'],$row['nazwa'],$row['id_kategorii']),
                $count,$row['ilosc_w_magazynie'],$row['nr_seryjny'],$row['id_zamowienia']));
            }

        } else {
        }
      }else{
        echo "Blad przy wykonywaniu zapytania";
      }


  }

  foreach ($produkty as $key => $value) {
    //echo " selloo". $value->numer_seryjny;
    if(!empty($value->getNumerSeryjny())){

      if(isset($produkty_z_numerami_seryjnymi_ilosc[$value->produkt->getId()])){
        $produkty_z_numerami_seryjnymi_ilosc[$value->produkt->getId()]++;
        continue;
      }
      else{
        $produkty_z_numerami_seryjnymi_ilosc[$value->produkt->getId()]=1;
        array_push($produkty_do_wyswietlenia, $value);
      }
    }else{
      array_push($produkty_do_wyswietlenia, $value);
    }
  }

 ?>