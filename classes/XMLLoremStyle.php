<?php
	
include ('./classes/table/Tabelle.php');
include('./classes/Bundesanzeiger/TableXML.php');

class XMLLoremStyle {
	private static $output_doc;
        private static $html_result;
	public static function init() {
            //AppendFunctionstoTemplate
            
            XMLTransformer::appendTemplate("A", "XMLProcessor::Absatz");
            XMLTransformer::appendTemplate("b", "XMLProcessor::Bold");
            XMLTransformer::appendTemplate("i", "XMLProcessor::Kursiv");
            XMLTransformer::appendTemplate("BerichtsteilUeberschrift", "XMLProcessor::Berichtsteil");
            XMLTransformer::appendTemplate("Zwischentitel", "XMLProcessor::Zwischentitel");
            XMLTransformer::appendTemplate("LI", "XMLProcessor::Liste");
            XMLTransformer::appendTemplate("L-Titel", "XMLProcessor::LTitel");
            XMLTransformer::appendTemplate("Tabelle", "XMLProcessor::initTabelle");
            

            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->xmlStandalone = false;
            $dom->load("info.xml");

            self::$output_doc =$dom;
	}

	
	/*
	 * 
	 * Ausgabe
	 * 
	 */
	public static function output() {
            self::$output_doc->saveXML();
            self::$output_doc->preserveWhiteSpace = false;
            self::$output_doc->formatOutput = true;
            

            self::$output_doc->save("write.xml");
            
            return self::$html_result;
		
	}
	
	/*
	 * 
	 * XML-Templates
	 * 
	 */	
	/*
	 * Absatz
	 */
	public static function Absatz($node) {
		//Create Element and Append it to output-Dom
		$absatz = self::$output_doc->createElement( "A" );
		self::$output_doc->lastChild->appendChild( $absatz );
		//set Text of Node
		$absatz->appendChild(
				self::$output_doc->createTextNode( $node->nodeValue )
		);
		self::$html_result.="<p>".$node->nodeValue."</p>";
                
	}
	/*
	 * Absatz
	 */
	public static function Berichtsteil($node) {
		$absatz = self::$output_doc->createElement( "BerichtsteilUeberschrift" );
		self::$output_doc->lastChild->appendChild( $absatz );
		$absatz->appendChild(
				self::$output_doc->createTextNode( $node->nodeValue )
		);
		self::$html_result.="<h2>".$node->nodeValue."</h2>";
	}
	/*
	 * Zwischentitel
	 */
	public static function Zwischentitel($node) {
		$absatz = self::$output_doc->createElement( "Zwischentitel" );
		self::$output_doc->lastChild->appendChild( $absatz );
		$absatz->appendChild(
				self::$output_doc->createTextNode( $node->nodeValue )
		);
		self::$html_result.="<h3>".$node->nodeValue."</h3>";
	}
        
	/*
	 * Liste
	*/ 
	public static function Liste($node) {
		$absatz = self::$output_doc->createElement( "A" );
		self::$output_doc->lastChild->appendChild( $absatz );
		$absatz->appendChild(
				self::$output_doc->createTextNode( " • ".$node->nodeValue )
		);
		self::$html_result.="<p>"." • ".$node->nodeValue."</p>";
	}
    /*
	 * Kursiv
	 */
	public static function Kursiv($node) {
		$absatz = self::$output_doc->createElement( "i" );
		self::$output_doc->lastChild->lastChild->appendChild( $absatz );
		$absatz->appendChild(
				self::$output_doc->createTextNode( $node->nodeValue )
		);
		self::$html_result.="<i>".$node->nodeValue."</i>";
	}
	/*
	 * Bold
	 */
	public static function Bold($node) {
		$absatz = self::$output_doc->createElement( "b" );
		self::$output_doc->lastChild->lastChild->appendChild( $absatz );
		$absatz->appendChild(
				self::$output_doc->createTextNode( $node->nodeValue )
		);
		self::$html_result.="<b>".$node->nodeValue."</b>";
	}

	/*
	 * LTitel
	 */
	public static function LTitel($node) {
		$absatz = self::$output_doc->createElement( "L-Titel" );
		self::$output_doc->lastChild->appendChild( $absatz );
                
		$absatz->appendChild(self::$output_doc->createTextNode( $node->nodeValue ));
	}
	
