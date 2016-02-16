<?php
require_once './classes/fileparser.php';
require_once './classes/XMLTransformer.php';
require_once './classes/XMLProcessor.php';


/**
 * Klasse für den Datenzugriff
 */
class XMLFileModel{
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
                asort($filenames);
                
		return $filenames;
            }
        }
        
        public static function readxml($filename) {
	XMLProcessor::init();
	XMLTransformer::getXMLdata($filename);
        return XMLProcessor::output();
}


}
?>
