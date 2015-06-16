<?php
require_once './classes/fileparser.php';

/**
 * Klasse für den Datenzugriff
 */
class Model{ //XMLProductKalkulatorModel()
        private static $directory = "./xml_data/input/";
        private static $xmlparser;

        
        
	public static function getXMLfiles(){
            $handle = opendir(self::$directory);
            if ($handle) {
                
                $i = 0;
                while (false !== ($file = readdir($handle))) {
                    if(FileParser::getExtension($file)=="xml") {
                        $filenames[$i] = $file;
                    }
                    $i++;
                    
                } 
		return $filenames;
            }
        }


}
?>
