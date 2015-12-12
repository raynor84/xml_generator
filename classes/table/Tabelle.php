<?php
require_once dirname(__FILE__) . '/Zelle.php';
class Tabelle {
	private $x_max;
	private $y_max;
	private $lastcoordinates_newcell = array();
	private $zellen;
	private $anz_headerzeilen=0;
	private $anz_footerzeilen=0;
       
        
	public function __construct($x_max,$y_max) {
		
		$x_max = intval($x_max)-1;	//for-loop
		$y_max = intval($y_max)-1;	//for-loop

		$this->setMaxX($x_max);
		$this->setMaxY($y_max);
		
		$this->lastcoordinates_newcell["x"]=0;
		$this->lastcoordinates_newcell["y"]=0;
		
	}

	public function setAnzHeader($anzahl) {
		if(!is_numeric($anzahl)) {
			die("setAnzahlHeader erwartet einen Numerischen Wert");
		}
		$this->anz_headerzeilen=$anzahl;
	}
	public function getAnzHeader() {
		return $this->anz_headerzeilen;
	}
	
	public function setAnzFooter($anzahl) {
		if(!is_numeric($anzahl)) {
			die("setAnzahlFooter erwartet einen Numerischen Wert");
		}
		$this->anz_footerzeilen=$anzahl;
	}
	public function getAnzFooter() {
		return $this->anz_footerzeilen;
	}
        public function getMaxX() {
            return $this->x_max;
        }
        
