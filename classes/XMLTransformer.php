<?php
Class XMLTransformer {
    private static $directory = "./xml_data/input/";
    private static $templates = array();
    private static $xmlparser;
    private static $MAXRECURSION = 6;
	private static $tmp_recursion = 0;
    /*
     * Analyses XML-Structure
     * returns an Array 
     */
	public static function appendTemplate($tagname, $staticreference) {
		if(!is_string($staticreference)||(!is_string($tagname))) {
			//example:call_user_func('MyClass::myCallbackMethod');
			die( 'appendTemplate($tagname, $staticreference) '
			 		.'$staticreference has to be a string like'
					.'MyClass::MyMethod()');
		}
		self::$templates[$tagname]=$staticreference;

	}

        public static function applyMatchingTemplate($node) {
		// Type 2: Static class method call
            //$node= new DOMDocument();
		foreach (self::$templates as $tag=>$function) {
			if($node->nodeName==$tag) {
				call_user_func($function, $node);
				//return;
			}
		}
		
	}
	
	
	public static function getXMLdata($filename){
		$filepath = self::$directory.$filename;
		
                $dom = new DOMDocument();
                $options = NULL;
                $dom->substituteEntities = TRUE;
                $dom->load($filepath);
                self::$xmlparser = $dom;    //simplexml_load_file($filepath); 
                
		$xmlarray = Array();
                $xmlarray = self::readXML(self::$xmlparser->childNodes);
		
		return $xmlarray;
	}
        
        private static function hasAppendedTemplates() {
            return count(self::$templates) > 0 ? true:false;
        }
        private static function readXML($xmlnode) {
            if(!self::hasAppendedTemplates()) {
                throw new RuntimeException("No Stylesheet-Templates Appended");
            }

            foreach ($xmlnode as $node) {
                    //$node = new DOMDocument();
                self::applyMatchingTemplate($node);
                    
                    //Read Childrens
                    if((!empty($node->childNodes)) && (self::$tmp_recursion < self::$MAXRECURSION)) {
                        self::$tmp_recursion++;
                        self::readXML($node->childNodes);
                        self::$tmp_recursion--;
                    }//Node

                }//endif xmlnode
        }
}

?>
