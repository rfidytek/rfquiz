<?php
  //Robert Fidytek, aktualizacja 30.04.2017 r.
  //Klasa konwertuje pytanie z formatu RFQUIZ na format Moodle XML 

class Txt2XML
{ 
  public $xml;     //obiekt generujący kod xml
  public $error;   //ilość błędów
  public $errors;  //informacje o błędach

  function __construct($mode="xml")
  {
  	if ($mode=="html") {
      require_once("FormatDisabledHTML.php");
      $this->xml = new FormatDisabledHTML();
    } else { //w innych przypadkach mamy tryb xml
      require_once("FormatMoodleXML.php");
      $this->xml = new FormatMoodleXML();     
    }
    $this->error=0;
    $this->errors=array();
  }

  function __destructor()
  {

  }

  private function txt2xml_wrong_format($info,$diagnostyka,$pytanie) 
  {
    $this->error++;
  	$this->errors[]="<span style=\"color:red\">".$info."</span>"."<br>";
  	$this->errors[]=$diagnostyka."<br>";
  	$this->errors[]="<b>".$pytanie."</b><br>";
  } 

  //główna metoda, zwraca liczbę błędów lub generuje plik XML
  public function txt2xml($dane) 
  {
    $nr_linii=0;
  	$nr_pustej=0;

    foreach($dane as $w)
    { 
      $w=rtrim($w); //usuwanie "\n"
      //echo "$w<br>";
      $nr_linii++;
      //zamiana problematycznych znaków
      $w=str_replace("&","&amp;",$w);
      $w=str_replace("<","&lt;",$w);
      $w=str_replace(">","&gt;",$w);
  		//zamieniam wszystkie wystąpienia "\|" na kody ASCII "|" (&#124;)
      $w=str_replace("\|","&#124;",$w);
      $w=str_replace("\\","&#92;",$w); ///////////////
      $w=str_replace("\"","&#34;",$w); ///////////////

      //zamiana bbcode
      $w=str_replace("[kod]","<pre><b>",$w);
      $w=str_replace("[/kod]","</b></pre>",$w);

  		//określam typy linii:
  		//"0" lub "1"-linia rozpoczynająca się od 0 lub 1
  		//"%"-linia odpowiedzi do pytania krotka odpowiedź (zawiera "%|")
  		//"|"-linia różna od powyższych zawierająca "|", zakładam, że będzie to dopasuj odpowiedź
  		//"t"-linia z tekstem (treść pytania, kolejna część odpowiedzi)
      //"c"-pytanie typu cloze
  		if(!preg_match('/[a-zA-ZąćęłńóśżźĘŁŃÓŚŻŹ0-9]+/', $w)) 
  		{
  			//echo '(Pusta linia): ';
  			$nr_pustej++;
  		}
      else if(substr_count($w,"|")>1)
      { 
        //echo "(Cloze): $w<br>";
        $typ[]="c";
        $pyt[]=$w;
      }
  		else if(preg_match('/^[0-1][|]/', $w)) 
  		{
  			//echo '(Odpowiedź): ';
  			$typ[]=$w[0];
  			$pyt[]=$w;
  		}
  		else if(preg_match('/[%][|]/', $w)) 
  		{
  			//echo '(Krótka): ';
  			$typ[]="%";
  			$pyt[]=$w;
  		}
  		else if(preg_match('/[|]/', $w)) 
		{
  			//echo '(Dopasuj): ';
  			$typ[]="|";
  			$pyt[]=$w;
  		}
  		else 
  		{
  			//echo '(Tekst): ';
  			$typ[]="t";
  			$pyt[]=$w;
		}
  	
  		//pojawiła się pusta linia lub koniec tablicy - trzeba określić typ wczytanego pytania
  		if($nr_pustej>0 || $nr_linii==count($dane))
  		{
 			
			if(isset($typ)&&count($typ)>0)  //gdy zmiennej $typ nie ma, to znaczy, że wczytaliśmy tylko pustą linię
			{
				//echo "typ: "; foreach($typ as $t) echo $t.' '; echo "<br>";
				

        $ile['t']=0; $ile['0']=0; $ile['1']=0; $ile['%']=0; $ile['|']=0; $ile['c']=0;
        $info="diagnostyka: t=".$ile['t'].", 0=".$ile['0'].", 1=".$ile['1'].", %=".$ile['%'].", |=".$ile['|'].", c=".$ile['c'];
        foreach($typ as $t) $ile[$t]++;
        if($ile['c']>0) {   
          if($ile['0']>0 || $ile['1']>0 || $ile['%']>0 || $ile['|']>0) {
            $pytanie="";
            foreach($pyt as $p) $pytanie.= $p.'<br>';
            $this->txt2xml_wrong_format("Pytanie Close zawiera inne rodzaje pytań",$info,$pytanie);
          } else { //Przetworzenie pytania cloze
            $this->txt2xml_cloze($pyt,$typ);
          }
        } else if($typ[0]=="0" || $typ[0]=="1") {				
					$this->txt2xml_truefalse($pyt,$typ);
				} else
				{			
					///$ile['t']=0; $ile['0']=0; $ile['1']=0; $ile['%']=0; $ile['|']=0; $ile['c']=0;
					///foreach($typ as $t) $ile[$t]++;
					//echo "t=".$ile['t'].", 0=".$ile['0'].", 1=".$ile['1'].", %=".$ile['%'].", |=".$ile['|']."<br>";
					if($typ[0]=="t" && $ile['0']>0 && $ile['1']>0 && $ile['%']==0 && $ile['|']==0 && $ile['c']==0 )
					{
						foreach($typ as $t) if( ($t=="1") || ($t=="0") ) {$pom=$t; break;}
						if($ile['1']==1 && $pom=="1") 
						{
							$this->txt2xml_singlechoice($pyt,$typ);
						} else 
						{
							if($ile['1']>=1 && $ile['1']<=10) 
							{
								$this->txt2xml_multichoice($pyt,$typ,$ile['1']);
							} else {
								$info="diagnostyka: t=".$ile['t'].", 0=".$ile['0'].", 1=".$ile['1'].", %=".$ile['%'].", |=".$ile['|'].", c=".$ile['c'];
								$pytanie="";
								foreach($pyt as $p) $pytanie.= $p.'<br>';
								$this->txt2xml_wrong_format("Ilość poprawnych odpowiedzi w multichoice musi być od 1 do 10",$info,$pytanie);
							}
						}
					} else if($typ[0]=="t" && $ile['0']==0 && $ile['1']==0 && $ile['%']>0 && $ile['|']==0)
					{
						$this->txt2xml_shortanswer($pyt,$typ);
					} else if($typ[0]=="t" && $ile['0']==0 && $ile['1']==0 && $ile['%']==0 && $ile['|']>0)
					{
						$this->txt2xml_matching($pyt,$typ);
					} else if($typ[0]=="t" && $ile['0']==0 && $ile['1']==0 && $ile['%']==0 && $ile['|']==0 && $ile['t']>0)
					{
						$this->txt2xml_catagory($pyt,$typ);
					} else
					{
						$info="diagnostyka: t=".$ile['t'].", 0=".$ile['0'].", 1=".$ile['1'].", %=".$ile['%'].", |=".$ile['|'].", c=".$ile['c'];
						$pytanie="";
						foreach($pyt as $p) $pytanie.= $p.'<br>';
						$this->txt2xml_wrong_format("Nieznany typ pytania:",$info,$pytanie);
					}
				}//prawda/fałsz
			}//if isset
    		if(isset($typ)) unset($typ);
    		if(isset($pyt)) unset($pyt);
    		$nr_pustej=0;
    	} //pusta linia
    }//foreach

    return $this->error;
  }      

