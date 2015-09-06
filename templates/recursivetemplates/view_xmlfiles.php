<?php
function readxml($filename) {
	require_once './classes/XMLTransformer.php';
	require_once './classes/XMLProcessor.php';
	XMLProcessor::init();
	XMLTransformer::getXMLdata($filename);
        XMLProcessor::output();
}



?>
