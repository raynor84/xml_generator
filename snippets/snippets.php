<?php
/*
 * Refactor
*/
function setIndesignAlign($str_properties) {

	if($str_properties=="THEAD") {
		$this->set_align("left");
	}
	else if($str_properties=="THEAD_bold") {
		$this->set_align("left");
		$this->set_format("b");
	}
	else if($str_properties=="THEAD_left") {
		$this->set_align("left");
	}
	else if($str_properties=="THEAD_left_bold") {
		$this->set_align("left");
		$this->set_format("b");
	}
	else if($str_properties=="THEAD_center") {
		$this->set_align("center");
	}
	else if($str_properties=="THEAD_right") {
		$this->set_align("right");
	}
	else if($str_properties=="THEAD_right_bold") {
		$this->set_align("right");
		$this->set_format("b");
	}
	else if($str_properties=="TBODY") {
		$this->set_align("left");
	}
	else if($str_properties=="TBODY_bold") {
		$this->set_align("left");
		$this->set_format("b");
	}
	else if($str_properties=="TBODY_left") {
		$this->set_align("left");
	}
	else if($str_properties=="TBODY_left_bold") {
		$this->set_align("left");
		$this->set_format("b");
	}
	else if($str_properties=="TBODY_right") {
		$this->set_align("right");
	}
	else if($str_properties=="TBODY_right_bold") {
		$this->set_align("right");
		$this->set_format("b");
	}
	else if($str_properties=="TBODY_right_ex") {
		$this->set_align("right");
		$this->set_format("ex");
	}
	else if($str_properties=="TBODY_left_ex") {
		$this->set_align("left");
		$this->set_format("ex");
	}
	else {
		$this->set_align("left");
	}

}
?>