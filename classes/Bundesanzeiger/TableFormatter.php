<?php

/**
 * Description of Bundesanzeiger
 *
 * @author fischer
 */
class TableFormatter {
    //the table has to be converted, to suite the Restrictions of the Bundesanzeiger
    CONST maxColls = 6;
    
    
    
    public function formatTable($table) {
    	$this->RemoveEmptyColumns($table);
    	$this->RemoveEmptyRows($table);
    	
    }
    
    private function RemoveEmptyRows($table) {
    	$y_max = $table->getMaxY();
    	for($y = 0; $y <= $y_max; $y++) {
    		$b_empty_row = $this->checkifRowisEmpty($y, $table);
    		if($b_empty_row == true) {
    			$table->deleteRow($y);
    			$y_max--;
                        $y--;
    		}
    	}
    	
    }
    private function RemoveEmptyColumns($table) {
    	$x_max = $table->getMaxX();
    	for ($x=0; $x <= $x_max; $x++) {
    		$b_empty_columns = $this->checkifColumnsAreEmpty($x, $table);
    		if($b_empty_columns == true) {
    			$table->deleteColumn($x);
    			$x_max--;
    		}
    		
    	}
    	 
    }
    
    private function checkifColumnsAreEmpty($x, $table) {

        $zellen = $table->getCells();
        $y_max = $table->getMaxY();
    	for ($y=0; $y < $y_max; $y++) {
    		
    		if(($zellen[$y][$x]!=" ") && ($zellen[$y][$x] != false) &&
    				($zellen[$y][$x]->getValue() != "") &&($zellen[$y][$x]->getValue()!=NULL)) {
    			return false;
    		}
    		
    	}
		if($table->columnhascolspan($x)) {
    		return false;
		}
    	return true;
    }
    
    private function checkifRowisEmpty($y, $table) {
    	$zellen = $table->getCells();
    	
    	$x_max = $table->getMaxX();
    	for ($x=0; $x <= $x_max; $x++) {
    		$zelle = $zellen[$y][$x];

    		//check if empty cell
    		if(!empty($zelle)) {

                    if((!empty($zelle->getValue())) && 
                            ((strlen(trim($zelle->getValue()))) > 0)) {
                            return false;
    			}
    		}
    				
    	}
    	if($table->rowhasrowspan($y)) {
    		return false;
    	}
    	return true;
    	 
    }
    
    
    public function splitLargeTables($table) {
        //if not large table, push current table and return
        if(!$this->checkifLargeTable($table)) {
                $tables = array();
                array_push($tables, $table);
        	return $tables;
        }
        
        
        $max_x = $table->getMaxX();

        //calculate seperated Tables
        $splittableColumns = $this->getSplittablesColumns($table);
        $col_min = 0;
        $col_max = self::maxColls;
        
        $ben_tabellen = ceil($max_x / self::maxColls);
        $tabl_range = array();
        
        do {
            //echo "col_min:".$col_min." col_max:".$col_max." <br />";
            //echo "ben_tabellen:".$ben_tabellen."<br />";
            $col_max = $this->getClosest2MaxColl($splittableColumns, $col_min, $col_max, $table);
            if($col_max == -1) {
                echo "<span style=\"color:red\">";
                echo "Couldn\'t split Table";
                echo "</span>";
                $col_max = $max_x;
                array_push($tabl_range, array("col_min"=>$col_min, "col_max"=>$col_max));
                break;
            }
            array_push($tabl_range, array("col_min"=>$col_min, "col_max"=>$col_max));
                
            $col_min = $col_max+1;
            $col_max = $col_min + self::maxColls;
            
            if($col_max > $max_x) {
                $col_max = $max_x;
            }

            
            $ben_tabellen--;

            
        } while($ben_tabellen >= 1);

        $tables = array();
        foreach($tabl_range as $range) {
            $table_tmp = new Tabelle(0,0);
            $table_tmp = clone $table;

            
            $table_tmp->deleteColumnsExceptfromCol1toCol2(
                    $range["col_min"],
                    $range["col_max"]
                    );
            array_push($tables, $table_tmp);
            
        }
        return $tables;
        
        
    }
    private function getClosest2MaxColl($ar_columns, $col_min, $col_max, $table) {
        
        //Validation
        if(!is_numeric($col_min)||(!is_numeric($col_max))) {
            die("col_min und col_max müssen eine Zahl sein");
        }
        if(!is_array($ar_columns)) {
            die("ar_columns muss ein array sein.");
        }
        foreach($ar_columns as $column) {
            if(!is_numeric($column)) {
                die("in dem Array ar_columns dürfen nur Zahlen enthalten sein.");
            }
        }
        $maxX = $table->getMaxX();
        $rest = fmod($col_max-$col_min, self::maxColls);
        if(($rest > 0)&&($maxX != $col_max)) {
            die("col_max muss entweder so groß sein, wie die Anzahl der Tabellenspalten oder"
                . "muss ein multiplikant von self::maxColls sein.");
        }
        if($col_max > $maxX) {
            die("col_max darf nicht größer sein, als die Anzahl der Tabellenspalten.");
        }
        //Keine Notwendigkeit die Tabelle aufzutrennen
        if($col_max == $maxX) {
            return $col_max;
        }
        
        //Processing
        $max = -1;
        rsort($ar_columns);
        foreach($ar_columns as $value) {
            if(($value <= $col_max)&&($max < $value)) {
                $max = $value;
            }
        }
        return $max;
    }
    
    private function getSplittablesColumns($table) {
        $zellen = $table->getCells();
        $max_x = $table->getMaxX();
        $splittableColumns = array();
        
        for($x=0; $x < $max_x; $x++) {
            //Nicht Spaltenanfang hinzufügen
            if($x == 0)
                continue;
            if($this->checkIfSplittable($x, $table)) {
                array_push($splittableColumns, $x);
            }
        }
        return $splittableColumns;
    }
    private function checkIfSplittable($x, $table) {
        $x++;
        $zellen = $table->getCells();
        $max_y = $table->getMaxY();
        $b_splittable = true;
        for($y=0; $y <= $max_y; $y++) {
            
            $zelle=$zellen[$y][$x];
            if(!is_a($zelle, "Zelle")) {
                $b_splittable = false;
                break;
            }
        }
        return $b_splittable;
    }
    

    private function checkifLargeTable($table) {
    	if($table->getMaxX() > self::maxColls) {
			return true;
		}
		return false;	
    }
    

    
}
