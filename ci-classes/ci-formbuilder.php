<?php
/* 
	Class Name: CI Form Builder
   	Class URI: http://cideator.com/php/classes/ci-formbuilder
   	Description: a plugin to create awesomeness and spread joy
   	Version: 1.0.0
   	Author: Farhan Wazir
	Author Email: seejee1@gmail.com
   	Author URI: http://cideator.com
   	License: GNU General Public License, version 2 (GPL2)
 
 	Copyright 2014 Farhan Wazir (email : seejee1@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
class ciformbuilder{
	private $form_tag = '<div class="ci-form"><h2 class="ci-heading">{label}</h2>{desc}<form %s>{fields}</form></div>';
	private $form_attr;
	private $grp_format = '<div %s class="ci-group">{label}{desc}{inner}</div>';
	private $field_label_format = '<span class="ci-label">%s</span>';
	private $desc_format = '<span class="ci-desc">%s</span>';
	private $default_grp = 'fields';
	private $groups = array(), $fields = array();
	private $fields_counter = 0;
	private $custom_func = array();
	private $prefix = 'ci_';
	private $debug;
	
	public function __construct($attr = false){
		//form tag attributes as array key
		//custom attributes also add able using 'custom' key
		if(is_array($attr)) $this->addFormAttr($attr);
	}
	
	/****************************************** general functions */
	//function tells you  number is odd or even
	private function numIsOdd($num = 0){
		$val = $num / 2;
		return floor($val) == $val;
	}
	
	//function use for filtering help contents like field/form/group label and description/help
	private function filterHelper($type, $format, $args){
		$label = (is_array($args) && array_key_exists('label', $args))? ($type == 'field')? sprintf($this->field_label_format, $args['label']) : $args['label']: '';
		$desc = (is_array($args) && array_key_exists('help', $args))? sprintf($this->desc_format, $args['help']): ((is_array($args) && array_key_exists(0, $args))? sprintf($this->desc_format, $args[0]): ((is_string($args))? sprintf($this->desc_format, $args): ''));
		
		return str_replace(array('{label}', '{desc}'), array($label, $desc), $format);
	}
	
	//this will return tag attributes in string format like <form method="get" action="ci-submission.php" so on...
	private function formatAttr($attr){
		$output = '';
		foreach($attr as $key => $val){
			$output .= (is_numeric($key))? $val : $key .'="'. $val .'"';
			//adding space between tag attributes
			$output .= ' ';
		}
		return $output;
	}
	
	/****************************************** Functions for execute external custom functions defined by user */
	function customFunction($func_name, $args = ''){
		ob_start();
		$func_name( ((is_array($args))? implode(',', $args) : $args) );
		$output = ob_get_clean();
		array_push($this->custom_func, $output);
	}
	
	private function customFuncExec($print = false){
		$output = $this->groups['customfunc'] = implode(' ', $this->custom_func);
		if($print) return $output;
	}
	
	function execCustomFunc(){
		echo $this->customFuncExec(true);
	}
	
	/****************************************** form fields related functions */	
	//function return pre-defined formats of fields
	private function fieldStruct($type = 'text'){
		$fields['text'] = '<label>{label}<input type="'.$type.'" %s /></label>{desc}';
		$fields['radio'] = '<label><input type="'.$type.'" %s />{label}</label>{desc}';
		$fields['checkbox'] = $fields['radio'];
		$fields['select'] = '<label>{label}<select %s>{inner}</select></label>{desc}';
		$fields['textarea'] = '<label>{label}<textarea %s>{inner}</textarea></label>{desc}';
		return (array_key_exists($type, $fields))? $fields[$type] : $fields['text'];
	}
	
	//prepareField function is bridge between formatAttr and filterHelper function for fields only.
	private function prepareField($type = 'text', $args){
		
		$format = $this->fieldStruct($type);
		
		//filter helper object in args
		$helper = (array_key_exists('helper', $args))? $args['helper'] : false;
		unset($args['helper']);
		
		//use filterHelper function for printing label and field description and then return to caller.
		return $this->filterHelper('field', sprintf($format, $this->formatAttr($args)), $helper);
	}
	
	//this is simple function for adding pre-defined css class in field div container tag
	private function fieldRowStyles($num = 0){
		$styles = "form-row ";
		if($this->numIsOdd($num)) $styles .= "even ";
		else $styles .= "odd ";
		return $styles;
	}
	
	/****************************************** Form group of fields functions */
	//section/group of fields
	function addGroup($args=false){
		$this->createGroup($args);
	}
	function createGroup($args=false){
		$id = (is_array($args) && array_key_exists('id', $args))? $args['id']: $this->default_grp;
		$label = (is_array($args['helper']) && array_key_exists('label', $args['helper']))? $args['helper']['label'] = '<h3 class="ci-heading">'.$args['helper']['label'].'</h3>': '';
		//$desc =(is_array($args['helper']) && array_key_exists('desc',$args['helper']))? sprintf($this->desc_format, $args['helper']['desc']):'';
		$this->groups[$id] = $this->filterHelper('group', sprintf($this->grp_format, 'id="'.$this->prefix.$id.'"'), $args['helper']);		
	}
	
	private function margeFieldsGroup(){
		foreach($this->fields as $grp => $fields){
			if(!array_key_exists($grp, $this->groups)) $this->createGroup(array('id' => $grp));
			$this->groups[$grp] = str_replace('{inner}', implode('', $fields), $this->groups[$grp]);
		}
		return is_array($this->groups)? implode(' ', $this->groups) : '';
	}
	
	//Create field function is only create an individual field and return created field
	function createField($args = false){
		echo $this->addField($args, true);
	}
	
	//Add Field function for adding field in fieldset for form, it will not returns any value 
	function addField($args = false, $return_field = false){
		/*
		- id = use for array key and field attribute
		- class = style class
		- type = text, checkbox, radio, list and textarea
		- name = name of field
		- other any custom attribute
		*/
		$this->fields_counter++;
		$id = $row_id = 'form-field-row-'.$this->fields_counter;
		
		$output = '<div id="'.$row_id.'" class="'. $this->fieldRowStyles($this->fields_counter) .' %s">{inner}</div>';
		
		if( !is_array($args) || !array_key_exists('type', $args) ) 
		return $this->fields[$row_id] = str_replace('{inner}',
			'<span class="error">Field not published due to error!</span> <span class="error-desc">Arguments is not well formatted.</span>',
			$output);
		
		$group = (array_key_exists('group', $args))? $args['group'] : $this->default_grp;
		//remove group key from coming arguments via this function
		unset($args['group']);
		
		$selected = (array_key_exists('selected', $args))? $args['selected'] : '';
		//remove value key from coming arguments via this function
		unset($args['selected']);
		
		$value = (array_key_exists('value', $args))? $args['value'] : '';
		//remove value key from coming arguments via this function
		unset($args['value']);
		
		$type = $args['type'];
		//remove type attribute
		unset($args['type']);
		
		//add class in field(s) row
		$output = sprintf($output, $type.'-row');
		
		switch($type){
			case 'list':
			case 'select':
				//check if user want list and multiple attribute is not set by user then, code add attribute for multi selection automatically
				if($type == 'list' && is_bool(array_search('multiple', $args))) array_push($args, 'multiple');
				
				$value_format = '<option value="%s" {selected} >%s</option>';
				$nval = '';
				
				if(is_array($value)) foreach($value as $key => $val) $nval .= str_replace('{selected}', (!is_bool(array_search($key, $selected)))? ' selected ': '', sprintf($value_format, $key, $val));
				
				$value = $nval;
				
				$output = str_replace('{inner}', /* this function use for adding field in row format which we defined above in function */ 
				/* print value in field, prepareField will be return format where {inner} string exists for field values. */
				str_replace('{inner}', $value, $this->prepareField('select', $args) ),
				/* defined row format in begining of this function */ 
				$output);
				break;
				
			case 'radio':
			case 'checkbox':
				$rc_format = '{label} %s {desc}';
				$fields = '';
				$rc_format = $this->filterHelper('field', $rc_format, ( (array_key_exists('helper', $args))? $args['helper']: false) );
				unset($args['helper']);
				if(is_array($value)) foreach($value as $key => $val){
										$args['value'] = $key;
										$args['helper']['label'] = $val;
										$args[29] = (is_bool(array_search($args['value'], $selected)))? ' ': ' checked ';
										$fields .= $this->prepareField($type, $args);
										unset($args[29]);
									}
				else $args['value'] = $value;
				$output = str_replace('{inner}', sprintf($rc_format, (($fields == '')? $this->prepareField($type, $args): $fields)), $output);
				break;
			
			case 'textarea':
			case 'comment':
				$output = str_replace('{inner}', str_replace('{inner}', $value, $this->prepareField('textarea', $args) ), $output);
				break;
			
			case 'string':
			case 'text':
				$args['value'] = $value;
				$output = str_replace('{inner}', $this->prepareField('text', $args), $output);
				break;
			
			case 'upload':
			case 'file':
				$output = str_replace('{inner}', $this->prepareField('file', $args), $output);
				break;
			
			case 'invisible':
			case 'hidden':
				$output = str_replace('{inner}', $this->prepareField('hidden', $args), $output);
				break;
			
			default:
				$args['value'] = $value;
				$output = str_replace('{inner}', $this->prepareField($type, $args), $output);
				break;
			
		}
		
		if($return_field) return $output;
		$this->fields[$group][$id] = $output;
	}
	
	/****************************************** button functions */
	function addButton($args){
		$this->addField($args);
	}
	function createButton($args){
		echo $this->addField($args, true);
	}
	
	/****************************************** form functions */
	private function formatFormAttr($format){
		$attr = (count($this->form_attr) < 1)? '': $this->form_attr;
		
		$helper = (($attr != '') && (array_key_exists('helper', $attr)))? $attr['helper'] : false;
		unset($attr['helper']);
		
		//setting up form tag attribute into string from array
		$this->form_attr = $this->formatAttr($attr);				
		//marge attributes in tag
		return $this->form_tag = sprintf($this->filterHelper('form', $format, $helper), $this->form_attr = $this->formatAttr($attr));
	}
	
	function addFormAttr($attr = false){
		if(is_array($attr)) foreach($attr as $key => $val) $this->form_attr[$key] = $val;
	}
	
	private function prepareForm(){
		$this->customFuncExec();
		//prepare all fields in group as per our form format and marge form fields array in fields group.
		$this->margeFieldsGroup();
		return str_replace('{fields}', 
		/* groups array convert into html tags for inserting in form tag */
		$this->margeFieldsGroup(), $this->formatFormAttr($this->form_tag) );
	}
	
	
	/****************************************** finally publisher function of class */
	//if argument will fields then only fields of form in result otherwise full form with fields will be in result.
	function form($customize = false){	
		
		$output = $this->prepareForm();
		
		switch($customize){
			default:
				break;
			case 'fields':
				$output = $this->margeFieldsGroup();
				break;
		}
		echo $output;
	}
}
?>