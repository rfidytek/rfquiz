<?php

$zapis_do_pliku=0;

if( isset($_POST['pobierzPlik2XML']) || isset($_POST['sprawdzRFQuiz']) ) {
  if(!empty($_FILES['plik']['type'])) {
 	 if(isset($_POST['pobierzPlik2XML'])) $zapis_do_pliku=1;
    //echo "Pobrano dane z pliku tekstowego ". $_FILES['plik']['tmp_name']."<br>";
    //wczytaj plik tekstowy
    if (file_exists($_FILES['plik']['tmp_name'])) {
      //echo 'Określam dane plik<br>';
      $dane=file($_FILES['plik']['tmp_name']);
      //zmień kodowanie zawartości pliku na utf-8
      for($i=0;$i<count($dane);$i++) {
        $charset=mb_detect_encoding($dane[$i]);
        $dane[$i]=iconv("$charset", "UTF-8", $dane[$i]);
        //echo $dane[$i]."<br>";
      }//for    	
    }//if-file_exists
    else {
      $zapis_do_pliku=0;
      echo "Plik ". $_FILES['plik']['tmp_name']." nie istnieje.<br>";
    }

  }//!empty
} 
if(isset($_POST['pobierzTekst2XML'])  || isset($_POST['sprawdzRFQuiz'])  ) {
  if(!empty($_POST['dane'])){
   	if(isset($_POST['pobierzTekst2XML'])) $zapis_do_pliku=1;
    //echo "Otrzymano dane z pola edycyjnego:<pre>".$_POST['dane']."</pre>";
    //przygotuj dane do konwersji
    $dane = explode("\n", $_POST['dane']);
  }//!empty
}

if($zapis_do_pliku==1) {
  require_once("Txt2XML.php");
  $x = new Txt2XML();
  //konwertuj na format Moodle XML
  $x->txt2xml($dane); 

  if($x->error==0) {
  	$nazwapliku = "MoodleXML_".date('Y-m-d_G_i_s').".xml";
    header("Content-disposition: attachment; filename=".$nazwapliku);
    header("Content-Type: application/force-download");
    //header('Content-type: text/plain');
    header("Content-Transfer-Encoding: binary");
	echo $x->xml->xml_quiz();
	echo "\n\n\n<!-- Kopia pytań w formacie RFQuiz (".date('Y-m-d G:i:s')."):\n\n\n";
    foreach($dane as $d) { $d=str_replace("--"," - -",$d); echo rtrim($d)."\n";} //znaki "--" zamieniamy na " - -"
	echo "\n -->\n";
	exit(1);  
  } 
}

if( isset($_POST['sprawdzRFQuiz']) ) {
  //echo 'Tworze $x<br>';
  require_once("Txt2XML.php");
  $x = new Txt2XML("html");
  //pytania w formularzu
  $x->txt2xml($dane);
} 

?>
<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8" />
	<title>Generator Moodle XML</title>
</head>
<body>
  

<?php if( isset($_POST['sprawdzRFQuiz']) && isset($x->error) && $x->error==0 ) { ?>
  <h1>Krok 2: Sprawdź pytania i konwertuj na format Moodle XML</h1>
  <form action="index.php" method="post" ENCTYPE="multipart/form-data">  	
    <p>  
      <?php echo '<textarea id="dane" name="dane" cols="80" rows="7" readonly="readonly" hidden="hidden">';
        $ile_el=count($dane);
        for($i=0;$i<=$ile_el-1;$i++) echo $dane[$i];   
        echo '</textarea>'."\n"; 
      ?>
      
      <button type="submit" name="pobierzTekst2XML" value="pobierzTekst2XML">Konwertuj na format Moodle XML</button>
      &nbsp;
      <button type="submit" name="ponow" value="ponow">Wczytaj pytania ponownie</button>    
    </p>
  </form>
<?php } else if(isset($_POST['sprawdzRFQuiz'])) { ?>
  <h1>Krok 1: Popraw błędy i spróbuj ponownie</h1>
  <form action="index.php" method="post" ENCTYPE="multipart/form-data">    
    <p>
      <button type="submit" name="error" value="Spróbuj ponownie">Wczytaj pytania ponownie</button>
    </p>
  </form>
<?php } else { ?>
  <h1>Krok 1: Wczytaj pytania w formacie RFQuiz</h1>
  <form action="index.php" method="post" ENCTYPE="multipart/form-data">    
    <p>Wskaż plik *.txt do konwersji:<br>
      <input type="file" name="plik" required>
    </p>
    <p>
      <button type="submit" name="sprawdzRFQuiz" value="sprawdzRFQuiz">Sprawdź zgodność z formatem RFQuiz</button>
      <!-- <br>
      <button type="submit" name="pobierzPlik2XML" value="pobierzPlik2XML">Konwertuj na format Moodle XML</button> -->
    </p>
  </form>
  
  <form action="index.php" method="post" ENCTYPE="multipart/form-data">    
    <p><br><br>Przykładowy test zapisany w formacie RFQuiz:<br>  
      <textarea id="dane" name="dane" cols="80" rows="20" value="" placeholder="Wpisz treści pytań." required>
Różne

Wskaż najdłuższą rzekę w Polsce.
1|Wisła
0|Odra
0|Dunaj
0|Warta

Ciałem fizycznym nie jest
0|spadający kamień
0|człowiek jadący na rolkach
1|błyskawica
0|odważnik na wadze szalkowej

Wskaż substancje, które nie zawierają wody.
1|Olej
0|Mleko
1|Benzyna
0|Sok pomarańczowy

1|Do regulowania jasności świecenia żarówki można wykorzystać prawo Ohma.

Jakie miasto jest stolicą Polski?
100%|Warszawa
80%|Wwa
80%|W-wa
10%|W

Dopasuj liczebniki główne.
thirteen|13
fourteen|14
fifteen|15
sixteen|16
seventeen|17
eighteen|18
nineteen|19

Książkę pt. "Krzyżacy" napisał |Henryk Sienkiewicz|Adam Mickiewicz|Juliusz Słowacki|Jan Długosz|Jan Matejko|. Jest to powieść |historyczna|przygodowa|fantasy|podróżnicza|epistolarna|. 
Oblicz: 
a) 3+3*3=|12|
b) 12*12=|144|
c) 3-3-3=|-3|
d) |2|-7=-5
e) jeden+pięć= |sześć|pięć|osiem|siedem| 


      </textarea>
    </p> 
    <p>
      <button type="submit" name="sprawdzRFQuiz" value="sprawdzRFQuiz">Sprawdź zgodność z formatem RFQuiz</button>
      <!-- <br>
      <button type="submit" name="pobierzTekst2XML" value="pobierzTekst2XML">Konwertuj na format Moodle XML</button> -->
    </p>
  </form>
  
<?php } ?>

<?php

  if(isset($x->error) && $x->error>0) {
    //echo "Są błędy";
    foreach($x->errors as $err) echo $err;
    //exit(1);
  } else if(isset($_POST['sprawdzRFQuiz'])) {
    //echo 'Wypisz $x<br>';
    if(isset($x->error) && $x->error==0) { echo $x->xml->xml_quiz(); }
    else { echo "<p>Musisz najpierw wybrać plik!</p>"; }
  }

  if($zapis_do_pliku==1) echo "<pre>".htmlspecialchars($x->xml->xml_quiz())."</pre>";

  //phpinfo();
?>
  <p><br><br><br></p>
</body>
</html>