  public function txt2xml_catagory($pyt,$typ) 
  {
  	//echo "Utworzono nową kategorię ...<br>";
  	//zakładam, że ktoś omyłkowo mógł wpisać nazwę kategorii w kilku liniach,
  	//linie te zostaną połączone w jedną linię (będą oddzielone spacjami)
  	$kat="";
  	foreach($pyt as $p) $kat.=$p." ";
  	$kat = trim($kat);//usunięcie białych znaków z początku i końca łańcucha
  	$this->xml->xml_catagory($kat);
  }

  public function txt2xml_singlechoice($pyt,$typ) 
  {
  	//echo "Utworzono pytanie jednokrotny wybór ...<br>";
  	//zakładam, że pytanie będzie podane we właściwym formacie
  	$tryb=0; //0-pobieramy treść pytania, 1-pobieramy treść nowej odpowiedzi, 2-dalsza treść odpowiedzi
    $tresc=""; //treść pytania, odpowiedzi
   	for($nr=0;$nr<count($pyt);$nr++) 
   	{
   		if($tryb==0 && $typ[$nr]=="t") 
   		{
   			$tresc.=$pyt[$nr].'<br>';
   		}
   		else if($tryb==0 && $typ[$nr]!="t") 
   		{
   			$this->xml->xml_singlechoice_start($tresc);
   			$tryb=1;
   		}

 		if( $tryb==2 && $typ[$nr]=="t" )
		{
			$tresc.='<br>'.$pyt[$nr];
		} else if( $tryb==2 && $typ[$nr]!="t" )
		{  //koniec treści pytania
			$this->xml->xml_singlechoice($tresc,$odp);
   			$tryb=1;
		}

   		if( $tryb==1 && $typ[$nr]!="t" )
   		{ //nowa odpowiedź
   			  $tresc="";
   			  $odp=$typ[$nr];
   			  $tresc.=substr($pyt[$nr], 2, strlen($pyt[$nr])-2);
   			  $tryb=2;
		}
		if( ($tryb==1 || $tryb==2) && ($nr+1)==count($pyt) )
		{  //ostatnie pytanie - trzeba je zapisać 
			$this->xml->xml_singlechoice($tresc,$odp);
		}	
   	}//for
    $this->xml->xml_singlechoice_stop();
  }

