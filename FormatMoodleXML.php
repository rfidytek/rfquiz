<?php
  //Robert Fidytek, 11.03.2017 r.
  //Klasa zapizuje pytanie w formacie Moodle XML 

class FormatMoodleXML
{
  
  private $number_question;  //numer pytania
  private $category_name;    //nazwa kategorii
  private $q;                //tworzone pytania

  private $generalfeedback;
  private $defaultgrade;
  private $penalty;
  private $hidden;
  private $single;
  private $shuffleanswers;
  private $answernumbering;
  private $usecase;
  private $correctfeedback;
  private $partiallycorrectfeedback;
  private $incorrectfeedback;
       
  function __construct($parametr = null)
  {
    $this->number_question=0;
    $this->category_name="Pytanie ";
    $this->generalfeedback="";
    $this->defaultgrade="1.0000000";
    $this->penalty="0.3333333";
    $this->hidden="0";
    $this->single="true";
    $this->shuffleanswers="true";
    $this->answernumbering="none";
    $this->usecase="0";

    $this->correctfeedback="Twoja odpowiedź jest poprawna.";
    $this->partiallycorrectfeedback="Twoja odpowiedź jest częściowo poprawna.";
    $this->incorrectfeedback="Twoja odpowiedź jest niepoprawna.";
  }

  public function xml_catagory($name) 
  {
    $this->number_question=0;
    $this->category_name=$name;
    $this->q.="  <question type=\"category\">\n";
    $this->q.="    <category>\n";
    $this->q.='        <text>$course$/'.$this->category_name."</text>\n";
    $this->q.="    </category>\n";
    $this->q.="  </question>\n\n";    
  }
     
  public function xml_quiz() 
  {      
    return '<?xml version="1.0" encoding="UTF-8"?>'."\n<quiz>\n\n".$this->q."</quiz>\n\n";
  } 

  private function question_name() 
  {
    $this->number_question++;
    return $this->category_name.' '.str_pad($this->number_question,2,0,STR_PAD_LEFT);
  }

  //------------------------------------------------------------------       
  public function xml_multichoice_start($questiontext,$single="false") 
  {      
    $this->single=$single;
    $this->q.="  <question type=\"multichoice\">\n";
    $this->q.="    <name>\n";
    $this->q.="      <text>".$this->question_name()."</text>\n";
    $this->q.="    </name>\n";
    $this->q.="    <questiontext format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$questiontext</p>]]></text>\n";
    $this->q.="    </questiontext>\n";
    $this->q.="    <generalfeedback format=\"html\">\n";
    $this->q.="      <text>$this->generalfeedback</text>\n";
    $this->q.="    </generalfeedback>\n";
    $this->q.="    <defaultgrade>$this->defaultgrade</defaultgrade>\n";
    $this->q.="    <penalty>$this->penalty</penalty>\n";
    $this->q.="    <hidden>$this->hidden</hidden>\n";
    $this->q.="    <single>$this->single</single>\n";
    $this->q.="    <shuffleanswers>$this->shuffleanswers</shuffleanswers>\n";
    $this->q.="    <answernumbering>$this->answernumbering</answernumbering>\n";
    $this->q.="    <correctfeedback format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$this->correctfeedback</p>]]></text>\n";
    $this->q.="    </correctfeedback>\n";
    $this->q.="    <partiallycorrectfeedback format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$this->partiallycorrectfeedback</p>]]></text>\n";
    $this->q.="    </partiallycorrectfeedback>\n";
    $this->q.="    <incorrectfeedback format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$this->incorrectfeedback</p>]]></text>\n";
    $this->q.="    </incorrectfeedback>\n";
    $this->q.="    <shownumcorrect/>\n";
  }  

  public function xml_multichoice($answer_text,$correct=0,$answers_correct=1,$multichoice=1) 
  {     
    if($correct==1) {
      $fraction=100/$answers_correct;
    } 
    else {
      if($multichoice==1) {$fraction="-100";} else {$fraction="0";}
    } 
    $this->q.="    <answer fraction=\"$fraction\" format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$answer_text</p>]]></text>\n";
    $this->q.="      <feedback format=\"html\">\n";
    $this->q.="        <text></text>\n";
    $this->q.="      </feedback>\n";
    $this->q.="    </answer>\n"; 
  } 

