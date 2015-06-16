<?php
Class FileParser {

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    public static function splitFilename($filename) { 
        $pos = strrpos($filename, '.'); 
        if ($pos === false) 
        { // dot is not found in the filename 
            return array($filename, ''); // no extension 
        } 
        else 
        { 
            $basename = substr($filename, 0, $pos); 
            $extension = substr($filename, $pos+1); 
            return array($basename, $extension); 
        } 
    }
    
    public static function getExtension($filename) {
        $filesplit = self::splitFilename($filename);
        return $filesplit[1];
    }
}
?>
