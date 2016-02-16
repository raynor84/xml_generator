<?php

include('./classes/Bundesanzeiger/TableFormatter.php');

/**
 * Description of BundesXMLCreator
 *
 * @author stefan
 */
class TableXML {
    private $table_nodes = array();
    private $tmp_node;
    private $doc;
    
    
    public function createTableNode(Tabelle $table,  DOMDocument & $doc) {
        $this->doc = & $doc;
        $bundesanz = new TableFormatter();
		
        $bundesanz->formatTable($table);
        
        $tables = $bundesanz->splitLargeTables($table);
        //TODO: Refactor
        foreach($tables as $bundestable) {
            $this->tmp_node = $doc->createElement( "TABLE" );
            $this->createCOLs($bundestable);
            $this->createTableHead($bundestable);
            $this->createTableBody($bundestable);

            array_push($this->table_nodes, $this->tmp_node);
            
        }
        return $this->table_nodes;
    }
    
    private function createCOLs($table) {
        
        for($i = 0; $i <= $table->getMaxX(); $i++) {
            $this->tmp_node->appendChild(
                    $this->doc->createElement("COL")
            );
        }
    }
    
    private function createTableHead($table) {
    	$anz_headerzeilen = $table->getAnzHeader();
    	
	    	if($anz_headerzeilen > 0) {
	        $this->tmp_node->appendChild(
	                $this->doc->createElement("THEAD")
	        );
	        for($i=0; $i < $anz_headerzeilen;$i++) {
	        	$this->createTableRow($i);
	        }
    	}
    }
    
    private function createTableBody($table) {
        $this->tmp_node->appendChild(
                $this->doc->createElement("TBODY")
        );
        $y_count = $table->getMaxY();
        $anz_header = $table->getAnzHeader();
        for ($i=$anz_header; $i <= $y_count; $i++) {
            $this->createTableRow($i, $table);
        }
        
    }
    private function createTableRow($y, $table) {
        $this->tmp_node->lastChild->appendChild(
                $this->doc->createElement("TR")
        		);
        $table->getCells();
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
                return;
            }
            if($cell->getFormat()=="") {
                $fragment = $this->doc->createDocumentFragment();
                $fragment->appendXML($cell->getValue());
                $cell_node->appendChild($fragment);
            } else {
                $fragment = $this->doc->createDocumentFragment();
                $fragment->appendXML($cell->getValue());
                    if($cell->getFormat()=="bold") {
                            $format = $this->doc->createElement("b");				
                    } else if($cell->getFormat()=="italic") {
                            $format = $this->doc->createElement("i");
                    } else {
                            die("Das Format ist Bundesanzeiger->createCell nicht bekannt.");
                    }
                $format->appendChild($fragment);
                $cell_node->appendChild($format);

            }
            //$this->table_node = new DOMElement();
            $this->tmp_node->lastChild->lastChild->appendChild($cell_node);
            //$this->doc = new DOMDocument();
            //$this->doc->appendChild($cell_node);

        /*
         * reminder:
         * http://php.net/manual/de/domdocumentfragment.appendxml.php
         */
        //$content = $this->doc->createDocumentFragment();
        

    }
     
    public function output() {
        return $this->table_nodes;
    }
    
}