  public function xml_multichoice_stop() 
  {      
    $this->q.="  </question>\n\n";
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
    $this->q.="  <question type=\"truefalse\">\n";
    $this->q.="    <name>\n";
    $this->q.="      <text>".$this->question_name()."</text>\n";
    $this->q.="    </name>\n";
    $this->q.="    <questiontext format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>".$questiontext."</p>]]></text>\n";
    $this->q.="    </questiontext>\n";
    $this->q.="    <generalfeedback format=\"html\">\n";
    $this->q.="      <text>$this->generalfeedback</text>\n";
    $this->q.="    </generalfeedback>\n";
    $this->q.="    <defaultgrade>$this->defaultgrade</defaultgrade>\n";
    $this->q.="    <penalty>$this->penalty</penalty>\n";
    $this->q.="    <hidden>$this->hidden</hidden>\n";
    if($correct==1) {$fraction="100";} else {$fraction="0";}
    $this->q.="    <answer fraction=\"$fraction\" format=\"moodle_auto_format\">\n";
    $this->q.="      <text>true</text>\n";
    $this->q.="      <feedback format=\"html\">\n";
    $this->q.="        <text></text>\n";
    $this->q.="      </feedback>\n";
    $this->q.="    </answer>\n";
    if($correct==1) {$fraction="0";} else {$fraction="100";}
    $this->q.="    <answer fraction=\"$fraction\" format=\"moodle_auto_format\">\n";
    $this->q.="      <text>false</text>\n";
    $this->q.="      <feedback format=\"html\">\n";
    $this->q.="        <text></text>\n";
    $this->q.="      </feedback>\n";
    $this->q.="    </answer>\n";
    $this->q.="  </question>\n\n";  
  }

 //------------------------------------------------------------------
  public function xml_shortanswer_start($questiontext) 
  { 

    //$questiontext=trim($questiontext);
    //echo $questiontext."<br>";
    $this->q.="  <question type=\"shortanswer\">\n";
    $this->q.="    <name>\n";
    $this->q.="      <text>".$this->question_name()."</text>\n";
    $this->q.="    </name>\n";
    $this->q.="    <questiontext format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$questiontext</p>]]></text>\n";
    $this->q.="    </questiontext>\n";
    $this->q.="    <generalfeedback format=\"html\">\n";
    $this->q.="      <text></text>\n";
    $this->q.="    </generalfeedback>\n";
    $this->q.="    <defaultgrade>$this->defaultgrade</defaultgrade>\n";
    $this->q.="    <penalty>$this->penalty</penalty>\n";
    $this->q.="    <hidden>$this->hidden</hidden>\n";
    $this->q.="    <usecase>$this->usecase</usecase>\n";
  }

  public function xml_shortanswer($answer,$fraction) 
  {
    $answer=trim($answer);
    $this->q.="    <answer fraction=\"$fraction\" format=\"moodle_auto_format\">\n";
    $this->q.="      <text><![CDATA[$answer]]></text>\n";
    $this->q.="      <feedback format=\"html\">\n";
    $this->q.="        <text></text>\n";
    $this->q.="      </feedback>\n";
    $this->q.="    </answer>\n";
  }

  public function xml_shortanswer_stop() 
  {
    $this->q.="  </question>\n\n";
  }