  public function txt2xml_multichoice($pyt,$typ,$ilosc_poprawnych_odp) 
  {
  	//echo "Utworzono pytanie wielokrotny wybór ...<br>";
  	//zakładam, że pytanie będzie podane we właściwym formacie
  	$tryb=0; //0-pobieramy treść pytania, 1-pobieramy treść nowej odpowiedzi, 2-dalsza treść odpowiedzi
    $tresc=""; //treść pytania, odpowiedzi

   	for($nr=0;$nr<count($pyt);$nr++) 
   	{
   		if($tryb==0 && $typ[$nr]=="t") 
   		{
   			$tresc.=$pyt[$nr].'<br>';
   		}
   		else if($tryb==0 && $typ[$nr]!="t") 
   		{
   			$this->xml->xml_multichoice_start($tresc);
   			$tryb=1;
   		}

 		if( $tryb==2 && $typ[$nr]=="t" )
		{
			$tresc.='<br>'.$pyt[$nr];
		} else if( $tryb==2 && $typ[$nr]!="t" )
		{  //koniec treści pytania
			$this->xml->xml_multichoice($tresc,$odp,$ilosc_poprawnych_odp);
   			$tryb=1;
		}

   		if( $tryb==1 && $typ[$nr]!="t" )
   		{ //nowa odpowiedź
   			  $tresc="";
   			  $odp=$typ[$nr];
   			  $tresc.=substr($pyt[$nr], 2, strlen($pyt[$nr])-2);
   			  $tryb=2;
		}
		if( ($tryb==1 || $tryb==2) && ($nr+1)==count($pyt) )
		{  //ostatnie pytanie - trzeba je zapisać 
			$this->xml->xml_multichoice($tresc,$odp,$ilosc_poprawnych_odp);
		}	
   	}//for
    $this->xml->xml_multichoice_stop();  	
  }

