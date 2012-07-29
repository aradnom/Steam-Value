<?php
/***************************************
@desc This class file is used for generation of commong form elements
I find myself using a lot
@author Tim Shaw
@date 7/30/2010
***************************************/

class form_tools {
	// Setting up default variables
	private $submit_type = "submit";
	private $submit_value = "Send";
	private $action = "";
	private $method = "post";
	private $form_code; // Box variable for holding generated form code
	
	public function generate_submit_button ( $submit_id, $submit_value, $submit_type, $form_label, $action, $method ) {
		// Should probably just deprecate this...
		if($submit_type){
			$this->submit_type = $submit_type;
		}
		if($action){
			$this->action = $action;
		}
		if($method){
			$this->method = $method;
		}
		$this->form_code ="
<form name='$form_label' id='$form_label' action='$this->action' method='$this->method'>
<input type='$this->submit_type' name='$submit_id' id='$submit_id' value='$submit_value' />
</form>  		
		";
		
		return $this->form_code;
	}
	
	public function generate_custom_form ( $fields = '', $form_label = '', $action = '', $method = '', $enctype = '' ) {
		// Enctype for file uploads: multipart/form-data
		
		$input_fields = ""; // Empty string for input elements	
		
		// Setting up default variables
		if($action){
			$this->action = $action;
		}
		if($method){
			$this->method = $method;
		}
		
		// Processing fields array
		foreach($fields as $row){
			$field = explode('|', $row);
			
			if(count($field) > 1){
				$input_fields .= "<input type='$field[0]' id='$field[1]' name='$field[1]' value='$field[2]' />"."\n";
			} else {
				$input_fields .= $row;
			}			
		}
		
		$this->form_code = "<form name='$form_label' id='$form_label' action='$this->action' method='$this->method' enctype='".$enctype."'>\n".$input_fields."</form>\n";
		
		return $this->form_code;
	}
}
?>