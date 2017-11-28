<?php
  //Robert Fidytek, 05.03.2017 r.
  //Klasa konwertuje pytanie z formatu txt na format Moodle XML 

	require_once("FormatMoodleXML.php");
  //dopracować:
	//kodowanie pliku tekstowego powinno być utf-8, inaczej klasa może sypać błędami
	//rozpatrzyć możliwość konwertowania w locie z windows-1250 i iso na utf-8

class Txt2XML
{ 
  private $f;      //zmienna plikowa
  private $fname;  //nazwa pliku
  public $xml;     //obiekt generujący kod xml

  public function __construct($fname=null)
  {
  	$this->fname=$fname;
    if($this->fname!=null)
      if(!($this->f=fopen($this->fname,'r'))) {
 		   echo "Plik ".$this->fname." nie istnieje! <br>";
 		   exit(1);
    	}
  	$this->xml = new FormatMoodleXML();
  }

  public function __destructor()
  {
    if($this->fname!=null)
    	fclose($this->f);
  }

  private function txt2xml_wrong_format($info,$diagnostyka,$pytanie) 
  {
  	echo "<span style=\"color:red\">".$info."</span>"."<br>";
  	echo $diagnostyka."<br>";
  	echo "<b>".$pytanie."</b><br>";
  } 

  //główna metoda
  public function txt2xml($tab=null) 
  {

    $rozmiar=count($tab);
  	$nr_pustej=0;

  	while($rozmiar>0 || !feof($this->f))
  	{
  		if($rozmiar>0) {$rozmiar--; list($k,$w)=each($tab); unser($tab[$k]);}
      else {$w=fgets($this->f);}
      

      //echo $w;

  		//zamieniam wszystkie wystąpienia "\|" na kody ASCII "|" (&#124;)
  		$w=str_replace("\|","&#124;",$w);
  		//określam typy linii:
  		//"0" lub "1"-linia rozpoczynająca się od 0 lub 1
  		//"%"-linia odpowiedzi do pytania krotka odpowiedź (zawiera "%|")
  		//"|"-linia różna od powyższych zawierająca "|", zakładam, że będzie to dopasuj odpowiedź
  		//"t"-linia z tekstem (treść pytrania, kolejna część odpowiedzi)
  		if(!preg_match('/[a-zA-ZąćęłńóśżźĘŁŃÓŚŻŹ0-9]+/', $w)) 
  		{
  			//echo '(Pusta linia): ';
  			$nr_pustej++;
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
  	
  		//pojawiła się pusta linia lub koniec pliku - trzeba określić typ wczytanego pytania
  		if($nr_pustej>0 || feof($this->f))
  		{
 			
			if(isset($typ)&&count($typ)>0)  //gdy zmiennej $typ nie ma, to znaczy, że wczytaliśmy tylko pustą linię
			{
				//echo "typ: "; foreach($typ as $t) echo $t.' '; echo "<br>";
				if($typ[0]=="0" || $typ[0]=="1") 
				{				
					$this->txt2xml_truefalse($pyt,$typ);
				} else
				{			
					$ile['t']=0; $ile['0']=0; $ile['1']=0; $ile['%']=0; $ile['|']=0;
					foreach($typ as $t) $ile[$t]++;
					//echo "t=".$ile['t'].", 0=".$ile['0'].", 1=".$ile['1'].", %=".$ile['%'].", |=".$ile['|']."<br>";
					if($typ[0]=="t" && $ile['0']>0 && $ile['1']>0 && $ile['%']==0 && $ile['|']==0)
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
								$info="diagnostyka: t=".$ile['t'].", 0=".$ile['0'].", 1=".$ile['1'].", %=".$ile['%'].", |=".$ile['|'];
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
						$info="diagnostyka: t=".$ile['t'].", 0=".$ile['0'].", 1=".$ile['1'].", %=".$ile['%'].", |=".$ile['|'];
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
    }//while
  }      

  public function txt2xml_catagory($pyt,$typ) 
  {
  	echo "Utworzono nową kategorię ...<br>";
  	//zakładam, że ktoś omyłkowo mógł wpisać nazwę kategorii w kilku liniach,
  	//linie te zostaną połączone w jedną linię (będą oddzielone spacjami)
  	$kat="";
  	foreach($pyt as $p) $kat.=$p." ";
  	$kat = trim($kat);//usunięcie białych znaków z początku i końca łańcucha
  	$this->xml->xml_catagory($kat);
  }

  public function txt2xml_singlechoice($pyt,$typ) 
  {
  	echo "Utworzono pytanie jednokrotny wybór ...<br>";
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
  	echo "Utworzono pytanie wielokrotny wybór ...<br>";

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
  	echo "Utworzono pytanie krótka odpowiedź ...<br>";

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
   			 $this->xml->xml_shortanswer($podzial[1],$podzial[0]);
		}
	
   	}//for
    $this->xml->xml_shortanswer_stop();
  }

  public function txt2xml_matching($pyt,$typ) 
  {
	echo "Utworzono pytanie dopasuj odpowiedź ...<br>";

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
	  echo "Utworzono pytanie prawda/fałsz ...<br>";
	
	  $pyt[0]=substr($pyt[0], 2, strlen($pyt[0])-2); //usuwamy pierwsze dwa znaki
	  $tresc="";
	
    //treść pytania zapisuję w jednej linii
    foreach($pyt as $p) { $tresc.=$p."<br>"; }
    //$tresc = str_replace("\r", "", $tresc);
    //$tresc = str_replace("\n", "", $tresc);
    $tresc = trim($tresc);//usunięcie białych znaków z początku i końca łańcucha
    $this->xml->xml_truefalse($tresc,$typ[0]);
  }
}
?>