  public function txt2xml_shortanswer($pyt,$typ) 
  {
  	//echo "Utworzono pytanie krótka odpowiedź ...<br>";

  	//zakładam, że pytanie będzie podane we właściwym formacie
  	//krótka odpowiedź musi być podana w jednej linii!
  	$tryb=0; //0-pobieramy treść pytania, 1-pobieramy treść krótkiej odpowiedzi
    $tresc=""; //treść pytania, odpowiedzi
   	for($nr=0;$nr<count($pyt);$nr++) 
   	{
   		//wczytujemy treść pytania
   		if($tryb==0 && $typ[$nr]=="t") 
   		{
   			$tresc.=$pyt[$nr].'<br>';
   		}
   		else if($tryb==0 && $typ[$nr]!="t") 
   		{
   			$this->xml->xml_shortanswer_start($tresc);
   			$tryb=1;
   		}

   		//przetwarzanie odpowiedzi
   		if( $tryb==1 && $typ[$nr]=="%" )
   		{ 
   			 $tresc="";
   			 $podzial = explode("%|", $pyt[$nr]); //czy zawsze będzie to dobry podział? sprawdzić!
   			 
         //co z przypadkiem, gdy jest ponad 100% lub inna zła liczba? trzeba to wcześniej sprawdzić!
         $procent=$podzial[0];
         //echo $procent;
         if($procent<1 || $procent>100) 
         {
            $info="Podano złą wartość procentu, ma być od 1% do 100%.";
            $this->txt2xml_wrong_format("Nieznany typ pytania:",$info,$pyt[$nr]);
         } else
    			 $this->xml->xml_shortanswer($podzial[1],$procent);
		}
	
   	}//for
    $this->xml->xml_shortanswer_stop();
  }

  public function txt2xml_matching($pyt,$typ) 
  {
	  //echo "Utworzono pytanie dopasuj odpowiedź ...<br>";

  	//zakładam, że pytanie będzie podane we właściwym formacie
  	//każda odpowiedź, która musi być dopasowana powinna być podana w jednej linii!
  	$tryb=0; //0-pobieramy treść pytania, 1-pobieramy treść krótkiej odpowiedzi
    $tresc=""; //treść pytania, odpowiedzi
   	for($nr=0;$nr<count($pyt);$nr++) 
   	{
   		//wczytujemy treść pytania
   		if($tryb==0 && $typ[$nr]=="t") 
   		{
   			$tresc.=$pyt[$nr].'<br>';
   		}
   		else if($tryb==0 && $typ[$nr]!="t") 
   		{
   			$this->xml->xml_matching_start($tresc);
   			$tryb=1;
   		}

   		//przetwarzanie odpowiedzi
   		if( $tryb==1 && $typ[$nr]=="|" )
   		{ 
   			 $tresc="";
   			 $podzial = explode("|", $pyt[$nr]); //czy zawsze będzie to dobry podział? sprawdzić!
   			 //co z przypadkiem, gdy jest ponad 100% lub inna zła liczba? trzeba to wcześniej sprawdzić!
   			 $this->xml->xml_matching($podzial[0],$podzial[1]);
		}
   	}//for
    $this->xml->xml_matching_stop();
  }

  public function txt2xml_truefalse($pyt,$typ) 
  {
  	//zakładam, że treść pytania może znajdować się w kilku liniach
  	//wszystkie linie zostaną uwzględnione, będą zakończone znacznikiem <br>
	  //echo "Utworzono pytanie prawda/fałsz ...<br>";
	
	  $pyt[0]=substr($pyt[0], 2, strlen($pyt[0])-2); //usuwamy pierwsze dwa znaki
	  $tresc="";
	
    //treść pytania zapisuję w jednej linii
    foreach($pyt as $p) { $tresc.=$p."<br>"; }
    //$tresc = str_replace("\r", "", $tresc);
    //$tresc = str_replace("\n", "", $tresc);
    $tresc = trim($tresc);//usunięcie białych znaków z początku i końca łańcucha
    $this->xml->xml_truefalse($tresc,$typ[0]);
  }

