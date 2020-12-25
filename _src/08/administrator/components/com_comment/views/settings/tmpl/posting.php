<?php
/***************************************************************
*  Copyright notice
*
*  Copyright 2010 Daniel Dimitrov. (http://compojoom.com)
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

defined('_JEXEC') or die('Restricted access');

$rows = new JOSC_tabRows();

$rows->addTitle(JText::_('TITLE_BASIC_SETTINGS'));
$row = new JOSC_tabRow();
$row->caption = JText::_('AJAX_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_ajax]', 'class="inputbox"', $this->config->_ajax);
$row->help = JText::_('AJAX_HELP');
$rows->addRow($row);

/*
 * STRUCTURE
 */
$rows->addTitle(JText::_('TITLE_STRUCTURE'));
$row = new JOSC_tabRow();
$row->caption = JText::_('TREE_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_tree]', 'class="inputbox"', $this->config->_tree);
$row->help = JText::_('TREE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('MLINK_POST_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_mlink_post]', 'class="inputbox"', $this->config->_mlink_post);
$row->help = JText::_('MLINK_POST_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('TREE_INDENT_CAPTION');
$row->component = JOSC_library::input('params[_tree_indent]', 'class="inputbox"', $this->config->_tree_indent);
$row->help = JTEXT::_('TREE_INDENT_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('SORT_DOWNWARD_CAPTION');
$sorting = array();
$sorting[] = JHTML::_('select.option', '1', JTEXT::_('SORT_DOWNWARD_VALUE_FIRST'));
$sorting[] = JHTML::_('select.option', '0', JTEXT::_('SORT_DOWNWARD_VALUE_LAST'));
$row->component = JHTML::_('select.genericlist', $sorting, 'params[_sort_downward]', 'class="inputbox"', 'value', 'text', $this->config->_sort_downward);
$row->help = JTEXT::_('SORT_DOWNWARD_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('DISPLAY_NUM_CAPTION');
$row->component = JOSC_library::input('params[_display_num]', 'class="inputbox"', $this->config->_display_num);
$row->help = JTEXT::_('DISPLAY_NUM_HELP');
$rows->addRow($row);

/*
 * POSTING
 */
$rows->addTitle(JTEXT::_('TITLE_POSTING'));
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('ENTER_WEBSITE_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_enter_website]', 'class="inputbox"', $this->config->_enter_website);
$row->help = JTEXT::_('ENTER_WEBSITE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('SUPPORT_UBBCODE_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_support_UBBcode]', 'class="inputbox"', $this->config->_support_UBBcode);
$row->help = JTEXT::_('SUPPORT_UBBCODE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('SUPPORT_PICTURES_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_support_pictures]', 'class="inputbox"', $this->config->_support_pictures);
$row->help = JTEXT::_('SUPPORT_PICTURES_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('PICTURES_MAXWIDTH_CAPTION');
$row->component = JOSC_library::input('params[_pictures_maxwidth]', 'class="inputbox"', $this->config->_pictures_maxwidth);
$row->help = JTEXT::_('PICTURES_MAXWIDTH_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('VOTING_VISIBLE_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_voting_visible]', 'class="inputbox"', $this->config->_voting_visible);
$row->help = JTEXT::_('VOTING_VISIBLE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('USE_NAME_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_use_name]', 'class="inputbox"', $this->config->_use_name);
$row->help = JTEXT::_('USE_NAME_HELP');
$rows->addRow($row);


$row = new JOSC_tabRow();
$row->caption = JText::_('DATE_FORMAT_CAPTION');
$row->component = JOSC_library::input('params[_date_format]', 'class="inputbox"', $this->config->_date_format);
$row->help = JText::_('DATE_FORMAT_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('NO_SEARCH_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_no_search]', 'class="inputbox"', $this->config->_no_search);
$row->help = JTEXT::_('NO_SEARCH_HELP');
$rows->addRow($row);

$rows->addTitle(JText::_('TITLE_IP_ADDRESS'));
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('IP_VISIBLE_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_IP_visible]', 'class="inputbox"', $this->config->_IP_visible);
$row->help = JTEXT::_('IP_VISIBLE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('IP_USERTYPES_CAPTION');
$usertypeslist = JOSC_utils::getJOSCUserTypes();
$selected = JOSC_library::GetIntsMakeOption(split(',', $this->config->_IP_usertypes));
$row->component = JHTML::_('select.genericlist', $usertypeslist, '_IP_usertypes[]', 'class="inputbox" multiple="multiple"', 'id', 'title', $selected);
$row->help = JText::_('IP_USERTYPES_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('IP_PARTIAL_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_IP_partial]', 'class="inputbox"', $this->config->_IP_partial);
$row->help = JTEXT::_('IP_PARTIAL_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('IP_CAPTION_CAPTION');
$row->component = JOSC_library::input('params[_IP_caption]', 'class="inputbox"', $this->config->_IP_caption);
$row->help = JText::_('IP_CAPTION_HELP');
$rows->addRow($row);

echo $rows->tabRows_htmlCode();
?>
