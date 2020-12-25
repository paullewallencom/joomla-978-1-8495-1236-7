<?php

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * Copyright Copyright (C) 2007 Alain Georgette. All rights reserved.
 * Copyright Copyright (C) 2006 Frantisek Hliva. All rights reserved.
 * License http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * !JoomlaComment is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * !JoomlaComment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 */

class JOSC_jscript {

    function insertJavaScript($path) {
	$html = "\n<script type='text/javascript'>\n";
	$html .= " var _JOOMLACOMMENT_MSG_DELETE 	= \"" . JText::_('JOOMLACOMMENT_MSG_DELETE', true) . "\";";
	$html .= " var _JOOMLACOMMENT_MSG_DELETEALL = \"" . JText::_('JOOMLACOMMENT_MSG_DELETEALL', true) . "\";";
	$html .= " var _JOOMLACOMMENT_WRITECOMMENT 	= \"" . JText::_('JOOMLACOMMENT_WRITECOMMENT', true) . "\";";
	$html .= " var _JOOMLACOMMENT_SENDFORM 		= \"" . JText::_('JOOMLACOMMENT_SENDFORM', true) . "\";";
	$html .= " var _JOOMLACOMMENT_EDITCOMMENT 	= \"" . JText::_('JOOMLACOMMENT_EDITCOMMENT', true) . "\";";
	$html .= " var _JOOMLACOMMENT_EDIT 			= \"" . JText::_('JOOMLACOMMENT_EDIT', true) . "\";";
	$html .= " var _JOOMLACOMMENT_FORMVALIDATE 	= \"" . JText::_('JOOMLACOMMENT_FORMVALIDATE', true) . "\";";
	$html .= " var _JOOMLACOMMENT_FORMVALIDATE_CAPTCHA = \"" . JText::_('JOOMLACOMMENT_FORMVALIDATE_CAPTCHA', true) . "\";";
	$html .= " var _JOOMLACOMMENT_FORMVALIDATE_CAPTCHA_FAILED = \"" . JText::_('JOOMLACOMMENT_FORMVALIDATE_CAPTCHA_FAILED', true) . "\";";
	$html .= " var _JOOMLACOMMENT_FORMVALIDATE_EMAIL = \"" . JText::_('JOOMLACOMMENT_FORMVALIDATE_EMAIL', true) . "\";";
	$html .= " var _JOOMLACOMMENT_FORMVALIDATE_INVALID_EMAIL = \"" . JText::_('JOOMLACOMMENT_FORMVALIDATE_INVALID_EMAIL', true) . "\";";
	$html .= ' var _JOOMLACOMMENT_ANONYMOUS 	= "' . JText::_('JOOMLACOMMENT_ANONYMOUS', true) . '";';
	$html .= " var _JOOMLACOMMENT_BEFORE_APPROVAL = \"" . JText::_('JOOMLACOMMENT_BEFORE_APPROVAL', true) . "\";";
	$html .= " var _JOOMLACOMMENT_REQUEST_ERROR = \"" . JText::_('JOOMLACOMMENT_REQUEST_ERROR', true) . "\";";
	$html .= " var _JOOMLACOMMENT_MSG_NEEDREFRESH = \"" . JText::_('JOOMLACOMMENT_MSG_NEEDREFRESH', true) . "\";";
	$html .= "\n</script>\n";
	$ifnocache = JOSC_utils::insertToHead($html);
	$ifnocache .= JOSC_utils::insertToHead("\n<script type='text/javascript' src='$path/jscripts/client.js'></script>\n");
	return $ifnocache;
    }

}

?>
