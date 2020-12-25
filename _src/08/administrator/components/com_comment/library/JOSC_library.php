<?php
defined('_JEXEC') or die('Restricted access');
/***************************************************************
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the Compojoom Comment project. The Compojoom Comment project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Description of JOSC_library
 *
 * @author Daniel Dimitrov
 */
class JOSC_library {

	public function getComponentList() {
		$list = array();
		$folderlist = JOSC_library::folderList(JPATH_COMPONENT_ADMINISTRATOR.DS.'plugin'.DS,false,true);
		foreach($folderlist as $com) {
			if ($com!='com_REPLACEnewplugin') {
				$list[] = JHTML::_('select.option', $com, $com, 'value', 'text' );
			}
		}
		return $list;
	}

	function readOnly($readonly)
	{
    	return ($readonly) ? " readonly='readonly' " : '';
	}

	function input($tag_name, $tag_attribs, $value, $readonly = false)
	{
    	$readonly = JOSC_library::readOnly($readonly);
    	return "<input name='$tag_name' type='text' $tag_attribs value='$value' $readonly/>";
	}

	function customRadioList( $tag_name, $tag_attribs, $selected, $yes=_CMN_YES, $no=_CMN_NO )
	{

		$arr = array(
			JHTML::_('select.option',  '0', $no ),
			JHTML::_('select.option',  '1', $yes )
		);

		return JHTML::_('select.radiolist',  $arr, $tag_name, $tag_attribs, 'value', 'text', (int) $selected );
	}

	function textarea($tag_name, $tag_attribs, $value, $readonly = false)
	{
	    $readonly = JOSC_library::readOnly($readonly);
	    return "<textarea name='$tag_name' $tag_attribs $readonly>$value</textarea>";
	}

	function hidden($tag_name, $value = '')
	{
    	return "<input type='hidden' name='$tag_name' value='$value' />";
	}


	function initVisibleJScript()
	{
?>
    	<script type='text/javascript'>
     		function JOSC_adminVisible(emptyvalue, showId, hideId) {

       		    if (showId && showId!=emptyvalue) {
     		    	document.getElementById(showId).style.visibility='visible';
					document.getElementById(showId).style.display = '';
     		    }
     		    if (hideId && hideId!=emptyvalue) {
     				document.getElementById(hideId).style.visibility = 'hidden';
     				document.getElementById(hideId).style.display = 'none';
     		    }
     		    return(showId);
     		}
        </script>
<?php
	}

	function isPHP($fileName)
	{
    	if (strlen($fileName) >= 4) {
    	    if (strtolower(substr($fileName, -4, 4)) == '.php')
    	        return true;
    	}
    	return false;
	}

	function isCSS($fileName)
	{
    	if (strlen($fileName) >= 4) {
    	    if (strtolower(substr($fileName, -4, 4)) == '.css')
    	        return true;
    	}
    	return false;
	}

	function languageList($path)
	{
    	$folder = @dir($path);
    	$darray = array();
    	$darray[] = JHTML::_('select.option', 'auto', 'autodetect');
    	if ($folder) {
    	    while ($file = $folder->read()) {
    	        if (JOSC_library::isPHP($file))
    	            $darray[] = JHTML::_('select.option', $file, substr($file, 0, strlen($file)-4));
    	    }
    	    $folder->close();
    	}
    	sort($darray);
    	return $darray;
	}

	function cssList($path, $makeoption=true)
	{
    	$folder = @dir($path);
    	$darray = array();
    	if ($folder) {
    	    while ($file = $folder->read()) {
    	        if (JOSC_library::isCSS($file))
    	            $darray[] = $makeoption ? JHTML::_('select.option',  $file, substr($file, 0, strlen($file)-4)) : $file;
    	    }
    	    $folder->close();
    	}
    	sort($darray);
    	return $darray;
	}

	function TemplatesCSSList($path)
	{
	    $folderlist = JOSC_library::folderList($path, false);
    	$foldercsslist = array();
    	if ($folderlist)
   		 	foreach($folderlist as $folder) {
    			$foldercsslist[$folder]['template'] = $folder;
    			$foldercsslist[$folder]['css'] 	= JOSC_library::cssList("$path/$folder/css");
    		}
    	return $foldercsslist;
	}

	/*
	 * return array of folder list option
	 */
	function folderList($path, $makeoption=true, $sort=true)
	{
    	$folder = dir($path);
    	$darray = array();
    	if ($folder) {
    	    while ($file = $folder->read()) {
    	        if ($file != "." && $file != ".." && is_dir("$path/$file"))
    	            $darray[] = $makeoption ? JHTML::_('select.option', $file, $file) : $file;
    	    }
    	    $folder->close();
    	}
    	if ($sort) sort($darray);

    	return $darray;
	}

	/*
	 * Function to handle an array of integers
 	 * Added 1.0.11
	 * JOSC for BACKWARD COMPATIBILITY
	 */
	function JOSCGetArrayInts( $name, $type=NULL )
	{

    	if (function_exists('josGetArrayInts')) {
    	  return call_user_func( 'josGetArrayInts', $name, $type ); /* call_user to avoid notice */
    	} else {
			if ( $type == NULL ) {
				$type = $_POST;
			}

			$array = JArrayHelper::getValue( $type, $name, array(0) );

			JArrayHelper::toInteger( $array );

			if (!is_array( $array )) {
			$array = array(0);
			}

			return $array;
    	}
	}

	/*
	 * transform an Array of integer in an Option object list (makeOption)
	 */
	function GetIntsMakeOption($intArray=array(), $OptionKey='id', $OptionValue='title')
	{
		$result 	= array();
		if (count($intArray)>0)
			foreach ( $intArray as $int ) {
				$result[] = JHTML::_('select.option',   $int, "$int", $OptionKey, $OptionValue );
			}
		return $result;
	}

	function copyDir($source,$dest)
	{

		if(!@mkdir($dest,0755) || ($dirFile=@opendir($source))===false)
			  return false;

		$result = true;
		while(($file=readdir($dirFile))!==false) {
			if(($file==".." || $file==".")) continue;

			$new_source = $source	."/".$file;
			$new_dest 	= $dest		."/".$file;
			if(@is_dir($new_source)) {
			    /* recurse call... */
				$result=JOSC_library::copyDir($new_source,$new_dest);
			} else {
				$result=@copy($new_source,$new_dest);
			}
		}
		closedir($dirFile);
		return $result;
	}
}
?>
