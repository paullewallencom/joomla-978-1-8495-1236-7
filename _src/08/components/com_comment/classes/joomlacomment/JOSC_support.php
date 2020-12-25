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

class JOSC_support {
    public $_comObject;
    public $_ajax;
    public $_local_charset;
    public $_absolute_path;
    public $_live_site;
    public $_template_absolute_path;
    public $_template_path;
    public $_template_name;
    public $_only_registered;
    public $_website_registered;
    public $_support_emoticons;
    public $_support_UBBcode;
    public $_support_pictures;
    public $_pictures_maxwidth;
    public $_support_quotecode;
    public static $_support_link;
    public $_hide;
    public $_emoticons;
    public $_emoticons_path;
    public $_censorship_enable;
    public $_censorship_case_sensitive;
    public $_censorship_words;
    public $_censorship_usertypes;
    public $_content_id;
    public $_component;
    public $_sectionid;
    public $_moderator;
    public $_show_readon;
    public $_date_format;
    public $_maxlength_text;
    public $_maxlength_word;
    public $_maxlength_line;

	function __construct(&$comObject)
	{
		$this->_comObject = $comObject;
	}

    function setAjax($value)
    {
        $this->_ajax = $value;
    }

    function setLocalCharset($value)
    {
        $this->_local_charset = $value;
    }

    function setAbsolute_path($value)
    {
        $this->_absolute_path = $value;
    }

    function setLive_site($value)
    {
        $this->_live_site = $value;
    }

    function setTemplate_path($value)
    {
        $this->_template_path = $value;
    }

    function setTemplateAbsolutePath($value)
    {
        $this->_template_absolute_path = $value;
    }

    function setTemplate_name($value)
    {
        $this->_template_name = $value;
    }

    function setOnly_registered($value)
    {
        $this->_only_registered = $value;
    }

    function setWebsiteRegistered($value)
    {
        $this->_website_registered = $value;
    }

    function setSupport_emoticons($value)
    {
        $this->_support_emoticons = $value;
    }

    function setSupport_UBBcode($value)
    {
        $this->_support_UBBcode = $value;
    }

    function setSupport_pictures($value,$maxwidth='')
    {
        $this->_support_pictures = $value;
        $this->_pictures_maxwidth = $maxwidth;
    }

    function getSupport_pictures()
    {	/* used in module */
        return $this->_support_pictures;
    }

    function setSupport_quotecode($value)
    {
        $this->_support_quotecode = $value;
    }

    function getSupport_quotecode()
    {	/* used in module */
        return $this->_support_quotecode;
    }

    function setSupport_link($value)
    {
        $this->_support_link = $value;
    }

    function getSupport_link()
    {	/* used in module */
        return $this->_support_link;
    }

    function setHide($value)
    {
        $this->_hide = $value;
    }

    function setEmoticons($value)
    {
        $this->_emoticons = $value;
    }

    function setEmoticons_path($value)
    {
        $this->_emoticons_path = $value;
    }

	function setContentId($value)
    {
        $this->_content_id = (int)$value;
    }

	function setComponent($value)
    {
        $this->_component = $value;
    }

	function setSectionid($value)
    {
        $this->_sectionid = $value;
    }

	function setModerator($value)
    {
        $this->_moderator = $value;
    }

    function setReadon($value)
    {
        $this->_show_readon= $value;
    }

    function setDate_format($value)
    {
        $this->_date_format = $value;
    }

	function setCensorShip($enable, $case_sensitive, $words, $usertypes ) {
    	$this->_censorship_enable 			= $enable;
    	$this->_censorship_case_sensitive 	= $case_sensitive;
    	$this->_censorship_words 			= $words;
    	$this->_censorship_usertypes 		= $usertypes;
	}

    function setMaxLength_text($value)
    {
        $this->_maxlength_text = $value;
    }

    function getMaxLength_text()
    {	/* used in module */
        return $this->_maxlength_text;
    }

    function setMaxLength_word($value)
    {
        $this->_maxlength_word = $value;
    }

    function getMaxLength_word()
    {	/* used in module */
        return $this->_maxlength_word;
    }

    function setMaxLength_line($value)
    {
        $this->_maxlength_line = $value;
    }

    function getMaxLength_line()
    {	/* used in module */
        return $this->_maxlength_line;
    }

    function censorText($text)
    {
        return JOSC_utils::censorText($text,$this->_censorship_enable,$this->_censorship_words,$this->_censorship_case_sensitive);
    }

	function formHiddenValues($contentid, $component, $sectionid )
	{	/* used also in BOARD ! */
        $hidden  = JOSC_utils::inputHidden('content_id', $contentid);
        $hidden .= JOSC_utils::inputHidden('component', $component);
        $hidden .= JOSC_utils::inputHidden('joscsectionid', $sectionid);
//		$hidden .= JHTML::_('form.token');
		return $hidden;
	}
}



?>
