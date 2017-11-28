<?php

	require_once("Txt2XML.php");

  $x = new Txt2XML("wejscie2.txt");
  $x->txt2xml();

  echo "<pre>".htmlspecialchars($x->xml->xml_quiz())."</pre>";

?>