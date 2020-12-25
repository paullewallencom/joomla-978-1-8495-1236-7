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

$rows->addTitle(JTEXT::_('TITLE_BASIC_SETTINGS'));
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('ONLY_REGISTERED_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_only_registered]', 'class="inputbox"', $this->config->_only_registered);
$row->help = JTEXT::_('ONLY_REGISTERED_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('AUTOPUBLISH_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_autopublish]', 'class="inputbox"', $this->config->_autopublish);
$row->help = JTEXT::_('AUTOPUBLISH_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('BAN_CAPTION');
$row->component = JOSC_library::textarea('params[_ban]', 'class="inputbox" rows="5"', $this->config->_ban);
$row->help = JText::_('BAN_HELP');
$rows->addRow($row);

/*
 * NOTIFICATIONS
 */

$row = new JOSC_tabRow();
$row->caption = JText::_('NOTIFY_MODERATOR_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_notify_moderator]', 'class="inputbox"', $this->config->_notify_moderator);
$row->help = JTEXT::_('NOTIFY_MODERATOR_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('MODERATOR_CAPTION');
$usertypeslist = JOSC_utils::getJOSCUserTypes(false);
$selected = JOSC_library::GetIntsMakeOption(split(',', $this->config->_moderator));
$row->component = JHTML::_('select.genericlist', $usertypeslist, '_moderator[]', 'class="inputbox" multiple="multiple"', 'id', 'title', $selected);
$row->help = JTEXT::_('MODERATOR_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('NOTIFY_USERS_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_notify_users]', 'class="inputbox"', $this->config->_notify_users);
$row->help = JTEXT::_('NOTIFY_USERS_HELP');
$rows->addRow($row);
$row->caption = JTEXT::_('RSS_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_rss]', 'class="inputbox"', $this->config->_rss);
$row->help = JTEXT::_('RSS_HELP');
$rows->addRow($row);

$rows->addTitle(JTEXT::_('TITLE_OVERFLOW'));
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('MAXLENGTH_TEXT_CAPTION');
$row->component = JOSC_library::input('params[_maxlength_text]', 'class="inputbox"', $this->config->_maxlength_text);
$row->help = JTEXT::_('MAXLENGTH_TEXT_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JTEXT::_('MAXLENGTH_LINE_CAPTION');
$row->component = JOSC_library::input('params[_maxlength_line]', 'class="inputbox"', $this->config->_maxlength_line);
$row->help = JTEXT::_('MAXLENGTH_LINE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('MAXLENGTH_WORD_CAPTION');
$row->component = JOSC_library::input('params[_maxlength_word]', 'class="inputbox"', $this->config->_maxlength_word);
$row->help = JText::_('MAXLENGTH_WORD_HELP');
$rows->addRow($row);

/*
 * ANTI-SPAM
 */
$rows->addTitle(JText::_('TITLE_CAPTCHA'));
$row = new JOSC_tabRow();
$row->caption = JText::_('CAPTCHA_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_captcha]', 'class="inputbox"', $this->config->_captcha);
$row->help = JText::_('CAPTCHA_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('CAPTCHA_TYPE_CAPTION');
$captchalist = array();
$captchalist[] = JHTML::_('select.option', 'default', JText::_('CAPTCHA_TYPE_DEFAULT'));
$captchalist[] = JHTML::_('select.option', 'recaptcha', JText::_('CAPTCHA_TYPE_RECAPTCHA'));
$row->component = JHTML::_('select.genericlist', $captchalist, 'params[_captcha_type]', 'class="inputbox"', 'value', 'text', $this->config->_captcha_type);
$row->help = JText::_('CAPTCHA_TYPE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('RECAPTCHA_PUBKEY_CAPTION');
$row->component = JOSC_library::input('params[_recaptcha_public_key]', 'class="inputbox"', $this->config->_recaptcha_public_key);
$row->help = JText::_('RECAPTCHA_PUBKEY_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('RECAPTCHA_PRVKEY_CAPTION');
$row->component = JOSC_library::input('params[_recaptcha_private_key]', 'class="inputbox"', $this->config->_recaptcha_private_key);
$row->help = JText::_('RECAPTCHA_PRVKEY_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('CAPTCHA_USERTYPES_CAPTION');
$usertypeslist = JOSC_utils::getJOSCUserTypes();
$selected = JOSC_library::GetIntsMakeOption(split(',', $this->config->_captcha_usertypes));
$row->component = JHTML::_('select.genericlist', $usertypeslist, '_captcha_usertypes[]', 'class="inputbox" multiple="multiple"', 'id', 'title', $selected);
$row->help = JText::_('CAPTCHA_USERTYPES_HELP');
$rows->addRow($row);


$row = new JOSC_tabRow();
$row->caption = JText::_('WEBSITE_REGISTERED_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_website_registered]', 'class="inputbox"', $this->config->_website_registered);
$row->help = JText::_('WEBSITE_REGISTERED_HELP');
$rows->addRow($row);
/*
 * CENSOR
 */
$rows->addTitle(JTEXT::_('TITLE_CENSORSHIP'));
$row = new JOSC_tabRow();
$row->caption = JText::_('CENSORSHIP_ENABLE_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_censorship_enable]', 'class="inputbox"', $this->config->_censorship_enable);
$row->help = JText::_('CENSORSHIP_ENABLE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('CENSORSHIP_CASE_SENSITIVE_CAPTION');
$row->component = JHTML::_('select.booleanlist', 'params[_censorship_case_sensitive]', 'class="inputbox"', $this->config->_censorship_case_sensitive);
$row->help = JText::_('CENSORSHIP_CASE_SENSITIVE_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('CENSORSHIP_WORDS_CAPTION');
$row->component = JOSC_library::textarea('params[_censorship_words]', 'class="inputbox" rows="5" cols="70"', $this->config->_censorship_words);
$row->help = JText::_('CENSORSHIP_WORDS_HELP');
$rows->addRow($row);
$row = new JOSC_tabRow();
$row->caption = JText::_('CENSORSHIP_USERTYPES_CAPTION');
$usertypeslist = JOSC_utils::getJOSCUserTypes();
$selected = JOSC_library::GetIntsMakeOption(split(',', $this->config->_censorship_usertypes));
$row->component = JHTML::_('select.genericlist', $usertypeslist, '_censorship_usertypes[]', 'class="inputbox" multiple="multiple"', 'id', 'title', $selected);
$row->help = JText::_('CENSORSHIP_USERTYPES_HELP');
$rows->addRow($row);

echo $rows->tabRows_htmlCode();
?>
