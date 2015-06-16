<?php
class Zelle {
	private $align;
	private $colspan;
	private $rowspan;
    private $value = "";
    private $formatting="normal";
        
	public function __construct() {
		
	}
	
	public function setCell($values, $cols, $rows, $format) {
		if(!is_array($values))
			die('setCells erwartet, dass $values ein array ist.');
		$this->set_colspan($cols);
		$this->set_rowspan($rows);
		
		$this->setFormat($format);
		$size = count($values);
		for($i=0; $i<$size; $i++) {
			if($i!=0) {
				$this->appendValue("&amp;lt;br /&amp;gt;");
			}
			$this->appendValue($values[$i]);
		}
	}
	public function setAlign($align) {
		if(($align != "left")&&($align != "center")&&($align != "right")&&($align !="")) {
			die("Bitte geben Sie für Zelle->setAlign left, center oder right an statt: $align");
		}
		if($align=="") {
			$align ="left";
		}
		$this->align = $align;
	}
	
	public function setRowspan($rowspan) {
		return $this->rowspan = $rowspan;
	}
	
	public function setColspan($colspan) {
		return $this->colspan = $colspan;
	}
	
	public function setValue($value) {
		if($value==NULL) {
			$value = "";
		}
		$this->value = $value;
	}
	public function setFormat($format) {
		$format = trim($format);
		

		$acceptedformats = array(
				"",
				"bold",
				"italic"
		);
		$b_formataccepted = false;
		//Debughelper::myprint_r($format);
		
		foreach($acceptedformats as $acceptedformat) {
			if($format==$acceptedformat) {
				$this->formatting=$format;
				$b_formataccepted=true;
				break;
			}
		}
		if($b_formataccepted == false) {
			die("Bitte verwenden Sie ein gültiges Format. Diese wären:". print_r($acceptedformats));
		}
	}
	
	public function appendValue($value) {
		$string = $this->getValue();
		$this->setValue($string.$value);
		
	}
	public function getAlign() {
        return $this->align;
    }
    public function getColspan() {
        return $this->colspan;
    }
    public function getRowspan() {
        return $this->rowspan;
    }
    public function getValue() {
        return $this->value;
    }
    public function getFormat() {
    	return $this->formatting;
    }
}