  public function txt2xml_cloze($pyt,$typ) 
  {
    $this->xml->xml_cloze_start();
    
    foreach($pyt as $p) { //dla każdej linii pytania
      if(isset($czesc)) unset($czesc);
      //echo "linia: $p<br>";
      $czesc = explode(" |", $p);
      $nr=0;
      foreach($czesc as $c)
      {
        $nr++;

        if(substr_count($c,"|")==0) { 
          $this->xml->xml_cloze_text($c);
          //echo "(text): $c<br>";
        } 
        else if(substr_count($c,"|")==1) { //to musi być cloze_shortanswer
          $cc=explode("|", $c);
          $this->xml->xml_cloze_shortanswer_start();
          $this->xml->xml_cloze_shortanswer_answer(100,$cc[0]);        
          $this->xml->xml_cloze_shortanswer_stop();
          $this->xml->xml_cloze_text($cc[1]);
        }
        else if(substr_count($c,"||")>0) { //błąd składni pytania
            $pytanie="";
            foreach($pyt as $p) $pytanie.= $p.'<br>';
            $this->txt2xml_wrong_format("Pytanie zagnieżdżone (Close) nie może zawierać sąsiednich znaków \"||\".","",$pytanie);

        } else if(substr_count($c,"|")==2 && $c[0]=="|") {//wiersz zaczyna się od cloze_shortanswer
          $cc=explode("|", $c);
          $this->xml->xml_cloze_shortanswer_start();
          $this->xml->xml_cloze_shortanswer_answer(100,$cc[1]);        
          $this->xml->xml_cloze_shortanswer_stop();
          $this->xml->xml_cloze_text($cc[2]);
        } else if(substr_count($c,"|")==2) {
          $cc=explode("|", $c);
          $this->xml->xml_cloze_text($cc[0]); 
          $this->xml->xml_cloze_shortanswer_start();
          $this->xml->xml_cloze_shortanswer_answer(100,$cc[1]);        
          $this->xml->xml_cloze_shortanswer_stop();
          $this->xml->xml_cloze_text($cc[2]);          
        } else if(substr_count($c,"|")>2 && $c[0]=="|") { //szczególny przypadek
          $cc=explode("|", $c);
          $this->xml->xml_cloze_multichoice_start();
          $ile=count($cc);
          for($i=1;$i<$ile-1;$i++) if($i==1) $this->xml->xml_cloze_multichoice_answer(100,$cc[$i]);
          else $this->xml->xml_cloze_multichoice_answer(0,$cc[$i]);
          $this->xml->xml_cloze_multichoice_stop();
          $this->xml->xml_cloze_text($cc[$ile-1]);
        } else if(substr_count($c,"|")>2) {
            //próba naprawy błędu użytkownika
            if(count($czesc)==1) { //gdy brak spacji przed znakiem |
              $cc=explode("|", $c);
              $this->xml->xml_cloze_text($cc[0]);
              $this->xml->xml_cloze_multichoice_start();
              $ile=count($cc);
              for($i=1;$i<$ile-1;$i++) if($i==1) $this->xml->xml_cloze_multichoice_answer(100,$cc[$i]);
              else $this->xml->xml_cloze_multichoice_answer(0,$cc[$i]);
              $this->xml->xml_cloze_multichoice_stop();
              $this->xml->xml_cloze_text($cc[$ile-1]);
            } else { //zakładam, że wszystko jest OK
            $cc=explode("|", $c);
            $this->xml->xml_cloze_multichoice_start();
            $ile=count($cc);
            for($i=0;$i<$ile-1;$i++) if($i==0) $this->xml->xml_cloze_multichoice_answer(100,$cc[$i]);
            else $this->xml->xml_cloze_multichoice_answer(0,$cc[$i]);
            $this->xml->xml_cloze_multichoice_stop();
            $this->xml->xml_cloze_text($cc[$ile-1]);
          }
        }
 
      } 
      $this->xml->xml_cloze_text("<br>\n");

      //echo $p.' '; echo "<br>";




        
    } //foreach1
    
    $this->xml->xml_cloze_stop() ;
  }

}
?>