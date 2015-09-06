<?php

include('./classes/Bundesanzeiger/TableFormatter.php');

/**
 * Description of BundesXMLCreator
 *
 * @author stefan
 */
class TableXML {
    
    
    public function createTableNode(Tabelle $table,  DOMDocument & $doc) {
        $this->doc = & $doc;
        $bundesanz = new TableFormatter();
		
        $bundesanz->structureTable($table);
        
        $tables = $bundesanz->splitLargeTables($table);
        
        foreach($tables as $bundestable) {
            $this->table_node = $doc->createElement( "TABLE" );
            $this->createCOLs($bundestable);
            $this->createTableHead($bundestable);
            $this->createTableBody($bundestable);
            echo $bundestable->toHTML();
        }
        return $this->table_node;
    }
    
    private function createCOLs($table) {
        
        for($i = 0; $i <= $table->getMaxX(); $i++) {
            $this->table_node->appendChild(
                    $this->doc->createElement("COL")
            );
        }
    }
    
    private function createTableHead($table) {
    	$anz_headerzeilen = $table->getAnzHeader();
    	
	    	if($anz_headerzeilen > 0) {
	        $this->table_node->appendChild(
	                $this->doc->createElement("THEAD")
	        );
	        for($i=0; $i < $anz_headerzeilen;$i++) {
	        	$this->createTableRow($i);
	        }
    	}
    }
    
    private function createTableBody($table) {
        $this->table_node->appendChild(
                $this->doc->createElement("TBODY")
        );
        $y_count = $table->getMaxY();
        $anz_header = $table->getAnzHeader();
        for ($i=$anz_header; $i <= $y_count; $i++) {
            $this->createTableRow($i, $table);
        }
        
    }
    private function createTableRow($y, $table) {
        $row_node = $this->table_node->lastChild->appendChild(
                $this->doc->createElement("TR")
        		);
        $cells = $table->getCells();
        $cell_count = $table->getMaxX();
		$zellen = $table->getCells();
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
    
}
