<?php
	class Debughelper {
            const DEBUG = true;
		public static function myprint_r($var) {
                    if(self::DEBUG==true) {
						$callers=debug_backtrace();
	                    echo "class: ".$callers[1]["class"];
	                    echo "  ";
	                    echo "method: ".$callers[1]["function"];
	                    echo "<br />";
						echo "<pre>";
						print_r($var);
						echo "</pre>";
                    }
		}
                public static function myecho($var) {
                    if(self::DEBUG == true) {
						$callers=debug_backtrace();
	                    echo "class: ".$callers[1]["class"];
	                    echo "  ";
	                    echo "method: ".$callers[1]["function"];
	                    echo "<br />";
                    	echo $var;
                    }
                }
	}