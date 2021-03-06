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
    
    /*
     * append tag to a function(Template). This way when ever a tag appears
     * the registered function should tell what to do with it.
     */
    public static function appendTemplate($tagname, $staticreference) {
            if(!is_string($staticreference)) {
                    //example	:	call_user_func('MyClass::myCallbackMethod');
                    die( 'appendTemplate($tagname, $staticreference) '
                                    .'$staticreference has to be a string like'
                                    .'MyClass::MyMethod()');
            }
            self::$templates[$tagname]=$staticreference;

    }
    
    /*
     * 
     */
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
                 * Attributes
                foreach($node->attributes() as $attribute_name=>$attribute_value) {

                    echo '['.$attribute_name.":".$attribute_value.'] <br />';
                }
                */

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
    
        /*
     * The actual Call of the template, the tag-node is given as Parameter.
     * If a template is assigned to it, call this template/function
     * 
     */
    public static function applyMatchingTemplate($node) {
            // Type 2: Static class method call
            foreach (self::$templates as $tag=>$function) {
                    if($node->getName()==$tag) {
                            call_user_func($function, $node);
                            //return;
                    }
            }

    }

}

?>
