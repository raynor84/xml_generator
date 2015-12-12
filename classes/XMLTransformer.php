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
		foreach (self::$templates as $tag=>$function) {
			if($node->getName()==$tag) {
				call_user_func($function, $node);
				//return;
			}
		}
		
	}
	
	
	public static function getXMLdata($filename){
		$filepath = self::$directory.$filename;
		
		$xmlarray = Array();
		self::$xmlparser = simplexml_load_file($filepath);
		$xmlarray = self::readXML(self::$xmlparser);
		XMLProcessor::output();
		
		return $xmlarray;
	}
        
        
        private static function readXML($xmlnode) {
                $xmlarray = array();

                foreach ($xmlnode as $xmlobject) {
                    $node = $xmlobject;
                    self::applyMatchingTemplate($node);

                    /*
                     * Output Children
                     * 
                    */
                    if(($node->children()!=NULL) 
                            && (self::$tmp_recursion < self::$MAXRECURSION)) {
                            self::$tmp_recursion++;
                                    self::readXML($xmlobject->children());
                            self::$tmp_recursion--;
                    }//Node

                }//endif xmlnode
        }
}

?>
