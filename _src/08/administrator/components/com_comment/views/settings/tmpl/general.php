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
$row->caption = JText::_('COMPLETE_UNINSTALL_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_complete_uninstall]', 'class="inputbox"', $this->config->_complete_uninstall);
$row->help = JText::_('COMPLETE_UNINSTALL_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('PLUGIN_FUNC_CAPTION');
$row->component = JOSC_library::input('params[_mambot_func]', 'class="inputbox"', $this->config->_mambot_func);
$row->help = JText::_('PLUGIN_FUNC_HELP');
$rows->addRow($row);

$rows->addTitle(JTEXT::_('TITLE_SECTIONS_CATEGORIES'));
$row = new JOSC_tabRow();
$row->caption = JText::_('INCLUDE_SC_CAPTION');
$row->component = JOSC_library::customRadioList('params[_include_sc]', 'class="inputbox"', $this->config->_include_sc, JText::_('INCLUDE'), JText::_('EXCLUDE'));
$row->help = JText::_('INCLUDE_SC_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('EXCLUDE_CONTENTITEMS_CAPTION');
$row->component = JOSC_library::input('params[_exclude_contentitems]', 'class="inputbox"', $this->config->_exclude_contentitems);
$row->help = JText::_('EXCLUDE_CONTENTITEMS_HELP');
$rows->addRow($row);

$row = new JOSC_tabRow();
$row->caption = JText::_('DISABLE_ADDITIONAL_COMMENTS_CAPTION');
$row->component = JOSC_library::input('params[_disable_additional_comments]', 'class="inputbox"', $this->config->_disable_additional_comments);
$row->help = JText::_('DISABLE_ADDITIONAL_COMMENTS_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('EXCLUDE_SECTIONS_CAPTION');
$sectionlist = $this->config->_comObject->getSectionsIdOption();
$selected = JOSC_library::GetIntsMakeOption(explode(',', $this->config->_exclude_sections));
$row->component = JHTML::_('select.genericlist', $sectionlist, '_exclude_sections[]', 'class="inputbox" multiple="multiple"', 'id', 'title', $selected);
$row->help = JText::_('EXCLUDE_SECTIONS_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('EXCLUDE_CATEGORIES_CAPTION');
$categorylist = $this->config->_comObject->getCategoriesIdOption();
$selected = JOSC_library::GetIntsMakeOption(explode(',', $this->config->_exclude_categories));
$row->component = JHTML::_('select.genericlist', $categorylist, '_exclude_categories[]', 'class="inputbox"  multiple="multiple"', 'id', 'title', $selected);
$row->help = JText::_('EXCLUDE_CATEGORIES_HELP');
$rows->addRow($row);


$rows->addTitle(JText::_('TITLE_TECHNICAL'));
$row = new JOSC_tabRow();
$row->caption = JText::_('DEBUG_USERNAME_CAPTION');
$row->component = JOSC_library::input('params[_debug_username]', 'class="inputbox"', $this->config->_debug_username);
$row->help = JText::_('DEBUG_USERNAME_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('XMLERRORALERT_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_xmlerroralert]', 'class="inputbox"', $this->config->_xmlerroralert);
$row->help = JText::_('XMLERRORALERT_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('AJAXDEBUG_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_ajaxdebug]', 'class="inputbox"', $this->config->_ajaxdebug);
$row->help = JText::_('AJAXDEBUG_HELP');
$rows->addRow($row);

echo $rows->tabRows_htmlCode();
?>