        public function getMaxY() {
            return $this->y_max;
        }
        public function setMaxX($x_max) {
        	if(!is_numeric($x_max)) {
        		die("Bitte für setMaxX einen numerischen Wert eingeben.");
        	}
        	$this->x_max = $x_max;
        }
        public function setMaxY($y_max) {
        	if(!is_numeric($y_max)) {
        		die("Bitte für setMaxY einen numerischen Wert eingeben.");
        	}
        	$this->y_max = $y_max;
        }
        public function addCell($zelle) {
			if(!is_a($zelle, "Zelle")) {
				die("Bitte übergeben Sie addCell eine Variable. Sie haben für den Parameter .'$zelle'. 
						folgenden wert gegeben:<br /> $zelle");
			}
			list($y, $x)=$this->getNewCoordinates();
			//echo "y:$y x:$x";
			
			$this->zellen[$y][$x] = $zelle;
			
			if($zelle != NULL) {
				$colspan = $zelle->getColspan();
				$rowspan = $zelle->getRowspan();
			} else {
				$colspan = 0;
				$rowspan = 0;
			}
			if($colspan > 1) {
				for($i=1; $i < $colspan; $i++) {
					list($y_tmp, $x_tmp) = $this->getNewCoordinates();
					//Debughelper::myecho($x_tmp." ".$y_tmp."<br />");
					$this->zellen[$y_tmp][$x_tmp] = false;
				}
			}
			if($rowspan > 1) {
				for($i=1; $i < $rowspan; $i++) {
					$this->zellen[$y+$i][$x] = false;
				}
			}
			
        }
        
        private function getNewCoordinates () {
			$x = $this->lastcoordinates_newcell["x"];
			$y = $this->lastcoordinates_newcell["y"];

			if($x < $this->x_max) {
				$new_x = $this->lastcoordinates_newcell["x"]++;
				$zellen = $this->zellen;
			} 
			else if($y < $this->y_max) {
				$this->lastcoordinates_newcell["y"]++;
				$this->lastcoordinates_newcell["x"] = 0;
				
			}
			else {

			}
			
			
			$tmp_x = $this->lastcoordinates_newcell["x"];
			$tmp_y = $this->lastcoordinates_newcell["y"];
			
			if((isset($this->zellen[$tmp_y][$tmp_x]))&&($this->zellen[$tmp_y][$tmp_x]==false)){
				//list($y, $x) = $this->getNewCoordinates();
				$this->getNewCoordinates();
			}

			return array($y, $x);

        }
        	 
	 	
		public function getCell($y, $x) {
			return $this->zellen[$y][$x];
		}
        public function getCells() {
        	$zellen = $this->zellen;
        	array_filter($zellen);
        	return $zellen;
        }

		public function toHTML() {
			$html = "<table border=\"1\">";
					$html .= $this->getHTMLTableHeader ();
                                        $html .= $this->getHTMLTableBody();
			$html.= "</table>";
			return $html;
		}
                
                private function getHTMLTableHeader() {
                    $anz_headerzeilen = $this->getAnzHeader();
                    if($anz_headerzeilen < 1) {
                        return "";
                    }
                    $html="<THEAD>";
			for($y = 0; $y < $this->getAnzHeader(); $y++) {
                            $html.=$this->getHTMLRow($y);
                        }
                    $html.="</THEAD>";
                    return $html;
                }
                
                private function getHTMLTableBody() {
                        $body_anfang = $this->getAnzHeader();
                        $max_y = $this->getMaxY();
                        $html = "";
			for($y = $body_anfang; $y <= $max_y; $y++) {
                            $html.=$this->getHTMLRow($y);
                        }
                        return $html;
                }
		/**
		 * @param html
		 */
                private function getHTMLRow($y) {
			$html="<tr>\n";
			for($x = 0; $x <=$this->getMaxX();$x++) {
				$html .= $this->getHTMLCell ($y, $x);
			}
			$html.= "</tr>\n";
			return $html;
		}
	
		/**
		 * @param html
		 * @param y
		 * @param x
		 */
		 private function getHTMLCell($y, $x) {
		 	$html = "";
			$cell = $this->zellen[$y][$x];
			if(is_a(
					$cell,
					"Zelle"
			)) {
				$value="";
				if($cell->getFormat()=="bold") {
					$value ="<b>";
					$value.=$cell->getValue();
					$value.="</b>";
				} else if($cell->getFormat()=="italic") {
					$value ="<i>";
					$value.=$cell->getValue();
					$value.="</i>";
				} else {
					$value = $cell->getValue();
				}
				$align = $cell->getAlign();
				$colspan = $cell->getColspan();
				$rowspan = $cell->getRowspan();
				$html .= "<td align=\"$align\" rowspan=\"$rowspan\" colspan=\"$colspan\">";
				$html .= $value;
				$html .= "</td>\n";
				return $html;
			}
		}
	
		
		private function validateCoordinates($x, $y) {
			$int_x=intval($x);
			$int_y=intval($y);
			if(($this->y_max < $int_y)
					||($this->x_max < $int_x)) {
				die("Koordinaten(zeilennr und spaltennr.) müssen innerhalb Spaltenanz. / Zeilenanz. der Tabelle sein ");
	                }
			
		}
	        
	    public function __toString() {
	    	$html = "<table border=\"1\">";
			for($y = 0; $y <= $this->getMaxY(); $y++) {
				$html.="<tr>\n";
				for($x = 0; $x <=$this->getMaxX();$x++) {
					$cell = $this->zellen[$y][$x];
	    		    $html .= "<td>";
	    			if(is_a($cell,"Zelle")) {
	    				$html .= $cell->getValue();
	    			} else if($cell==false) {
	    				$html.="false";
	    			}
					$html .= "</td>\n";
	    		} 
	    		$html.= "</tr>\n";
	    	}
	    	$html.= "</table>";
	    	return $html;
	    	
	    }
            public function deleteColumnsExceptfromCol1toCol2($x_min, $x_max) {
                if((!is_numeric($x_min))||(!is_numeric($x_max))) {
                    die("x_min und x_max müssen Numerisch sein.");
                } 
                if($x_min > $x_max) {
                    die("x_min($x_min) muss größer als x_max($x_max) sein.");
                }
                if($x_min > 0) {
                    $this->delColumn_x1tox2(0, $x_min-1);
                }
                if($x_max < $this->getMaxX()) {
                    $this->delColumn_x1tox2($x_max+1, $this->getMaxX());
                }
            }
            
            public function delColumn_x1tox2($x1, $x2) {
                if(!((is_numeric($x1))&&(is_numeric($x2)))) {
                    die("fuer die Methode deleteColumns1toColumn2 muessen x1 und x2 numerisch sein.");
                }
                if($x2 < $x1) {
                    die("x2 muss groeßer sein als x1");
                }
                $x_max = $this->getMaxX();
                if($x2 > $x_max) {
                    die("x2($x2) darf nicht größer als x_max($x_max) sein.");
                }
                
                
                /*
                 * todo: Refactoring
                 */
                $y_max = $this->getMaxY();
                $cell = new Zelle();
                
                //column x1 sollte nicht mit vorheriger Spalte verbunden sein.
                $skip = 0;
                for($y=0; $y < $y_max; $y++) {
                    $cell = $this->zellen[$y][$x1];
                    if(($cell==false)&&($skip < 1)) {
                        
                        die("Column1 ist mit einer Spalte außerhalb verbunden");
                        
                    } else if(($cell == false)&&($skip >= 1)) {
                    
                        $skip--;

                    } else {
                            $rowspan = $cell->getRowspan();
                            $skip = $rowspan -1;
                    }
                    
                    
                }
                
                //column x2 sollte nicht mit vorheriger Spalte verbunden sein
                for($y=0; $y < $y_max; $y++) {
                    $cell = $this->zellen[$y][$x2];
                    if($cell == false) {
                        
                    } else if($cell->getColspan() > 1) {
                        die("Column2 ist mit einer Spalte außerhalb verbunden.");
                    }
                }
                
                //prüfen bei nächsten Spalte, ob Column2 mit einer Spalte außerhalb verbunden ist, 
                //bzw. innerhalb von col1 und col2 eine Zelle mit einer Zelle außerhalb verbunden ist.
                for($y=0; $y < $y_max; $y++) {
                    if($x2+1 > $x_max) {
                        break;
                    }
                    $cell = $this->zellen[$y][$x2+1];
                    if(($cell==false)&&($skip < 1)) {
                        
                        die("Column2 ist mit einer Spalte außerhalb verbunden");
                        
                    } else if(($cell == false)&&($skip >= 1)) {
                    
                        $skip--;

                    } else {
                            $rowspan = $cell->getRowspan();
                            $skip = $rowspan -1;
                    }
                }
                
                for($x=$x1; $x<=$x2; $x++) {
                    for($y=0; $y <=$y_max; $y++) {
                        $zelle = new Zelle();
                        $zelle->setColspan(1);
                        $zelle->setRowspan(1);
                        $zelle->setValue("NULL");
                        $this->zellen[$y][$x] = $zelle;
                    }
                }

                for($x=$x1; $x<=$x2; $x2--) {
                        $this->deleteColumn($x);
                }
            }
            public function deleteColumn($x) {
	    	if(!is_numeric($x)) {
	    		die("fuer die Methode deleteColumn muss ein Numerischer Wert übergeben werden.");	
	    	}
	    	if($this->columnhascolspan($x)) {
	    		die("Teile der Zellen sind mit Zellen von anderen Spalten verbunden.($x)");
	    	}
	    		$y_max = $this->getMaxY();
	    		for ($y=0; $y <= $y_max; $y++) {
					$this->deleteCell($y, $x);
	    	}
	    	
	    	$max_x = $this->getMaxX();
	    	$this->setMaxX($max_x-1);
	    	
	    }
	    
	    public function deleteRow($y) {
	    	if(!is_numeric($y)) {
	    		die("fuer die Methode deleteRow muss ein Numerischer Wert übergeben werden.");
	    	}
	    	if($this->rowhasrowspan($y)) {
	    		die("Teile der Zellen sind mit Zellen von anderen Spalten verbunden.");
	    	}
	    	
	    	$max_y = $this->getMaxY();
	    	for($i = $y; $i < $max_y; $i++) {
	    		$this->zellen[$i] = $this->zellen[$i+1];
	    	}
	    	unset($this->zellen[$max_y]);
			
	    	$this->setMaxY($max_y-1);
	    }
	    	 
	    public function rowhasrowspan($y) {
	    	$x_max = $this->getMaxX();
	    	$skip = 0;
	    	for ($x=0; $x <= $x_max; $x++) {
	    		$cell = $this->zellen[$y][$x];
	    		 
	    		if(($cell == false)&&($skip < 1)) {
	    			return true;
	    			 
	    		} else if(($cell == false)&&($skip >= 1)) {
	    			$skip--;
	    			 
	    		} else {
	    			//$cell = new Zelle();
	    			$colspan = $cell->getColspan();
	    			$rowspan = $cell->getRowspan();
	    			$skip = $colspan -1;
	    			if($rowspan > 1) {
	    				return true;
	    			}
	    		}
	    	}
	    }
	    public function columnhascolspan($x) {
	    	$y_max = $this->getMaxY();
	    	$skip = 0;
	    	for ($y=0; $y <= $y_max; $y++) {
	    		$cell = $this->zellen[$y][$x];
	    		
	    		if(($cell == false)&&($skip < 1)) {
                                    echo "test $y $x";
                                    echo "Zelle: ".$this->zellen[$y][$x-1]->getValue();
	    				return true;
	    		
	    		} else if(($cell == false)&&($skip >= 1)) {
	    				$skip--;
	    		
	    		} else {
	    			//$cell = new Zelle();
	    			$colspan = $cell->getColspan();
	    			$rowspan = $cell->getRowspan();
	    			$skip = $rowspan -1;
	    			if($colspan > 1) {
	    				return true;
	    			}
	    		}
	    	}
	    	return false;
	    }
	    
	    
                   
	    public function deleteCell($y, $x) {
	    	//http://php.net/manual/de/function.array-shift.php
	    	$max_x = $this->getMaxX();
	    	
	    	for($i=$x; $i < $max_x; $i++) {
	    		$this->zellen[$y][$i] = $this->zellen[$y][$i+1];
	    	}
	    	unset($this->zellen[$y][$max_x]);
	    } 
            
            public function __clone() {
/*                $this->anz_headerzeilen = clone $this->anz_headerzeilen;
                $this->anz_footerzeilen = clone $this->anz_footerzeilen;
                $this->x_max = clone $this->x_max;
                $this->y_max = clone $this->y_max;
*/                $tmp_zellen = array();
                foreach($this->zellen as $zelle) {
                    if(!is_a($zelle, "Zelle")) {
                        continue;
                    }
                    $tmp_zelle = clone $zelle;
                    array_push($tmp_zellen, $tmp_zelle);
                }
                
            }

		/*
		public function addColl(Integer $anzahl);
		public function addRow(Integer $anzahl);
		public function addColBefore(Integer $position, Integer $anzahl = 1);
		public function addColAfter(Integer $position, Integer $anzahl = 1);
		public function addRowBefore(Integer $position, Integer $anzahl = 1);
		public function addRowAfter(Integer $position, Integer $anzahll = 1);
		*/
}

?>
