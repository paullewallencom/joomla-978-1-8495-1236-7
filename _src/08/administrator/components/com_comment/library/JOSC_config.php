<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

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
require_once(JPATH_SITE. DS . 'components' . DS . 'com_comment' . DS . 'joscomment' . DS . 'utils.php');

class JOSC_config {
	protected $component = null;
	private static $instances = array();

	protected function __construct() {}
	protected function __clone() {}

	public static function getConfig($id, $comObject) {
		$component = $comObject->_component;
		if(!isset(self::$instances[$component])) {
			self::$instances[$component] = self::_createConfig($id, $component, $comObject);
		}
		return self::$instances[$component];
	}

	private function &_createConfig($id, $component, $comObject) {
		$application = JFactory::getApplication();
		$database =& JFactory::getDBO();

		if ($id) {
			$where = ' WHERE id = ' . $database->Quote($id);
		} else {
			$where = ' WHERE set_component = ' . $database->Quote($component);
		}

		$query = 'SELECT * FROM ' . $database->nameQuote('#__comment_setting')
			. $where;

		$database->setQuery($query);
		$row = $database->loadObject();

		if(!$application->isAdmin()) {
			if(!$row) {
				return $row;
			}
		}

		$params = new JParameter($row->params );

		$conf = new stdClass();
        /*
         * 'def' is doing 'get' with default value if not found.
         */

		$conf->_set_name = $row->set_name;
		$conf->_set_component = $row->set_component;
		$conf->_set_sectionid = $row->set_sectionid;

		if ($comObject != null) {
			$conf->_comObject = $comObject;
		} else {
			$backend = null;
			if(!$row->set_component) {
				$row->set_component = 'com_content';
			}
			$conf->_comObject = JOSC_utils::ComPluginObject($row->set_component, $backend, $id);
		}
		
		$conf->_complete_uninstall 		= $params->def( '_complete_uninstall'	, 0 );
		$conf->_mambot_func			    = $params->def( '_mambot_func', 'onPrepareContent' );
		$conf->_ajax					= $params->def( '_ajax', 1 );
		$conf->_only_registered			  = $params->def( '_only_registered', 0 );
		$conf->_moderator			     = $params->def( '_moderator', 0);
		$conf->_include_sc			    = $params->def( '_include_sc', 0);
		$conf->_exclude_sections 		= $params->def( '_exclude_sections', '');
		$conf->_exclude_categories 		= $params->def( '_exclude_categories'	, '');
		$conf->_exclude_contentitems	= $params->def( '_exclude_contentitems'	, '');
		$conf->_disable_additional_comments = $params->def('_disable_additional_comments', '');

		$conf->_template 				= $params->def( '_template'				, 'modern');
		$conf->_template_css 			= $params->def( '_template_css'			, 'standard.css');
		$conf->_copy_template			= $params->def( '_copy_template'		, 0);
		$conf->_template_custom			= $params->def( '_template_custom'		, '');
		$conf->_template_custom_css		= $params->def( '_template_custom_css'	, 'css.css');
		$template_info = self::setTemplateCustomPath();  /* custom_path , custom_livepath*/
		$conf->_template_custom_path = $template_info['_template_custom_path'];
		$conf->_template_custom_livepath = $template_info['_template_custom_livepath'];
		$conf->_template_modify			= $params->def( '_template_modify'		, 1);
		$conf->_template_library		= $params->def( '_template_library'		, 1);
		$conf->_form_area_cols			= $params->def( '_form_area_cols'		, 40);
		$conf->_emoticon_pack 			= $params->def( '_emoticon_pack'		, 'modern');
		$conf->_emoticon_wcount 		= $params->def( '_emoticon_wcount'		, 12);
		$conf->_tree 					= $params->def( '_tree'					, 1);
		$conf->_mlink_post 				= $params->def( '_mlink_post'			, 0);
		$conf->_tree_indent 			= $params->def( '_tree_indent'			, 20);
		$conf->_sort_downward 			= $params->def( '_sort_downward'		, 0);
		$conf->_display_num 			= $params->def( '_display_num'			, 0);
		$conf->_support_profiles 		= $params->def( '_support_profiles'		, 0);
		$conf->_support_avatars 		= $params->def( '_support_avatars'		, 0);
		$conf->_gravatar		= $params->def( '_gravatar'		, 0);
		$conf->_support_emoticons 		= $params->def( '_support_emoticons'	, 1);
		$conf->_enter_website 			= $params->def( '_enter_website'		, 1);
		$conf->_support_UBBcode 		= $params->def( '_support_UBBcode'		, 1);
		$conf->_support_pictures 		= $params->def( '_support_pictures'		, 0);
		$conf->_pictures_maxwidth 		= $params->def( '_pictures_maxwidth'	, '');
		$conf->_censorship_enable 		= $params->def( '_censorship_enable'	, 0);
		$conf->_censorship_case_sensitive = $params->def( '_censorship_case_sensitive', 0);
		$conf->_censorship_words 		= $params->def( '_censorship_words'		, 'nastybitch = nast***tch, motherfucker = moth****cker, fucking = fu**ing, twat, fisting, kokot = ko**t');
		$conf->_censorship_usertypes 	= $params->def( '_censorship_usertypes'	, '-1,3');
		$conf->_IP_visible 				= $params->def( '_IP_visible'			, 1);
		$conf->_IP_partial 				= $params->def( '_IP_partial'			, 1);
		$conf->_IP_caption 				= $params->def( '_IP_caption'			, '');
		$conf->_IP_usertypes 			= $params->def( '_IP_usertypes'			, '-1,0,1,2,3,4,5,6');
		$conf->_preview_visible 		= $params->def( '_preview_visible'		, 0);
		$conf->_preview_length 			= $params->def( '_preview_length'		, 80);
		$conf->_preview_lines 			= $params->def( '_preview_lines'		, 5);
		$conf->_voting_visible 			= $params->def( '_voting_visible'		, 1);
		$conf->_use_name 				= $params->def( '_use_name'				, 0);
		$conf->_notify_email 			= $params->def( '_notify_email'			, 'webmaster@mysite.com');
		$conf->_notify_moderator		= $params->def( '_notify_moderator'		, 0);
		$conf->_notify_users 			= $params->def( '_notify_users'			, 1);
		$conf->_rss 					= $params->def( '_rss'					, 0);
		$conf->_date_format 			= $params->def( '_date_format'			, '%Y-%m-%d %H:%M:%S');
		$conf->_no_search	 			= $params->def( '_no_search'			, 0);
		$conf->_captcha 				= $params->def( '_captcha'				, 1);
        $conf->_captcha_type			= $params->def( '_captcha_type'			, 0);
        $conf->_recaptcha_public_key	= $params->def( '_recaptcha_public_key'	, '');
        $conf->_recaptcha_private_key	= $params->def( '_recaptcha_private_key', '');
		$conf->_captcha_usertypes 		= $params->def( '_captcha_usertypes'	, '-1');
		$conf->_akismet_use             = $params->def( '_akismet_use'          , 0);
		$conf->_akismet_key             = $params->def( '_akismet_key'          , '');
		$conf->_website_registered 		= $params->def( '_website_registered'	, 0);
		$conf->_autopublish 			= $params->def( '_autopublish'			, 1);
		$conf->_ban 					= $params->def( '_ban'					, '');
		$conf->_maxlength_text 			= $params->def( '_maxlength_text'		, 1000);
		$conf->_maxlength_word 			= $params->def( '_maxlength_word'		, 80);
		$conf->_maxlength_line 			= $params->def( '_maxlength_line'		, -1);
		$conf->_show_readon 			= $params->def( '_show_readon'			, 1);
		$conf->_intro_only 				= $params->def( '_intro_only'			, 0);
		$conf->_menu_readon 			= $params->def( '_menu_readon'			, 1);

                /* technical default parameters */
		$conf->_debug_username				= $params->def( '_debug_username'	, '');
		$conf->_xmlerroralert			= $params->def( '_xmlerroralert'		, 0);
		$conf->_ajaxdebug				= $params->def( '_ajaxdebug'			, 0);

		return $conf;
	}

