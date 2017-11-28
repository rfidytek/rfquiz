<?php
require_once("konwertery/Txt2XML.php");

if (isset($_FILES['plik']['type'])) {

	echo 'Wskazano plik: '.$_FILES['plik']['name'];
//	phpinfo();


	$x = new TxT2XML();
	$dane=file_get_contents($_FILES['plik']['tmp_name']);
	$x->txt2xml($dane);

   

  echo "<pre>".htmlspecialchars($x->xml->xml_quiz())."</pre>";
}

if(isset($_POST['pobierz'])) {
	//przypadki pobierz1 i pobierz2 powiązane są z polem textarea
	//przypadki pobierz3 i pobierz4 powiązane są z załadowanym plikiem
	switch($_POST['pobierz']) {
		case "pobierz1":
			$x = $_POST['tresc'];
			$x->txt2xml(); //tutaj oś nie działa
			pobierzxml($x);
			break;
		case "pobierz2":
			$x = $_POST['tresc'];
			//$x->txt2html();
			//instrukcje pobierania
			//-----------
			break;
		case "pobierz3":
			pobierzxml($x);
			break;
		case "pobierz4":
			//instrukcje pobierania
			break;			
	}
}

function pobierzxml($x) {
	header('Content-Disposition: attachment; filename="quiz.xml"');
	header('Content-Length: ' . strlen($x));
	header('Connection: close');
	echo $x;
}

function pobierzhtml($x) {
	header('Content-Disposition: attachment; filename="quiz.html"');
	header('Content-Length: ' . strlen($x));
	header('Connection: close');
	echo $x;
}
?>