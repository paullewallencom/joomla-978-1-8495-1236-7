<?php defined('_JEXEC')  or die('Direct Access to this location is not allowed.');

/*
 * Copyright (c) 2009 Daniel Dimitrov (http://compojoom.com) . All rights reserved.
 * License http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * Compojoom Comment is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Compojoom Comment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 */

function com_install() {
	$lang =& JFactory::getLanguage();
	$lang->load('com_comment');
	
	if(_checkForUpdate()) {
		echo '<p>' . JText::_('DATABASES UPDATED') . '</p>';
	}
	_installPlugin();
}

function _checkForUpdate() {
	$database =& JFactory::getDBO();
	$update = false;
	$needToInsertConfig = false;
	$query = 'SELECT * FROM ' . $database->nameQuote('#__comment_setting')
			. ' WHERE ' . $database->nameQuote('set_component') . ' = ""';
	$database->setQuery($query);
	$row = $database->loadObject();

	if($row == NULL) {
		$query = 'SELECT * FROM ' . $database->nameQuote('#__comment_setting')
		. ' WHERE ' . $database->nameQuote('set_component') . ' = "com_content"';
		$database->setQuery($query);
		$config = $database->loadObject();

		if($config == NULL) {
			$needToInsertConfig = true;
		}
		
	} elseif ($row->set_component == '') {
		$update = true;
	}

	if($needToInsertConfig) {
		_insertConfig();
		if(_checkIfCommentNeedsUpdate()) {
				_updateCommentTable();
		}
	}
	if($update) {
		if($row->set_component == '') {
			_updateSettingTable();
			if(_checkIfCommentNeedsUpdate()) {
				_updateCommentTable();
			}
		}
		
	}
}

function _checkIfCommentNeedsUpdate() {
	$database =& JFactory::getDBO();
	$update = false;
	$query = 'SELECT * FROM ' . $database->nameQuote('#__comment')
			. ' WHERE ' . $database->nameQuote('component') . ' = ""'
			. ' LIMIT 1';
	$database->setQuery($query);

	$row = $database->loadObject();
	if($row) {
		$update = true;
	}
	return $update;
}
function _insertConfig() {
	$database =& JFactory::getDBO();
	$query = 'INSERT INTO ' . $database->nameQuote('#__comment_setting') . ' VALUES'
				."(1, 'Content Settings', 'com_content', 0, '_complete_uninstall=0\n_mambot_func=onPrepareContent\n_include_sc=0\n_exclude_contentitems=\n_disable_additional_comments=\n_debug_username=\n_xmlerroralert=0\n_ajaxdebug=0\n_only_registered=0\n_autopublish=1\n_ban=\n_notify_moderator=0\n_notify_users=1\n_rss=0\n_maxlength_text=1000\n_maxlength_line=-1\n_maxlength_word=80\n_captcha=1\n_captcha_type=default\n_akismet_use=0\n_akismet_key=\n_website_registered=0\n_censorship_enable=0\n_censorship_case_sensitive=0\n_censorship_words=nastybitch = nast***tch, motherfucker = moth****cker, fucking = fu**ing, twat, fisting, kokot = ko**t\n_ajax=1\n_tree=1\n_mlink_post=0\n_tree_indent=20\n_sort_downward=0\n_display_num=0\n_enter_website=1\n_support_UBBcode=1\n_support_pictures=0\n_pictures_maxwidth=\n_voting_visible=1\n_use_name=0\n_support_profiles=0\n_support_avatars=0\n_gravatar=0\n_date_format=%Y-%m-%d %H:%M:%S\n_no_search=0\n_IP_visible=1\n_IP_partial=1\n_IP_caption=\n_show_readon=1\n_menu_readon=1\n_intro_only=0\n_preview_visible=0\n_preview_length=80\n_preview_lines=5\n_template=modern\n_template_cssJQdefault-emotop=css-age.css\n_template_cssMT-DoubleSlide=css.css\n_template_cssMTdefault-emotop=css-age.css\n_template_cssSSlide-emotop=css-age.css\n_template_cssSSlideBoth-emotop=css-age.css\n_template_cssakostyle=css.css\n_template_cssmodern=standard.css\n_copy_template=0\n_template_custom=\n_template_custom_cssmyMTdefault-emotop=css.css\n_template_custom_cssmydefault=css.css\n_template_custom_cssmydefault-emotop=css.css\n_template_modify=1\n_template_library=1\n_form_area_cols=40\n_support_emoticons=1\n_emoticon_pack=modern\n_emoticon_wcount=12\n_moderator=0\n_exclude_sections=\n_exclude_categories=\n_IP_usertypes=-1,3,4,2,5,6,1,0\n_captcha_usertypes=-1\n_censorship_usertypes=-1,3\n_template_css=standard.css\n_template_custom_css=')";
		$database->setQuery($query);
		if($database->query()) {
			echo '<p>' . JText::_('CONTENT CONFIG CREATED') . '</p>';
	}
}
function _updateSettingTable() {
	$database =& JFactory::getDBO();
	$query = 'UPDATE ' . $database->nameQuote('#__comment_setting')
				. ' SET ' . $database->nameQuote('set_component') . ' = ' . $database->Quote('com_content')
				. ' WHERE ' . $database->nameQuote('set_component') . ' = ""';
			$database->setQuery($query);
			if($database->query()) {
				echo '<p>' . JText::_('COMPOJOOMCOMMENT_DATABASE_TABLE') . ' ' . $database->replacePrefix('#__comment_setting') . ' ' . JText::_('COMPOJOOMCOMMENT_UPDATED') . '</p>';
	}
}

