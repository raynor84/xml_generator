<?php

/**
 * Description of Bundesanzeiger
 *
 * @author fischer
 */
class Bundesanzeiger {
    private $table;
    private $table_node;
    private $doc;
    CONST maxColls = 7;
    
    
    public function createTableNode(Tabelle $table,  DOMDocument & $doc) {
        $this->doc = & $doc;
        $this->table = $table;
        $this->table_node = $doc->createElement( "TABLE" );
		
        $this->structureTable();
        $this->createCOLs();
        $this->createTableHead();
        $this->createTableBody();
        echo $this->table->toHTML();
        
        return $this->table_node;
    }
    
    private function structureTable() {
    	$this->RemoveEmptyColumns();
    	$this->RemoveEmptyRows();
        $this->splitLargeTables();
    	
    }
    
    private function RemoveEmptyRows() {
    	$y_max = $this->table->getMaxY();
    	for($y = 0; $y <= $y_max; $y++) {
    		$b_empty_row = $this->checkifRowisEmpty($y);
    		if($b_empty_row == true) {
    			$this->table->deleteRow($y);
    			$y_max--;
    		}
    	}
    	
    }
    private function RemoveEmptyColumns() {
    	$x_max = $this->table->getMaxX();
    	for ($x=0; $x <= $x_max; $x++) {
    		$b_empty_columns = $this->checkifColumnsAreEmpty($x);
    		if($b_empty_columns == true) {
    			$this->table->deleteColumn($x);
    			$x_max--;
    		}
    		
    	}
    	 
    }
    
    private function checkifColumnsAreEmpty($x) {
    	//Debughelper::myecho($x."<br />");
        $zellen = $this->table->getCells();
        $y_max = $this->table->getMaxY();
    	for ($y=0; $y < $y_max; $y++) {
    		//Debughelper::myecho("Zelleninhalt:".$zellen[$y][$x]->getValue());
    		
    		if(($zellen[$y][$x]!=" ") && ($zellen[$y][$x] != false) &&
    				($zellen[$y][$x]->getValue() != "") &&($zellen[$y][$x]->getValue()!=NULL)) {
    			return false;
    		}
    		
    	}
		if($this->table->columnhascolspan($x)) {
    		return false;
		}
    	return true;
    }
    
    private function checkifRowisEmpty($y) {
    	//Debughelper::myecho($x."<br />");
    	$zellen = $this->table->getCells();
    	
    	$x_max = $this->table->getMaxX();
    	for ($x=0; $x < $x_max; $x++) {
    		$zelle = $zellen[$y][$x];

    		//check if empty cell
    		if(!empty($zelle)) {
    			//echo "[$y][$x]";
    		    if((!empty($zelle->getValue())) && ((strlen(trim($zelle->getValue()))) > 0)) {
    				//echo "zelle->getValue() > 0";
    				//echo "<br />";
    				return false;
    			}
    		}
    				
    	}
    	if($this->table->rowhasrowspan($y)) {
    		return false;
    	}
  		//echo "deleteRow:".$y;
    	return true;
    	 
    }
    
    
    private function splitLargeTables() {
        //check if large Table
        if(!$this->checkifLargeTable()) {
        	return;
        }
        
        $max_x = $this->table->getMaxX();
        $ben_tabellen = ceil($this->table->getMax() / self::maxColls);
        
        if($ben_tabellen = 1) {
           //do nothing no splitting necessary
        } else if($ben_tabellen = 2) {
            $table1;
            $table2;
            $t1_max;
            $t2_max;
            $splittableColumns = $this->getSplittablesColumns();
            foreach($splittableColumns as $x) {
                $t1_max = $x;
                $t2_max = $max_x - $x;
                if(($t1_max > self::maxColls)&&($t2_max > self::maxColls)) {
                    continue;
                } else {
                    break;
                }
                
            }
            //Split Table with position $t1_max and $t2_max;
            
            
        } else if($ben_tabellen = 3) {
            $table1;
            $table2;
            $table3;
            $splittableColumns = $this->getSplittablesColumns();
            foreach($splittableColumns as $x) {
                $t1_max = $x;
                $t2_max;
                $t3_max;
            }
            //split table with Position t1, t2, t3_max.
            
        } else {
            die("Tabelle muss anscheinend in mehr als 3 Teile aufgeteilt werden. Bitte überprüfen Sie, ob die Tabelle doch in 3 Tabellen teilbar ist.");
        }
        
        
    }
    
    
    private function getSplittablesColumns() {
        $this->table = new Tabelle();
        $zelle = new Zelle();
        $zellen = $this->table->getCells();
        $max_x = $this->table->getMaxX();
        $splittableColumns = array();
        
        for($x=0; $x < $max_x; $x++) {
            if($this->checkIfSplittable($x)) {
                $splittableColumns = array_push($splittableColumns, $x);
            }
        }
        return $splittableColumns;
    }
    private function checkIfSplittable($x) {
        $this->table = new Tabelle();
        $zelle = new Zelle();
        $zellen = $this->table->getCells();
        $max_y = $this->table->getMaxY();
        $b_splittable = true;
        for($y=0; $y < $max_y; $y++) {
            
            $zelle=$zellen[$y][$x];
            if(!is_a($zelle, "Zelle")) {
                $b_splittable = false;
                break;
            }
        }
        return $b_splittable;
    }
    