	/*
	 * Tabelle
	 */
	public static function initTabelle(DOMElement $node) {
            $max_x = $node->getAttribute("aid:tcols");
            $max_y = $node->getAttribute("aid:trows");

            $table = new Tabelle($max_x, $max_y);

            //for looping with iterator - $x and $y
            $cells = $node;
        		
            foreach($cells as $cell) {

                $cell_o = self::Zelle($cell);
                $anz_headerzeilen =0;
                $str_format = "TBODY";
                $zeilentyp = "TBODY";
                list($x, $y) = $table->addCell($cell_o);

                if($cell->children()!=NULL) {
                        $contentnodes = $cell->children();
                        foreach($contentnodes as $contentnode) {
                                $str_format = $contentnode->getName();
                                $zeilentyp = self::getZeilentyp($str_format);
                                if($zeilentyp=="THEAD") {
                                    $anz_headerzeilen = $y+1;
                                }
                                break;
                        }
                        $table->setAnzHeader($anz_headerzeilen);
                }

            }
        
        
        
            /*
             * Output
             */
            $eBanXML = new TableXML();
            $doc = & self::$output_doc;
            $tablenodes = $eBanXML->createTableNode($table, $doc);

            foreach($tablenodes as $tablenode) {
                self::$output_doc->lastChild->appendChild($tablenode);
            }
            self::$html_result.= $table->toHTML();	
	}
	
	public static function Zelle(DOMElement $cell) {
		
                $cols = $cell->getAttribute("aid:ccols");
                $rows = $cell->getAttribute("aid:crows");

                //Default values
		$align="left";
		$format="";
		$value="";
		$contentnodes = $cell->children();
		$i=0;
		foreach($contentnodes as $contentnode) {
			if($i>0) {
				$value.="<br />";
			}
		
			$str_formatnode = $contentnode->getName();
			$align = self::getAlignment($str_formatnode);
			$format = self::getFormat($str_formatnode);
			$value.=$contentnode;		
			$i++;
		}

		$cell_o = new Zelle();
		$cell_o->setFormat($format);
		$cell_o->setAlign($align);
		$cell_o->setColspan($cols);
		$cell_o->setRowspan($rows);
		$cell_o->setValue($value);
		
		
		return $cell_o;
	}
	
	public static function getAlignment($str_format) {
		if (strpos($str_format,'left') !== false) {
			return 'left';
		}
		else if (strpos($str_format,'right') !== false) {
			return 'right';
		}
		else if (strpos($str_format,'center') !== false) {
			return 'center';
		}
		else {
			return 'left';
		}
	}
	public static function getFormat($str_format) {
		if (strpos($str_format, 'bold')) {
			return 'bold';
		} else if(strpos($str_format, 'italic')) {
			return 'italic';
		} else {
			return '';
		}
	}
	
	public static function getZeilentyp($str_format) {
		if(strpos($str_format, 'THEAD')) {
			return 'THEAD';
		} else if(strpos($str_format, 'TBODY')) {
			return 'TBODY';
		} else {
			return 'TBODY';
		}
	}
        
        /**
        * function xml2array
        *
        * This function is part of the PHP manual.
        *
        * The PHP manual text and comments are covered by the Creative Commons 
        * Attribution 3.0 License, copyright (c) the PHP Documentation Group
        *
        * @author  k dot antczak at livedata dot pl
        * @date    2011-04-22 06:08 UTC
        * @link    http://www.php.net/manual/en/ref.simplexml.php#103617
        * @license http://www.php.net/license/index.php#doc-lic
        * @license http://creativecommons.org/licenses/by/3.0/
        * @license CC-BY-3.0 <http://spdx.org/licenses/CC-BY-3.0>
        */
    private static function node2array($xmlnode, $recursion = null) {
    	$xmlarray = array();
        foreach ($xmlnode as $xmlobject) {
        	$tagname = $xmlobject->getName();
            $xmlarray["tagname"] = $tagname; 
                
                
            /*
            * Attributes
            */
            if($xmlobject->attributes()) {
            	$xmlarray["attributes"] = array();
                foreach($xmlobject->attributes() as $attribute_name=>$attribute_value) {
                	$xmlarray["attributes"][$attribute_name] = $attribute_value;
                }
            }
                
            if(($xmlobject->children()!=NULL)
                && (($recursion > 0) || ($recursion == NULL))) {
                    
                $childarray = self::node2array(
                $xmlobject->children(),
                	$recursion-1
                );
                $xmlarray["children"] = $childarray;
                //Debughelper::myprint_r($xmlarray["children"]);
                    
            }
		}
    	return $xmlarray;
    }
    
    private static function xml2array ( $xmlObject, $out = array () ) {
    foreach ( (array) $xmlObject as $index => $node ) {
        $out[$index] = ( is_object ( $node ) ) ? self::xml2array ( $node ) : $node;
    }
    return $out;
}
}

?>

