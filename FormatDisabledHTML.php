<?php
  //Robert Fidytek, 24.04.2017 r.
  //Klasa wyświetla pytania w postaci formularzy w trybie disabled 
  //Zastosowano nazewnictwo metod z klasy FormatMoodleXML, aby móc podmienić stosowne obiekty

class FormatDisabledHTML
{
  
  private $number_question;  //numer pytania
  private $name_question;  //nazwa pytania
  private $category_name;    //nazwa kategorii
  private $q; 

  private $single;

  private $generalfeedback;
  private $defaultgrade;
  private $penalty;
  private $hidden;

  private $shuffleanswers;
  private $answernumbering;
  private $usecase;
  private $correctfeedback;
  private $partiallycorrectfeedback;
  private $incorrectfeedback;
       
  function __construct($parametr = null)
  {
    $this->number_question=0;
    $this->category_name="Pytanie";
    $this->name_question="Pytanie";
  }

  public function xml_catagory($name) 
  {
    $this->number_question=0;
    $this->category_name=$name;
    $this->q.="<h2>".$name."</h2>\n\n";   
  }
     
  public function xml_quiz() 
  {      
    return '<div>'."\n".$this->q."</div>\n";
  } 

  private function question_name() 
  {
    $this->number_question++;
    $this->name_question=$this->category_name.' '.str_pad($this->number_question,2,0,STR_PAD_LEFT);
    return $this->name_question;
  }

  //------------------------------------------------------------------       
  public function xml_multichoice_start($questiontext,$single="false") 
  {      
    $this->single=$single;
    $this->q.="<fieldset>\n";
    $this->q.="<legend>".$this->question_name()."</legend>\n";
    $this->q.=$questiontext."<br>\n";
  }  

  public function xml_multichoice($answer_text,$correct=0,$answers_correct=1,$multichoice=1) 
  {     
    $this->q.="<input type=\"";
    if($multichoice==1) $this->q.="checkbox"; else $this->q.="radio";
    $this->q.="\" name=\"".$this->name_question."\" value=\"\" ";
    if($correct==1) $this->q.="checked=\"checked\" ";
    $this->q.="disabled=\"disabled\">".$answer_text."<br>\n";
  } 

  public function xml_multichoice_stop() 
  {      
    $this->q.="</fieldset><br>\n\n";
  }      

  //------------------------------------------------------------------

  public function xml_singlechoice_start($questiontext) 
  {      
    $this->q.=$this->xml_multichoice_start($questiontext,"true");
  } 

  public function xml_singlechoice($answer_text,$correct=0) 
  {     
    $this->q.=$this->xml_multichoice($answer_text,$correct,1,0);
  } 

  public function xml_singlechoice_stop() 
  {      
    $this->q.=$this->xml_multichoice_stop();
  }    

 //------------------------------------------------------------------
  public function xml_truefalse($questiontext,$correct=0) 
  {      
    $this->q.="<fieldset>\n";
    $this->q.="<legend>".$this->question_name()."</legend>\n";
    $this->q.=$questiontext."<br>\n";
 
    $this->q.="<input type=\"radio\" name=\"".$this->name_question."\" value=\"\" ";
    if($correct==1 || $correct=="1") $this->q.="checked=\"checked\" ";
    $this->q.="disabled=\"disabled\">Prawda\n";
 
    $this->q.="<input type=\"radio\" name=\"".$this->name_question."\" value=\"\" ";
    if($correct==0) $this->q.="checked=\"checked\" ";
    $this->q.="disabled=\"disabled\">Fałsz\n";
 
    $this->q.="</fieldset><br>\n\n"; 
  }

 //------------------------------------------------------------------
  public function xml_shortanswer_start($questiontext) 
  { 
    $this->q.="<fieldset>\n";
    $this->q.="<legend>".$this->question_name()."</legend>\n";
    $this->q.=$questiontext."\n";
  }

  public function xml_shortanswer($answer,$fraction) 
  {
    $answer=trim($answer);

    $this->q.="<input type=\"text\" name=\"".$this->name_question."\" value=\"".$answer.
              "\" disabled=\"disabled\"> ".$fraction."%<br>\n";
  }

  public function xml_shortanswer_stop() 
  {
    $this->q.="</fieldset><br>\n\n";
  }

 //------------------------------------------------------------------
  public function xml_matching_start($questiontext) 
  {      
    $this->q.="<fieldset>\n";
    $this->q.="<legend>".$this->question_name()."</legend>\n";
    $this->q.=$questiontext."<br>\n";
  }

  public function xml_matching($text,$answer) 
  {      
    $this->q.="$text=";
    $this->q.="<input type=\"text\" name=\"".$this->name_question."\" value=\"".$answer.
              "\" disabled=\"disabled\"><br>\n";
  }

  public function xml_matching_stop() 
  {
    $this->q.="</fieldset><br>\n\n";
  }


  //------------------------------------------------------------------
  //cloze text
  public function xml_cloze_start() 
  {         
    $this->q.="<fieldset>\n";
    $this->q.="<legend>".$this->question_name()."</legend>\n";
  }

  public function xml_cloze_text($text) 
  {
    $this->q.=$text;
  }

  public function xml_cloze_stop() 
  {
   $this->q.="</fieldset><br>\n\n";
  }

  //shortanswer
  public function xml_cloze_shortanswer_start() 
  {
    $this->q.="\n<input type=\"text\" name=\"".$this->name_question."\" value=\"";
  }

  public function xml_cloze_shortanswer_answer($percent,$answer) 
  {  
    $this->q.=$answer;
    //$this->q.=' ('.$percent."%) ";
  }

  public function xml_cloze_shortanswer_stop() 
  {
    $this->q.="\" disabled=\"disabled\">"."\n";
  }

  //multichoice
  public function xml_cloze_multichoice_start() 
  {
    $this->q.="\n<select name=\"".$this->name_question."\" >\n"; //disabled=\"disabled\"
  }

  public function xml_cloze_multichoice_answer($percent,$answer) 
  {   
    if($percent>=100) $this->q.="<option selected=\"selected\">";
    else $this->q.="<option>";
    $this->q.=$answer;
    //$this->q.=.' ('.$percent."%)";
    $this->q.="</option>\n";
  }

  public function xml_cloze_multichoice_stop() 
  {
    $this->q.="</select>\n";
  }

  //numerical
  public function xml_cloze_numerical_start() 
  {
    $this->q.="\n<input type=\"text\" name=\"".$this->name_question."\" value=\"";
  }

  public function xml_cloze_numerical_answer($percent,$answer,$error=0) 
  {
    $this->q.='['.($answer+$error).','.($answer-$error).'] ('.$percent."%) ";
  }

  public function xml_cloze_numerical_stop() 
  {
     $this->q.="\" disabled=\"disabled\">"."\n";
  }

}


 ?>