    private function checkifLargeTable() {
    	if($this->table->getMaxX() > self::maxColls) {
			return true;
		}
		return false;	
    }
    

    private function createCOLs() {
        
        for($i = 0; $i < $this->table->getMaxX(); $i++) {
            $this->table_node->appendChild(
                    $this->doc->createElement("COL")
            );
        }
    }
    
    private function createTableHead() {
    	$anz_headerzeilen = $this->table->getAnzHeader();
    	
	    	if($anz_headerzeilen > 0) {
	        $this->table_node->appendChild(
	                $this->doc->createElement("THEAD")
	        );
	        for($i=0; $i < $anz_headerzeilen;$i++) {
	        	$this->createTableRow($i);
	        }
    	}
    }
    
    private function createTableBody() {
        $this->table_node->appendChild(
                $this->doc->createElement("TBODY")
        );
        $y_count = $this->table->getMaxY();
        $anz_header = $this->table->getAnzHeader();
        for ($i=$anz_header; $i <= $y_count; $i++) {
            $this->createTableRow($i);
        }
        
    }
    private function createTableRow($y) {
        $row_node = $this->table_node->lastChild->appendChild(
                $this->doc->createElement("TR")
        		);
        $cells = $this->table->getCells();
        $cell_count = $this->table->getMaxX();
		$zellen = $this->table->getCells();
		for($x=0; $x<=$cell_count;$x++) {
	            if($zellen[$y][$x]!=null) {
	                $this->createCell($zellen[$y][$x]);
	            }
        }
    }
    private function createCell(Zelle $cell) {
        
        $cell_node = $this->doc->createElement("TD");
        $cell_node->setAttribute("align", $cell->getAlign());
        $cell_node->setAttribute("colspan", $cell->getColspan());
        $cell_node->setAttribute("rowspan", $cell->getRowspan());
        if($cell->getValue()==NULL) {
        	$cell->setValue(" ");
        }
		if($cell->getFormat()=="") {
	        $cell_node->appendChild(
	        	$this->doc->createTextNode($cell->getValue())
	        );
		} else {
	        $textnode = $this->doc->createTextNode($cell->getValue());
			if($cell->getFormat()=="bold") {
				$format = $this->doc->createElement("b");				
			} else if($cell->getFormat()=="italic") {
				$format = $this->doc->createElement("i");
			} else {
				die("Das Format ist Bundesanzeiger->createCell nicht bekannt.");
			}
	        $format->appendChild($textnode);
	        $cell_node->appendChild(
	        	$format
	        );
        	
		}
		$this->table_node->lastChild->lastChild->appendChild($cell_node);
		
        /*
         * reminder:
         * http://php.net/manual/de/domdocumentfragment.appendxml.php
         */
        //$content = $this->doc->createDocumentFragment();
        

    }
     
    public function output() {
        return $this->table_node;
    }
    
/*    
    private function splitRowspanCells() {
    	$y_count = $this->table->getMaxY();
    	$x_count = $this->table->getMaxX();
    
    	$zellen = $this->table->getCells();
    	for ($y =1; $y <= $y_count; $y++) {
    		foreach($zellen[$y] as $zelle) {
    			if($zelle->getRowspan() > 1) {
    				 
    			}
    		}
    	}
    }
*/    
}
