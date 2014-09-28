<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CI-FormBuilder PHP Class Demo</title>
<link href="styles/ci-formbuilder.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!-- 
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

 -->

<?php

require "ci-classes/ci-formbuilder.php";

formPrint();

function formPrint(){
	
	echo '
	   	<div class="wrap">
		<h1>CI Form Builder PHP Class</h1>
	   This is Creative Ideator Form Builder, it generates form with less code and programmer friendly, <strong>No HTML involve just one code for each field</strong>. For demo: <a href="http://cideator.com/php/classes/ci-formbuilder">DEMO</a><br /><br />

Form builder is a best choice for Wordpress Developers. You can generate many settings, theme setting and frontend forms with less and friendly code. For help you can see source of index.php, it will help you more.
<br />
<h2>Usage:</h2>
Step 1: Just place ci-classes folder in your code.<br />
Step 2: Add line in your code <strong>require "ci-formbuilder.php";</strong><br />
Step 3: $ci_form = new ciformbuilder();
<br /><br />
Now start making form by class API. Below is EXAMPLE';
        
	$options = array( 'res' => 'Yes', 'showall' => 'No', 'width' => 439, 'height' => 200, 'nav' => 'Bottom');
	
	$ci_form = new ciformbuilder();
	$ci_form = new ciformbuilder( array(
									
									'method' => 'post',
									'helper' => array('label' => 'Apparence Setting')
									));
	$ci_form->addFormAttr(array("id" => "ci-test-formbuilder", "class" => "ci-formbuilder"));
	//$ci_form->customFunction('settings_fields', 'ci_slider_settings');
	
	$ci_form->addGroup(array(
									'id' => 'nav',
									'helper' => array('label' => 'Navigation',
														'help' => 'You can setting up your navigation.')
									));
	$ci_form->addGroup(array(
									'id' => 'responsive',
									'helper' => array('label' => 'Responsive')
									));
	$ci_form->addGroup(array(
									'id' => 'sizing',
									'helper' => array('label' => 'Sizing')
									));

	$ci_form->addField(array(
									'type' => 'radio',
									'name' => 'ci_slider_settings[res]',
									'id' => 'ci-responsive',
									'value' => array('No', 'Yes'),
									'selected' => array($options['res']),
									'group' => 'responsive',
									'helper' => array('label' => 'Responsive')
									));
	$ci_form->addField(array(
									'type' => 'radio',
									'name' => 'ci_slider_settings[showall]',
									'id' => 'ci-showall-contents',
									'value' => array('No', 'Yes'),
									'selected' => array($options['showall']),
									'group' => 'responsive',
									'helper' => array('label' => 'Show Contents',
														'help' => 'If YES then in mobile device navigation will hide and contents will show vertically.')
									));
	$ci_form->addField(array(
									'type' => 'string',
									'name' => 'ci_slider_settings[width]',
									'id' => 'ci-width',
									'value' => $options['width'],
									'group' => 'sizing',
									'helper' => array('label' => 'Width',
														'help' => 'If you selected responsive then no need, width must be in percent.')
									));
	$ci_form->addField(array(
									'type' => 'string',
									'name' => 'ci_slider_settings[height]',
									'id' => 'ci-height',
									'value' => $options['height'],
									'group' => 'sizing',
									'helper' => array('label' => 'Height',
														'help' => 'If you selected responsive then no need, height must be in percent.')
									));
	$ci_form->addField(array(
									'type' => 'select',
									'name' => 'ci_slider_settings[nav]',
									'id' => 'ci-nav',
									'value' => array('No Navigation', 'Top', 'Left', 'Bottom', 'Right', 'Center'),
									'selected' => array($options['nav']),
									'group' => 'nav',
									'helper' => array('label' => 'Navigation')
									));
	$ci_form->addButton(array(
									'type' => 'submit',
									'name' => 'submit',
									'id' => 'submit',
									'value' => 'Save Changes',
									'class' => 'button button-primary',
									));
	
	
	$ci_form->form();
	
?>
       </div>
<?php
}
?>
</body>
</html>