function _updateCommentTable() {
	$database =& JFactory::getDBO();
	$query = 'UPDATE ' . $database->nameQuote('#__comment')
				. ' SET ' . $database->nameQuote('component') . ' = ' . $database->Quote('com_content')
				. ' WHERE ' . $database->nameQuote('component') . ' = ""';
		$database->setQuery($query);
		if($database->query()) {
			echo '<p>' . JText::_('COMPOJOOMCOMMENT_DATABASE_TABLE') . ' ' . $database->replacePrefix('#__comment') . ' ' . JText::_('COMPOJOOMCOMMENT_UPDATED') . '</p>';
	}
}

function _installPlugin() {
	$componentInstaller =& JInstaller::getInstance();
	$installer = new JInstaller();
	$db =& JFactory::getDBO();

	$pathToPlugin = $componentInstaller->getPath('source') . DS . 'plugin' . DS . 'joscomment';
	//check if plugin is already insalled
	$query = 'SELECT COUNT(*)'
		. ' FROM ' . $db->nameQuote('#__plugins')
		. ' WHERE ' . $db->nameQuote('element') . ' = '
		. $db->Quote('joscomment')
		. ' AND ' . $db->nameQuote('folder') . ' = '
		. $db->Quote('content');
	$db->setQuery($query);
	$pluginInstalled = (bool)$db->loadResult();

	if ($pluginInstalled) {
		echo '<p>'.JText::_( 'COMPOJOOMCOMMENT_THE_CONTENT_PLUGIN_IS_ALREADY_INSTALLED') . '</p>';
	}
	else {
		if (!$installer->install($pathToPlugin)) {
			echo '<p>' . JText::_('FAILED TO INSTALL THE CONTENT PLUGIN') . '</p>';
		}
		else {
			echo '<p>' . JText::_('INSTALLED THE CONTENT PLUGIN') . '</p>';

			$query = 'UPDATE ' . $db->nameQuote('#__plugins')
				. ' SET ' . $db->nameQuote('published') . ' = 1'
				. ' WHERE ' . $db->nameQuote('element') . ' = '
				. $db->Quote('joscomment')
				. ' AND '	. $db->nameQuote('folder') . ' = '
				. $db->quote('content');

			$db->setQuery($query);
			if (!$db->query()) {
				echo '<p>' . JText::_('FAILED TO ENABLE THE CONTENT PLUGIN') . '</p>';
			}
			else {
				echo '<p>' . JText::_('ENABLED THE CONTENT PLUGIN') . '</p>';
			}
		}
	}

	return true;
}
?>

