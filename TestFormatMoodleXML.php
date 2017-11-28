<?php
  //Robert Fidytek, 04.03.2017 r.
  //Test klasy FormatMoodleXML 

  require_once("FormatMoodleXML.php");

  $x = new FormatMoodleXML();

 /*
  $x->xml_catagory("Nazwa kategorii C ");

  $x->xml_singlechoice_start("Wskaż najdłuższą rzeką w Polsce.");
  $x->xml_singlechoice("Wisła",1);
  $x->xml_singlechoice("Odra",0);
  $x->xml_singlechoice("Dunaj",0);
  $x->xml_singlechoice("Warta",0);
  $x->xml_singlechoice_stop();

  //$x->xml_catagory("Nazwa kategorii B ");

  $x->xml_multichoice_start("Ciałem fizycznym nie jest");
  $x->xml_multichoice("spadający kamień",0);
  $x->xml_multichoice("człowiek jadący na rolkach",0);
  $x->xml_multichoice("błyskawica",1,1);
  $x->xml_multichoice("odważnik na wadze szalkowej",0);
  $x->xml_multichoice_stop();

  $x->xml_multichoice_start("Wskaż substancje, które nie zawierają wody.");
  $x->xml_multichoice("Olej",1,2);
  $x->xml_multichoice("Mleko",0);
  $x->xml_multichoice("Benzyna",1,2);
  $x->xml_multichoice("Sok pomarańczowy",0);
  $x->xml_multichoice_stop();

  $x->xml_truefalse("Do regulowania jasności świecenia żarówki można wykorzystać prawo Ohma.",1);

  $x->xml_truefalse("Mercedes to marka piwa.",0);

  $x->xml_shortanswer_start("Jakie miasto jest stolicą Polski?");
  $x->xml_shortanswer("Warszawa",100);
  $x->xml_shortanswer("Wwa",80);
  $x->xml_shortanswer("W-wa",80);
  $x->xml_shortanswer("W",10);
  $x->xml_shortanswer_stop();

  $x->xml_matching_start("Dopasuj liczebniki główne.");
  $x->xml_matching("thirteen","13");
  $x->xml_matching("fourteen","14");
  $x->xml_matching("fifteen","15");
  $x->xml_matching("sixteen","16");
  $x->xml_matching("seventeen","17");
  $x->xml_matching("eighteen","18");
  $x->xml_matching("nineteen","19");
  $x->xml_matching_stop();
*/

  $x->xml_catagory("Cloze");

  $x->xml_cloze_start();

  $x->xml_cloze_text("Stolicą Polski jest ");

  $x->xml_cloze_shortanswer_start(); 
  $x->xml_cloze_shortanswer_answer(100,"Warszawa");
  $x->xml_cloze_shortanswer_answer(80,"Wwa");
  $x->xml_cloze_shortanswer_answer(20,"W");
  $x->xml_cloze_shortanswer_stop(); 

  $x->xml_cloze_text(". 2+2=");

  $x->xml_cloze_multichoice_start(); 
  $x->xml_cloze_multichoice_answer(100,"4");
  $x->xml_cloze_multichoice_answer(0,"5"); 
  $x->xml_cloze_multichoice_answer(50,"IV"); 
  $x->xml_cloze_multichoice_stop(); 
  $x->xml_cloze_text(". ");



  $x->xml_cloze_text("Długość rzeki Rega wynosi ");

  $x->xml_cloze_numerical_start(); 
  $x->xml_cloze_numerical_answer(100,170,3);
  $x->xml_cloze_numerical_answer(50,170,13); 

  $x->xml_cloze_numerical_stop(); 
  $x->xml_cloze_text(".");

  $x->xml_cloze_stop(); 

  echo "<pre>".htmlspecialchars($x->xml_quiz())."</pre>";

 ?>