	private function setTemplateCustomPath($check=false,$copytemplate='') {
		if (!$check) {
			$params['_template_custom_path'] = '';
			$params['_template_custom_livepath'] = '';
		}
		$mediapath 		= JPATH_SITE . DS . "media";
		$absolute_path	= $mediapath. DS ."myjosctemplates";
		$livepath		= JURI::base() . "media/myjosctemplates";
		$standardpath 	= JPATH_SITE. DS . "components". DS. "com_comment". DS . "joscomment".DS."templates";

		if (!is_writable("$mediapath")) {
			return ($check ? "<SPAN style=\"color:red;\">$mediapath is not writable</SPAN>":"");
		}
    	/*
    	 * check directory and create if not exist
    	 */
		if (!@is_dir($absolute_path)) {
			if (!@mkdir($absolute_path, 0755))
				return ($check ? "<SPAN style=\"color:red;\">Unable to create directory '$absolute_path'</SPAN>":"");
		}
		if ($copytemplate) {
			/*
		 	 * if copytemplate = '*'
		 	 *      copy all standard templates (which are not already copied) in custom directory if not exist
		 	 * 	else copy only copytemplate to 'my'copytemplate
			 */
			$folderlist	= JOSC_library::folderList($standardpath, false, false);
			if ($folderlist) {
				foreach($folderlist as $template) {
					if ($copytemplate!='*' && $copytemplate!=$template)
						continue;
					if (!@is_dir($absolute_path.DS."my$template"))
						JOSC_library::copyDir($standardpath.DS . "$template", $absolute_path.DS ."my$template");
				}
			}
		}

		if ($check) {
			return "<SPAN style=\"color:green;\">$absolute_path is writable</SPAN>";
		} else {
			$params['_template_custom_path'] = $absolute_path;
			$params['_template_custom_livepath'] = $livepath;
		}

		return $params;
	}
}
?>