 //------------------------------------------------------------------
  public function xml_matching_start($questiontext) 
  {      
    $this->q.="  <question type=\"matching\">\n";
    $this->q.="    <name>\n";
    $this->q.="      <text>".$this->question_name()."</text>\n";
    $this->q.="    </name>\n";
    $this->q.="    <questiontext format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$questiontext</p>]]></text>\n";
    $this->q.="    </questiontext>\n";
    $this->q.="    <generalfeedback format=\"html\">\n";
    $this->q.="      <text></text>\n";
    $this->q.="    </generalfeedback>\n";
    $this->q.="    <defaultgrade>$this->defaultgrade</defaultgrade>\n";
    $this->q.="    <penalty>$this->penalty</penalty>\n";
    $this->q.="    <hidden>$this->hidden</hidden>\n";
    $this->q.="    <shuffleanswers>$this->shuffleanswers</shuffleanswers>\n";
    $this->q.="    <correctfeedback format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$this->correctfeedback</p>]]></text>\n";
    $this->q.="    </correctfeedback>\n";
    $this->q.="    <partiallycorrectfeedback format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$this->partiallycorrectfeedback</p>]]></text>\n";
    $this->q.="    </partiallycorrectfeedback>\n";
    $this->q.="    <incorrectfeedback format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$this->incorrectfeedback</p>]]></text>\n";
    $this->q.="    </incorrectfeedback>\n";
    $this->q.="    <shownumcorrect/>\n";
  }

  public function xml_matching($text,$answer) 
  {      
    $this->q.="    <subquestion format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>$text</p>]]></text>\n";
    $this->q.="      <answer>\n";
    $this->q.="        <text>$answer</text>\n";
    $this->q.="      </answer>\n";
    $this->q.="    </subquestion>\n";
  }

  public function xml_matching_stop() 
  {
    $this->q.="  </question>\n\n";
  }

 //------------------------------------------------------------------
  public function xml_cloze_start() 
  {         
    $this->q.="  <question type=\"cloze\">\n";
    $this->q.="    <name>\n";
    $this->q.="      <text>".$this->question_name()."</text>\n";
    $this->q.="    </name>\n";
    $this->q.="    <questiontext format=\"html\">\n";
    $this->q.="      <text><![CDATA[<p>";
  }

  public function xml_cloze_text($text) 
  {
    $this->q.=$text;
  }

  public function xml_cloze_stop() 
  {
    $this->q.="</p>]]></text>\n";
    $this->q.="    </questiontext>\n";
    $this->q.="    <generalfeedback format=\"html\">\n";
    $this->q.="      <text></text>\n";
    $this->q.="    </generalfeedback>\n";
    $this->q.="    <penalty>$this->penalty</penalty>\n";
    $this->q.="    <hidden>$this->hidden</hidden>\n";
    $this->q.="  </question>\n\n";
  }

  //shortanswer
  public function xml_cloze_shortanswer_start() 
  {
    $this->q.=" {1:SHORTANSWER:";
  }

  public function xml_cloze_shortanswer_answer($percent,$answer) 
  {
    if ($percent<=0) ;
    else if ($percent>=100) $this->q.="=$answer#$this->correctfeedback";   
    else $this->q.="~%$percent%$answer#$this->partiallycorrectfeedback";
  }

  public function xml_cloze_shortanswer_stop() 
  {
    $this->q.="}";
  }

  //multichoice
  public function xml_cloze_multichoice_start() 
  {
    $this->q.=" {1:MULTICHOICE:";
  }

  public function xml_cloze_multichoice_answer($percent,$answer) 
  {
    if ($percent<=0) $this->q.="~$answer#$this->incorrectfeedback";
    else if ($percent>=100) $this->q.="=$answer#$this->correctfeedback";   
    else $this->q.="~%$percent%$answer#$this->partiallycorrectfeedback";
  }

  public function xml_cloze_multichoice_stop() 
  {
    $this->q.="}";
  }

  //numerical
  public function xml_cloze_numerical_start() 
  {
    $this->q.=" {1:NUMERICAL:";
  }

  public function xml_cloze_numerical_answer($percent,$answer,$error=0) 
  {
    if ($percent<=0) ;
    else if ($percent>=100) $this->q.="=$answer:$error#$this->correctfeedback";   
    else $this->q.="~%$percent%$answer:$error#$this->partiallycorrectfeedback";
  }

  public function xml_cloze_numerical_stop() 
  {
    $this->q.="}";
  }

}
 ?>