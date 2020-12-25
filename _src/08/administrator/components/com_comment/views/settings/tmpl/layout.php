<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  Copyright 2010s Daniel Dimitrov. (http://compojoom.com)
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
 * ************************************************************* */

defined('_JEXEC') or die('Restricted access');

$rows = new JOSC_tabRows();

/*
 * FRONTPAGE
 */
$rows->addTitle(JText::_('TITLE_FRONTPAGE'));
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('SHOW_READON_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_show_readon]', 'class="inputbox"', $this->config->_show_readon);
$row->help = JTEXT::_('SHOW_READON_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('MENU_READON_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_menu_readon]', 'class="inputbox"', $this->config->_menu_readon);
$row->help = JTEXT::_('MENU_READON_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('INTRO_ONLY_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_intro_only]', 'class="inputbox"', $this->config->_intro_only);
$row->help = JTEXT::_('INTRO_ONLY_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('PREVIEW_VISIBLE_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_preview_visible]', 'class="inputbox"', $this->config->_preview_visible);
$row->help = JTEXT::_('PREVIEW_VISIBLE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('PREVIEW_LENGTH_CAPTION');
$row->component = JOSC_library::input('params[_preview_length]', 'class="inputbox"', $this->config->_preview_length);
$row->help = JTEXT::_('PREVIEW_LENGTH_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('PREVIEW_LINES_CAPTION');
$row->component = JOSC_library::input('params[_preview_lines]', 'class="inputbox"', $this->config->_preview_lines);
$row->help = JTEXT::_('PREVIEW_LINES_HELP');
$rows->addRow($row);

$rows->addTitle(JTEXT::_('TITLE_TEMPLATES'));
/*
 * TEMPLATES
 */
/* standard template and CSS */
$style = $this->config->_template_custom ? ' style="color:grey;" ' : '';
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('TEMPLATE_CAPTION');
$foldercsslist = JOSC_library::TemplatesCSSList(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'joscomment' . DS . 'templates');
$css = "";
if ($foldercsslist) {
	foreach ($foldercsslist as $folder) {
		$param = '_template_css' . $folder['template'];
		$css .= '<tr id="' . $param . '" style="display:none;" >'
				. '<td><b>CSS </b></td><td>'
				. JHTML::_('select.genericlist', $folder['css'], "params[$param]", ' class="inputbox" ' . $style, 'value', 'text', $this->config->_template_css)
				. '</td></tr>'
		;
	}
}
$folderlist = JOSC_library::folderList(JPATH_SITE . "/components/com_comment/joscomment/templates");
$onchange = $css ? " onchange=\"JOSC_template_active=JOSC_adminVisible('_template_css', '_template_css'+document.getElementsByName('params[_template]')[0].value,JOSC_template_active);\" " : "";
$row->component = '<table cellpadding=0 cellspacing=0><tr><td><b>HTML </b></td><td>'
		. JHTML::_('select.genericlist', $folderlist, 'params[_template]', 'class="inputbox" ' . $style . $onchange, 'value', 'text', $this->config->_template)
		. '</td></tr>'
		. $css . "<script type='text/javascript'>var JOSC_template_active='_template_css'+'" . $this->config->_template . "';JOSC_adminVisible('_template_css', JOSC_template_active);</script>"
		. '</table>'
;
$row->help = JTEXT::_('TEMPLATE_HELP');
$rows->addRow($row);
/* copy of template ? */
$copytemplate = $this->config->_copy_template ? $this->config->_template : '';
$check = $this->setTemplateCustomPath(true, $copytemplate); /* get path and copy if asked */
$this->config->_copy_template = '0'; /* reset */

$row = new JOSC_tabRow();
$row->caption = JTEXT::_('COPY_TEMPLATE_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_copy_template]', 'class="inputbox" ', $this->config->_copy_template);
$row->help = JTEXT::_('COPY_TEMPLATE_HELP');
$rows->addRow($row);
/* customized template and CSS */
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('TEMPLATE_CUSTOM_CAPTION') . ($check ? ("<br />" . JText::_('TEMPLATE_CUSTOM_LOCATION') . $check) : "");
$foldercsslist = JOSC_library::TemplatesCSSList($this->config->_template_custom_path);
$css = "";
if ($foldercsslist) {
	foreach ($foldercsslist as $folder) {
		$param = '_template_custom_css' . $folder['template'];
		$css .= '<tr id="' . $param . '" style="display:none;" >'
				. '<td><b>CSS </b></td><td>'
				. JHTML::_('select.genericlist', $folder['css'], "params[$param]", ' class="inputbox"', 'value', 'text', $this->config->_template_custom_css)
				. '</td></tr>'
		;
	}
}
$folderlist = JOSC_library::folderList($this->config->_template_custom_path);
// add empty value
array_unshift($folderlist, JHTML::_('select.option', '', '-- Use standard --', 'value', 'text'));
$onchange = $css ? " onchange=\"JOSC_template_custom_active=JOSC_adminVisible('_template_custom_css', '_template_custom_css'+document.getElementsByName('params[_template_custom]')[0].value, JOSC_template_custom_active);\" " : "";
$row->component = '<table cellpadding=0 cellspacing=0><tr><td><b>HTML </b></td><td>'
		. JHTML::_('select.genericlist', $folderlist, 'params[_template_custom]', 'class="inputbox"' . $onchange, 'value', 'text', $this->config->_template_custom)
		. '</td></tr>'
		. $css . "<script type='text/javascript'>var JOSC_template_custom_active='_template_custom_css'+'" . $this->config->_template_custom . "';JOSC_adminVisible('_template_custom_css', JOSC_template_custom_active);</script>"
		. '</table>'
;
$row->help = JTEXT::_('TEMPLATE_CUSTOM_HELP');
$rows->addRow($row);

$row = new JOSC_tabRow();
$row->caption = JTEXT::_('TEMPLATE_MODIFY_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_template_modify]', 'class="inputbox" ', $this->config->_template_modify);
$row->help = JTEXT::_('TEMPLATE_MODIFY_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('TEMPLATE_LIBRARY_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_template_library]', 'class="inputbox" ', $this->config->_template_library);
$row->help = JTEXT::_('TEMPLATE_LIBRARY_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('FORM_AREA_COLS_CAPTION');
$row->component = JOSC_library::input('params[_form_area_cols]', 'class="inputbox"', $this->config->_form_area_cols);
$row->help = JTEXT::_('FORM_AREA_COLS_HELP');
$rows->addRow($row);


/*
 * EMOTICONS
 */
$rows->addTitle(JTEXT::_('TITLE_EMOTICONS'));
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('SUPPORT_EMOTICONS_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_support_emoticons]', 'class="inputbox"', $this->config->_support_emoticons);
$row->help = JTEXT::_('SUPPORT_EMOTICONS_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('EMOTICON_PACK_CAPTION');
$selectlist = array();
$folderlist = JOSC_library::folderList(JPATH_SITE . DS . "components" . DS . "com_comment" . DS . "joscomment" . DS . "emoticons", false, false);
$help_emoticons = "";

if ($folderlist) {
	foreach ($folderlist as $pack) {
		$help_id = 'help_emoticons_' . $pack;
		$help_emoticons .= "<div id=\"$help_id\" style=\"display:none\">"
				. $this->emoticons_confightml($pack)
				. "</div>";
		$selectlist[] = JHTML::_('select.option', $pack, $pack);
	}
}
$onchange = $folderlist ? " onchange=\"JOSC_help_emoticons_active=JOSC_adminVisible('help_emoticons_', 'help_emoticons_'+document.getElementsByName('params[_emoticon_pack]')[0].value, JOSC_help_emoticons_active);\" " : "";
$row->component = JHTML::_('select.genericlist', $selectlist, 'params[_emoticon_pack]', 'class="inputbox"' . $onchange, 'value', 'text', $this->config->_emoticon_pack);
$row->help = $help_emoticons
		. "<script type='text/javascript'>var JOSC_help_emoticons_active='help_emoticons_'+'" . $this->config->_emoticon_pack . "';JOSC_adminVisible('help_emoticons_', JOSC_help_emoticons_active);</script>"
;

$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('EMOTICON_WCOUNT_CAPTION');
$row->component = JOSC_library::input('params[_emoticon_wcount]', 'class="inputbox"', $this->config->_emoticon_wcount);
$row->help = JTEXT::_('EMOTICON_WCOUNT_HELP');
$rows->addRow($row);

echo $rows->tabRows_htmlCode();
?>
