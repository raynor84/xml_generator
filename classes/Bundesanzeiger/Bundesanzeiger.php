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
        //first table is equal to processing table
        //do
            //if deletable delete last column of procesing table and add it to next table.
            //if processing table <= maxColls
                //set table[] to processing_table
                //if next table > maxColls
                    //set processing table to next table.
        //while processing table > maxColls 
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
