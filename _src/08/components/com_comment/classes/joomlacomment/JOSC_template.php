<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
/***************************************************************
*  $Revision$
*
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the !JoomlaComment project. The !joomlaComment project is
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
class JOSC_template {
    var $_live_site;
    var $_absolute_path;
    var $_template_path = '';
    var $_template_absolute_path = '';
    var $_name = '';
    var $_css  = '';
    var $_title = '';
    /*
     * parsed blocks
     */
    var $_body = '';
    var $_library = '';
	var $_readon = '';
	var $_previewline = '';
    var $_menu = ''; /* ? */
    var $_post = '';
    var $_search = '';
    var $_searchResults = '';
    var $_form = '';
    var $_poweredby = '';

    function JOSC_template($name,$css='template_css.css')
    {
        $this->_name = $name;
        $this->_css  = $css;
    }

    function loadFromFile()
    {
        $fileName = $this->_template_absolute_path .'/'. $this->_name . '/index.html';
        if (file_exists($fileName)) {
            $file = fopen ($fileName, 'r');
            $template = fread ($file, filesize($fileName));
            fclose($file);
            return $template;
        } else {
            die ('!JoomlaComment template not found: ' . $this->_name);
        }
    }

    /*
     * function to add the css to the head of the document
     */
    function CSS()
    {
        $css = $this->_template_path . '/' . $this->_name . '/css/'.$this->_css;
        $document =& JFactory::getDocument();
        $document->addStyleSheet($css);
    }

    function parse($readon=false)
    {
        $template = $this->loadFromFile();
        $this->_body 	= JOSC_utils::block($template, 'body');
        $this->_library = JOSC_utils::block($template, 'library');
        if ($readon) {
        	$this->_readon = JOSC_utils::block($template, 'readon');
        	$this->_previewline = JOSC_utils::block($template, 'previewline');
        } else {
        	$this->_menu = JOSC_utils::block($template, 'menu');
        	$this->_search = JOSC_utils::block($template, 'search');
        	$this->_searchResults = JOSC_utils::block($template, 'searchresults');
        	$this->_post = JOSC_utils::block($template, 'post');
        	$this->_form = JOSC_utils::block($template, 'form');
        	$this->_poweredby = JOSC_utils::block($template, 'poweredby');
        }
    }
}
?>
