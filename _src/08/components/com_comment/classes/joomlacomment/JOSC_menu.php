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
class JOSC_menu extends JOSC_support {
	var $_menu;
	var $_rss;
	var $_no_search;

	function JOSC_menu($value) {
		$this->_menu = $value;
	}

	function setRSS($value) {
		$this->_rss = $value;
	}

	function setNoSearch($value) {
		$this->_no_search = $value;
	}

	function insertButton($text, $link, $icon = '') {
		if ($icon) $icon = "<img class='menuicon' src='$icon' alt='$icon' />";
		return "<td class='button'><a id='$text' href='$link'>$icon$text</a></td>";
	}

	public function menu_htmlCode() {
		$user = JFactory::getUser();

		$html = $this->_menu;

		$only_registered = !$user->username && $this->_only_registered;

		/* {_JOOMLACOMMENT_COMMENTS_TITLE} */
		$html 		= str_replace('{_JOOMLACOMMENT_COMMENTS_TITLE}', JText::_('JOOMLACOMMENT_COMMENTS_TITLE'), $this->_menu);
		/* {template_live_site} 	*/
		$html 		= str_replace('{template_live_site}', $this->_template_path.'/'.$this->_template_name, $html);

		/* {BLOCK-add_new}	_JOOMLACOMMENT_ADDNEW */
		$display	= !$only_registered;
		$html 		= JOSC_utils::checkBlock('BLOCK-add_new', $display, $html);
		if ($display) {
			$html = str_replace('{_JOOMLACOMMENT_ADDNEW}', JText::_('JOOMLACOMMENT_ADDNEW'), $html);
			$html = str_replace('{BUTTON_ADDNEW_js}', 'JOSC_addNew()', $html);
		}

		/* {BLOCK-delete_all}  _JOOMLACOMMENT_DELETEALL	 */
		$display	= JOSC_utils::isModerator($this->_moderator);
		$html 		= JOSC_utils::checkBlock('BLOCK-delete_all', $display, $html);
		if ($display) {
			$html 	= str_replace('{_JOOMLACOMMENT_DELETEALL}', JText::_('JOOMLACOMMENT_DELETEALL'), $html);
			$html 	= str_replace('{BUTTON_DELETEALL_js}', 'JOSC_deleteAll()', $html);
		}

		/* {BLOCK-search} _JOOMLACOMMENT_SEARCH */
		$display	= !$this->_no_search;
		$html 		= JOSC_utils::checkBlock('BLOCK-search', $display, $html);
		if ($display) {
			$html = str_replace('{_JOOMLACOMMENT_SEARCH}', JText::_('JOOMLACOMMENT_SEARCH'), $html);
			$html = str_replace('{BUTTON_SEARCH_js}', 'JOSC_searchForm()', $html);
		}

		/* {BLOCK-rss} _JOOMLACOMMENT_RSS */
		$display	= $this->_rss;
		$html 		= JOSC_utils::checkBlock('BLOCK-rss', $display, $html);
		if ($display) {
			$html 	= str_replace('{_JOOMLACOMMENT_RSS}', JText::_('JOOMLACOMMENT_RSS'), $html);
			$option = JRequest::getCmd('option');
			$html 	= str_replace('{BUTTON_RSS_URL}', JRoute::_("index.php?option=com_comment&no_html=1&josctask=rss&plugin=$option&contentid=$this->_content_id"), $html);
		}
		return $html;
	}
}

?>
