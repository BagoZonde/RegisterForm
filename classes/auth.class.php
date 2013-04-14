<?php
	/**
	 * Interface for Auth class
	 */
	interface AuthInterface{
		function Solve();
		function Form();
		function Create();
		function Done();
		function Validate();
	}

	/**
	 * Class for displaying form, validate and register new members
	 */
	class Auth extends Sys implements AuthInterface{
		private $_config;
		private $_formID;
		private $_validationError;
		private $_data;

		/**
		 * Create an instance $_config, checking POST data (if any) and executes Solve method
		 * Setting $_config and $_formID
		 * 
		 * @param type $formID id of form used for AJAX and assigned for identification when receiving POST data
		 * @param array $config configuration containing fields and other parameters, @see __construct for details
		 * @throws Exception if any parameter is set unproperly, form will refuse to show or retrieve POST data
		 */
		public function __construct($formID, array $config){
			if (!$formID){
				throw new Exception('Form cannot be assigned without ID.');
			}else{
				$this->_formID=$formID;
			}
			if (!in_array(strtolower($config['method']), array('post', 'get'))){
				throw new Exception('Form method ' . ($config['method']?'"' . $config['method'] . '"':' '). 'not supported.');
			}
			if (!isset($config['target_id']) || !$config['target_id']){
				throw new Exception('Please define target_id for form.');
			}
			if (!isset($config['ajax_frame']) || !$config['ajax_frame']){
				throw new Exception('Please define ajax_frame for form.');
			}
			if (!isset($config['fields']) || !is_array($config['fields']) || sizeof($config['fields'])==0){
				throw new Exception('Please define fields for form.');
			}
			if (!isset($config['submit']) || !$config['submit']){
				$config['submit']='Ok';
			}
				
			$this->_config=$config;

			$this->Validate();
			$this->Solve(); //tutaj małe factory!!
		}

		/**
		 * Using $_validationError and $_process due to circumstances (POST sent or not)
		 * decision is taken: display form (for first time or for correction) or adding data to database & displaying message
		 */
		public function Solve(){
			if ($this->_validationError || $this->_process==false){
				$this->Form();
			}else if ($this->_process==true){
				$this->Create();
				$this->Done();
			}
		}

		/**
		 * Generate form regarding on $_config['fields'] data, also some jQuery code for AJAX
		 * is put for this action
		 */
		public function Form(){
			print '<form name="' . $this->_config['form_name'] . '" 
				id="' . $this->_config['form_id'] . '" 
				method="' . strtoupper($this->_config['method']) . '" 
				action="' . $this->_config['action'] . '">';
			print '<input type="hidden" name="formID" value="' . $this->_formID . '">';

			foreach($this->_config['fields'] as $id=>$field){
				print '<label>' . $field['label'] . ': </label>';
				$errorClass=isset($field['error'])?true:false;
				if ($field['type']=='text'){
					print '<input type="text" value="' . (isset($_POST[$id])?$_POST[$id]:'') . '" name="' . $id . '"';
					print $errorClass?' class="error"':'';
					print isset($field['placeholder'])?' placeholder="' . $field['placeholder'] . '"':'';
					print isset($field['maxlength'])?' maxlength="' . $field['maxlength'] . '"':'';
					print '><br />';
				}
				if ($field['type']=='password'){
					print '<input type="password" value="' . (isset($_POST[$id])?$_POST[$id]:'') . '" name="' . $id . '"';
					print $errorClass?' class="error"':'';
					print isset($field['maxlength'])?' maxlength="' . $field['maxlength'] . '"':'';
					print '><br />';
				}
				if ($field['type']=='select'){
					if (isset($field['options']) && is_array($field['options'])){
						print '<select name="' . $id . '"';
						print $errorClass?' class="error"':'';
						print '>';
						foreach($field['options'] as $option_id=>$option_name){
							print '<option' . (isset($_POST[$id]) && $_POST[$id]==$option_id?' selected="selected"':'');
							print ' value="' . $option_id . '">' . $option_name . '</option>';
						}
						print '</select><br />';
					}
				}
			}
			print '<label>&nbsp;</label><input type="submit" value="' . $this->_config['submit'] . '">';
			print '</form>';
			print <<<SCRIPT
<script type="text/javascript">
	$(function(){
		$('form#{$this->_config['form_id']} input[type=submit]').click(function(event){
			event.preventDefault();
		$.ajax({
			type:'POST',
			url: '{$this->_config['ajax_frame']}',
			data: $(this).parent().serialize(),
			success:function(data){
				$('#{$this->_config['target_id']}').html(data);
			}
		});
		});

	});
</script>
SCRIPT;
		}

		/**
		 * As all fields were validated, data is send to database
		 * This is a good place to insert additional information i.e. created date
		 */
		public function Create(){
			$this->_data['created']=date('Y-m-d H:i:s');
			Sys::InsertDB('users', $this->_data);
		}

		/**
		 * Well Done message after submit data to database
		 */
		public function Done(){
			print $this->_config['done'];
		}
		
		/**
		 * Validates all fields in iteration according to $_config['field'] data
		 * Setting $_validationError if some data was not entered properly
		 * Setting $_process to true if POST data for this $_formID sent
		 * @throws Exception
		 */
		public function Validate(){
			$this->_validationError=false;
			$this->_process=isset($_POST['formID'])==$this->_formID?true:false;
			if ($this->_process==true){
				$this->_data=array();
				foreach($this->_config['fields'] as $id=>$field){
					$thisField=&$this->_config['fields'][$id];
					if (isset($_POST[$id])){
						$this->_data[$id]=$_POST[$id];
						if (!isset($field['maxlength']) || !$field['maxlength']){
							$this->_validationError=true;
							throw new Exception('Please define maxlength value for ' . $id . ' field.');
						}
						$postValue=substr($_POST[$id], 0, $field['maxlength']);
						if (isset($field['regex']) && $field['regex']){
							if (!preg_match($field['regex'], $postValue)){
								$thisField['error']=1;
								$this->_validationError=true;
							}
						}
						if (isset($field['compare'])){
							if (strcmp($postValue, $_POST[$field['compare']])){
								$thisField['error']=1;
								$this->_validationError=true;
							}
						}
					}
				}
			}
		}
	}
