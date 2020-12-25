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

class JOSC_properties extends JOSC_template {
	/* special parameters */
	public $_contentrow;
	public $_params;
	public $_component;
	public $_sectionid;
	public $_comObject;
	public $_limitstart;
	public $_total;
	public $_request_uri;
	/* config params */
	public $_ajax;
	public $_local_charset;
	public $_only_registered;
	public $_language;
	public $_moderator = array();
	public $_include_sc;
	public $_exclude_sections = array();
	public $_exclude_categories = array();
	public $_exclude_contentitems = array();
	public $_exclude_contentids = array();
	public $_template;
	public $_template_css;
	public $_form_area_cols;
	public $_emoticon_pack;
	public $_emoticon_wcount;
	public $_tree;
	public $_mlink_post;
	public $_tree_indent;
	public $_sort_downward;
	public $_display_num;
	public $_support_emoticons;
	public $_support_UBBcode;
	public $_support_pictures;
	public $_censorship_enable;
	public $_censorship_case_sensitive;
	public $_censorship_words;
	public $_censorship_usertypes;
	public $_IP_visible;
	public $_IP_partial;
	public $_IP_caption;
	public $_IP_usertypes;
	public $_preview_visible;
	public $_preview_length;
	public $_preview_lines;
	public $_voting_visible;
	public $_use_name;
	public $_notify_admin;
	public $_notify_email;
	public $_notify_moderator;
	public $_notify_users;
	public $_rss;
	public $_date_format;
	public $_no_search;
	public $_captcha;
	public $_captcha_type;
	public $_recaptcha_public_key;
	public $_recaptcha_private_key;
	public $_autopublish;
	public $_ban;
	public $_avatar;
	public $_profile;
	public $_profiles;
	public $_maxlength_text;
	public $_maxlength_word;
	public $_maxlength_line;
	public $_show_readon;
	public $_debug_username;
	public $_xmlerroralert;
	public $_ajaxdebug;
	public $_akismet_use;
	public $_akismet_key;
	public $_gravatar;
	public $_disable_additional_comments;

	public function __construct($absolutePath, $liveSite, &$comObject, &$exclude, &$row, &$params) {
		$user =& JFactory::getUser();

		$config = JOSC_config::getConfig(0, $comObject);

		if($config == false) {
			return $config;
		}

		$this->_comObject		= $comObject;
		$this->_component 		= $this->_comObject->_component;
		$this->_sectionid 		= $this->_comObject->_sectionid;
		$this->_content_id 		= $this->_comObject->_id;

		$language = JFactory::getLanguage();
		$language->load('com_comment');

		if ($exclude && isset($row)) {
			//determines wheter or not we are on the frontpage
			$visible = $comObject->checkVisual($this->_content_id );
			$this->_show_readon			= $comObject->setShowReadon( $row, $params, $config );
			$this->_exclude_contentitems = $config->_exclude_contentitems ? explode(',', $config->_exclude_contentitems) : array();
			$this->_exclude_sections	= $config->_exclude_sections ? explode(',', $config->_exclude_sections) : array();
			$this->_exclude_categories	= $config->_exclude_categories ? explode(',', $config->_exclude_categories) : array();
			$this->_include_sc			= $config->_include_sc;
			$this->_preview_visible = $config->_preview_visible;
			$this->_preview_length 	= $config->_preview_length;
			$this->_preview_lines 	= $config->_preview_lines;
			if ($this->_comObject->_official) {
				$obj = $this;
				if (!$comObject->checkSectionCategory($row, $obj ))
					return false;
			} else {
				if (!$comObject->checkSectionCategory($row, $this->_include_sc, $this->_exclude_sections, $this->_exclude_categories, $this->_exclude_contentitems ))
					return false;
			}
		} else {
			// since exclude is not set when we post an ajax comment we need $visible
			// to be true, otherwise we will not have the configuration
			$visible = true;
		}

		$this->_absolute_path 	= $absolutePath;
		$this->_live_site 		= $liveSite;

		$this->_template 				= $config->_template_custom ? $config->_template_custom : $config->_template;
		$this->_template_path 			= $config->_template_custom ? $config->_template_custom_livepath : "$liveSite/templates";
		$this->_template_absolute_path 	= $config->_template_custom ? $config->_template_custom_path : "$absolutePath/templates";
		$this->_template_css			= $config->_template_custom ? $config->_template_custom_css : $config->_template_css;
		$this->JOSC_template($this->_template, $this->_template_css);

		//	if we are on the frontpage we don't need to load the whole configuration
		if($visible) {
			$this->_moderator 		= explode(',', $config->_moderator);
			$this->_ajax 			= $config->_ajax;
			$this->_only_registered = $config->_only_registered;
			$this->_tree 			= $config->_tree;
			$this->_mlink_post 		= $config->_mlink_post;
			$this->_tree_indent 	= $config->_tree_indent;
			$this->_sort_downward 	= $config->_sort_downward; //($this->_tree ? 0 : $config->_sort_downward);
			$this->_display_num 	= $config->_display_num;
			$this->_support_emoticons = $config->_support_emoticons;
			$this->_enter_website	= $config->_enter_website;
			$this->_support_UBBcode = $config->_support_UBBcode;
			$this->_support_pictures = $config->_support_pictures;
			$this->_pictures_maxwidth = $config->_pictures_maxwidth;
			$this->_censorship_enable = $config->_censorship_enable && in_array(JOSC_utils::getJOSCUserType($user->usertype), explode(',', $config->_censorship_usertypes));
			$this->_censorship_case_sensitive = $config->_censorship_case_sensitive;
			$this->Set_censorship_words($config->_censorship_words);
			$this->_IP_usertypes 	= explode(',', $config->_IP_usertypes);
			$this->_IP_visible 		= $config->_IP_visible;
			$this->_IP_partial 		= $config->_IP_partial;
			$this->_IP_caption 		= $config->_IP_caption;
			$this->_voting_visible 	= $config->_voting_visible;
			$this->_use_name 		= $config->_use_name;
			$this->_notify_email 	= $config->_notify_email;
			$this->_notify_moderator = $config->_notify_moderator;
			$this->_autopublish 	= $config->_autopublish;
			$this->_notify_users 	= $config->_notify_users;
			$this->_rss 			= $config->_rss;
			$this->_date_format 	= $config->_date_format;
			$this->_no_search		= $config->_no_search;
			$this->_captcha 		= $config->_captcha && in_array(JOSC_utils::getJOSCUserType($user->usertype), explode(',', $config->_captcha_usertypes));
			$this->_captcha_type                = $config->_captcha_type;
			$this->_recaptcha_public_key        = $config->_recaptcha_public_key;
			$this->_recaptcha_private_key       = $config->_recaptcha_private_key;
			$this->_website_registered = $config->_website_registered;
			$this->_ban 			= $config->_ban;
			$this->_profile 		= $config->_support_profiles;
			$this->_avatar 			= $config->_support_avatars;
			$this->_gravatar		= $config->_gravatar;
			$this->_maxlength_text 	= $config->_maxlength_text;
			$this->_maxlength_word 	= $config->_maxlength_word;
			$this->_maxlength_line 	= $config->_maxlength_line;
			$this->_template_library		= $config->_template_library;
			$this->_form_area_cols	= $config->_form_area_cols;
			$this->_emoticon_pack 	= $config->_emoticon_pack;
			$this->_emoticon_wcount = $config->_emoticon_wcount;
			$this->_emoticons_path = $liveSite . "/emoticons/$this->_emoticon_pack/images";
			$this->loadEmoticons($absolutePath . DS . 'emoticons' . DS . $this->_emoticon_pack . DS .'index.php');
			$this->_debug_username = $config->_debug_username;
			$this->_xmlerroralert = $config->_xmlerroralert ? '1' : '0';
			$this->_ajaxdebug = $config->_ajaxdebug ? '1' : '0';
			$this->_akismet_use = $config->_akismet_use;
			$this->_akismet_key = $config->_akismet_key;
			$this->_disable_additional_comments = $config->_disable_additional_comments;
		}
		$exclude = false;
	}

	function Set_censorship_words($censorship_words) {
		$this->_censorship_words = array();

		if ($this->_censorship_enable && $censorship_words) {

			$censorship_words = explode(',', $censorship_words);

			if (is_array($censorship_words)) {

				foreach($censorship_words as $word) {

					$word = trim($word);

					if (JString::strpos($word, '=')) {
						$word = explode('=', $word);
						$from = trim($word[0]);
						$to   = trim($word[1]);
					} else {
						$from = $word;
						$to   = JOSC_strutils::str_fill(JString::strlen($word), '*');
					}

					$this->_censorship_words[$from] = $to;
				}
			}
		}
		return;
	}

	function jscriptInit() {
		$user =& JFactory::getUser();

		$html = "\n<script type='text/javascript'>\n";
		$html .= "var JOSC_ajaxEnabled=$this->_ajax;";
		$html .= "if (!JOSC_http) JOSC_ajaxEnabled=false;";
		$html .= "var JOSC_sortDownward='$this->_sort_downward';";
		$captchaEnabled = $this->_captcha ? 'true' : 'false';
		$html .= "var JOSC_captchaEnabled=$captchaEnabled;";
		$html .= "var JOSC_captchaType='$this->_captcha_type';";
		$html .= "var JOSC_template='$this->_template_path/$this->_name';";
		$html .= "var JOSC_liveSite='$this->_live_site';"; /* joscomment */
		$html .= "var JOSC_ConfigLiveSite='".JURI::base()."';";
		$html .= "var JOSC_linkToContent='".$this->_comObject->linkToContent( $this->_content_id )."';";
		$html .= "var JOSC_autopublish='$this->_autopublish';"; /* not used ?*/
		if ($this->_debug_username && ($user->username==$this->_debug_username || $this->_debug_username=="JOSCdebugactive")) {
			$html .= "var JOSC_XmlErrorAlert=$this->_xmlerroralert;";
			$html .= "var JOSC_AjaxDebug=$this->_ajaxdebug;";
		}

		$html .= "\n</script>\n";
		return $html;
	}

	function loadEmoticons($fileName) {
		require_once($fileName);
		$this->_emoticons = $GLOBALS["JOSC_emoticon"];
	}
}
?>