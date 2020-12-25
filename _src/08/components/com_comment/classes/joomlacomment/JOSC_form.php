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
class JOSC_form extends JOSC_support {
	var $_form;
	var $_captcha;
	var $_captcha_type;
	var $_recaptcha_public_key;
	var $_recaptcha_private_key;
	var $_recaptcha_language;
	var $_notify_users;
	var $_enter_website;
	var $_emoticon_wcount;
	var $_tname;
	var $_temail;
	var $_twebsite;
	var $_tnotify;
	var $_form_area_cols;

	public function setUserId($id) {
		$this->userId = $id;
	}
	function JOSC_form($value) {
		$this->_form = $value;
	}

	function setCaptcha($value) {
		$this->_captcha = $value;
	}
	function setCaptchaType($value) {
		$this->_captcha_type = $value;
	}
	function setRecaptchaPublicKey($value) {
		$this->_recaptcha_public_key = $value;
	}
	function setRecaptchaPrivateKey($value) {
		$this->_recaptcha_private_key = $value;
	}
	function setRecaptchaLanguage($value) {
		switch($value) {
			case "de-DE":
				$this->_recaptcha_language="de";
				break;
			default:
				$this->_recaptcha_language="en";
				break;
		}
	}

	function setGravatarSupport($value) {
		$this->_gravatar = $value;
	}

	function setAdditionalComments($value) {
		$this->_disable_additional_comments = explode(',',$value);
	}

	function setComObject($object) {
		$this->_comObject = $object;
	}
	function setNotifyUsers($value) {
		$this->_notify_users = $value;
	}

	function setEnterWebsite($value) {
		$this->_enter_website = $value;
	}

	function setEmoticonWCount($value) {
		$this->_emoticon_wcount = $value;
	}

	function set_tname($value) {
		$this->_tname = $value;
	}

	function set_temail($value) {
		$user = JFactory::getUser();
		/**
		 * if we don't disable the emailclock it brakes the email input
		 * form
		 */
		if(JPluginHelper::isEnabled('content','emailcloak')  && JRequest::getCmd('option') == 'com_content') {
			$emailCloak = '{emailcloak=off}';
		} else {
			$emailCloak = '';
		}
		$this->_temail = ($user->id ? JText::_('JOOMLACOMMENT_AUTOMATICEMAIL') : $emailCloak.$value); /* change also modify - ajax_quote ! */
	}

	function set_twebsite($value) {
		$this->_twebsite = $value;
	}

	function set_tnotify($value) {
		$this->_tnotify = $value;
	}

	function setFormAreaCols($value) {
		$this->_form_area_cols = $value;
	}

	public function onlyRegistered() {
		return '<div class="onlyregistered">' . JText::_('JOOMLACOMMENT_ONLYREGISTERED') . '</div>';
	}

	public function disableAdditionalComments() {
		return '<div class="nomore">' . JText::_('JOOMLACOMMENT_DISABLEADDITIONALCOMMENTS') . '</div>';
	}

	function readOnly($userId) {
		if ($userId) return 'disabled="disabled"';
		else return '';
	}

	function displayStyle($display) {
		return ($display) ? "" : "display:none;";
	}

	function emoticons($link=true) {
		if (!$this->_support_emoticons) return '';
		$html = "<div class='emoticoncontainer'>";
		$html .= "<div class='emoticonseparator'></div>";
		$index = 0;
		$icon_used = array();
		foreach ($GLOBALS["JOSC_emoticon"] as $ubb => $icon) {

			if (in_array($icon, $icon_used))
				continue;  /* ignore: avoid same icons twice ! */

			$icon_used[] = $icon;

			$html .= "<span class='emoticonseparator'>";
			$html .= "<span class='emoticon'>";
			$html .= $link ? "<a href='javascript:JOSC_emoticon(\"$ubb\")'>" : "";
			$html .= "<img src='$this->_emoticons_path/$icon' border='0' alt='$ubb' title='$ubb' />";
			$html .= $link ? "</a>":"";
			$html .= "</span></span>";
			$index++;
			if ($index == $this->_emoticon_wcount) {
				$index = 0;
				$html .= "<div class='emoticonseparator'></div>";
			}
		}
		$html .= '</div>';
		return "<div>$html</div>";
	}

	function loadUBBIcons(&$ubbIconList, $absolute_path, $live_site) {
		require_once($absolute_path.DS.'ubb_icons.php');
		if(is_array($ubbIcons)) {
			foreach($ubbIcons as $name => $icon) {
				$ubbIconList[$name] = "$live_site/$icon";
			}
		}
	}

	function UBBCodeButtons() {
		$absolute_path = "$this->_template_absolute_path/$this->_template_name/images";
		$live_site = "$this->_template_path/$this->_template_name/images";
		$ubbIconList = array();
		$this->loadUBBIcons($ubbIconList, "$this->_absolute_path/images", "$this->_live_site/images");
		if (file_exists("$absolute_path/ubb_icons.php")) {
			$this->loadUBBIcons($ubbIconList, $absolute_path, $live_site);
		}
		$html = "<a href='javascript:JOSC_insertUBBTag(\"b\")'><img src='" . $ubbIconList['bold'] . "' class='buttonBB' name='bb' alt='[b]' /></a>&nbsp;";
		$html .= "<a href='javascript:JOSC_insertUBBTag(\"i\")'><img src='" . $ubbIconList['italicize'] . "' class='buttonBB' name='bi' alt='[i]' /></a>&nbsp;";
		$html .= "<a href='javascript:JOSC_insertUBBTag(\"u\")'><img src='" . $ubbIconList['underline'] . "' class='buttonBB' name='bu' alt='[u]' /></a>&nbsp;";
		$html .= "<a href='javascript:JOSC_insertUBBTag(\"s\")'><img src='" . $ubbIconList['strike'] . "' class='buttonBB' name='bs' alt='[s]' /></a>&nbsp;";
		$html .= "<a href='javascript:JOSC_insertUBBTag(\"url\")'><img src='" . $ubbIconList['url'] . "' class='buttonBB' name='burl' alt='[url]' /></a>&nbsp;";
		$html .= "<a href='javascript:JOSC_insertUBBTag(\"quote\")'><img src='" . $ubbIconList['quote'] . "' class='buttonBB' name='bquote' alt='[quote]' /></a>&nbsp;";
		$html .= "<a href='javascript:JOSC_insertUBBTag(\"code\")'><img src='" . $ubbIconList['code'] . "' class='buttonBB' name='bcode' alt='[code]' /></a>&nbsp;";
		$html .= "<a href='javascript:JOSC_insertUBBTag(\"img\")'><img src='" . $ubbIconList['image'] . "' class='buttonBB' name='bimg' alt='[img]' /></a>&nbsp;";
		return $html;
	}

	function UBBCodeSelect() {
		$html = '';
		$html .= "<select name='menuColor' class='select' onchange='JOSC_fontColor()'>";
		$html .= "<option>-" . JText::_('JOOMLACOMMENT_COLOR') . "-</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_AQUA') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_BLACK') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_BLUE') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_FUCHSIA') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_GRAY') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_GREEN') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_LIME') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_MAROON') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_NAVY') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_OLIVE') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_PURPLE') . "</option>";
		$html .= "<option>" .JText::_('JOOMLACOMMENT_RED') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_SILVER') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_TEAL') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_WHITE') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_YELLOW') . "</option>";
		$html .= "</select>&nbsp;";
		$html .= "<select name='menuSize' class='select' onchange='JOSC_fontSize()'>";
		$html .= "<option>-" . JText::_('JOOMLACOMMENT_SIZE') . "-</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_TINY') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_SMALL') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_MEDIUM') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_LARGE') . "</option>";
		$html .= "<option>" . JText::_('JOOMLACOMMENT_HUGE') . "</option>";
		$html .= "</select>";
		return $html;
	}

	function notifySelect() {
		$html = "<select name='tnotify' id='tnotify' class='inputbox' tabindex='3'>";
		$html .= "<option value='0' ".($this->_tnotify ?  "" : "selected=\"selected\"")	." >" 	. JText::_('JOOMLACOMMENT_ENTERNOTIFY0') . "</option>";
		$html .= "<option value='1' ".($this->_tnotify ? "selected=\"selected\"" : "")	." >" 	. JText::_('JOOMLACOMMENT_ENTERNOTIFY1') . "</option>";
		$html .= '</select>';
		return $html;
	}

	function disable_form($hidden) {
		$html = "<form name='joomlacommentform' method='post' action='PHP_SELF'>";
		$html .= $hidden;
		$html .= "<table class='buttoncontainer' style='display:none;' cellpadding='0' cellspacing='0'>";
		$html .= "<tr>";
		$html .= "<td><input type='button' class='button' name='bsend' value='{_SENDFORM}' onclick='JOSC_editPost(-1,-1)' /></td>";
		$html .= "<td id='JOSC_busy'></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .= "</form>";
		return $html;
	}

	function form_htmlCode() {

		$user =& JFactory::getUser();
		$gid = $user->gid;
		$hidden = $this->formHiddenValues($this->_content_id, $this->_component, $this->_sectionid);
		$disabled_more_comments = $this->_disable_additional_comments;

		if ((!$user->username && $this->_only_registered)) {
			$html = $this->onlyRegistered();
			/* needed informations but hidden : */
			$html .= $this->disable_form($hidden);
			return $html;
		}
		$id = $this->_comObject->getPageId();

		if(in_array($id, $disabled_more_comments)) {

			/* needed informations but hidden : */
			$html = $this->disableAdditionalComments();
			$html .= $this->disable_form($hidden);
			return $html;

		}

		/*
		 * parse template block _form
		*/
		$html = $this->_form;

		/*
         * No blocks
		*/
		$html = str_replace('{_WRITECOMMENT}', JText::_('JOOMLACOMMENT_WRITECOMMENT'), $html);
		$html = str_replace('{self}', 'index.php', $html);
		$html = str_replace('{id}', $this->_content_id, $html);

		$html = str_replace('{_HIDDEN_VALUES}', $hidden, $html);

		$html = str_replace('{template_live_site}', $this->_template_path.'/'.$this->_template_name, $html);
		$html = str_replace('{formareacols}', $this->_form_area_cols, $html);

		$html = str_replace('{_COMMENT}', JText::_('JOOMLACOMMENT_COMMENTS_1'), $html);
		$html = str_replace('{_YOUR_CONTACT_DETAILS}', JText::_('YOUR_CONTACT_DETAILS'), $html);
		$html = str_replace('{_SECURITY}', JText::_('SECURITY'), $html);
		$html = str_replace('{_MESSAGE}', JText::_('MESSAGE'), $html);

		$html = str_replace('{_ENTERNAME}', JText::_('JOOMLACOMMENT_ENTERNAME'), $html);
		$html = str_replace('{username}', $this->_tname, $html);
		$html = str_replace('{registered_readonly}', $this->readOnly($this->userId), $html);

		if ($this->_gravatar) {
			$html = str_replace('{gravatar}', JText::_('JOOMLACOMMENT_GRAVATAR_ENABLED') , $html);
		} else {
			$html = str_replace('{gravatar}', '' , $html);
		}

		$html = str_replace('{_ENTERTITLE}', JText::_('JOOMLACOMMENT_ENTERTITLE'), $html);

		$html = str_replace('{_SENDFORM}', JText::_('JOOMLACOMMENT_SENDFORM'), $html);
		/*
         * With blocks
		*/
		/* {_UBBCODE} {UBBCodeButtons} {UBBCodeSelect}	*/
		$display	= $this->_support_UBBcode;
		$html 		= JOSC_utils::checkBlock('BLOCK-_UBBCODE', $display, $html);
		if ($display) {
			$UBBCodeButtons = $this->UBBCodeButtons();
			$UBBCodeSelect = $this->UBBCodeSelect();
			$html = str_replace('{_UBBCODE}', JText::_('JOOMLACOMMENT_UBBCODE'), $html);
			$html = str_replace('{UBBCodeButtons}', $UBBCodeButtons, $html);
			$html = str_replace('{UBBCodeSelect}', $UBBCodeSelect, $html);
		}

		/* {_CAPTCHATXT} {security_image}				*/
		$display	= $this->_captcha;
		$html 		= JOSC_utils::checkBlock('BLOCK-_CAPTCHATXT', $display, $html);
		if ($display) {
			$html = str_replace('{security_image}', "<div id='captcha'>", $html);
			$html = str_replace('{security_lang}', $this->_recaptcha_language, $html);
			$html = str_replace('{/security_image}', JOSC_security::insertCaptcha('security_refid',$this->_captcha_type,$this->_recaptcha_public_key). '</div>', $html);

			/* $html = str_replace('{security_image}', "<div id='captcha'><script>var RecaptchaOptions = {lang : '".$this->_recaptcha_language."'};</script>"
            . JOSC_security::insertCaptcha('security_refid',$this->_captcha_type,$this->_recaptcha_public_key). '</div>', $html);*/
			if($this->_captcha_type=="recaptcha") {
				$html = str_replace('{CAPTCHALBL}', 'reCAPTCHA:', $html);
				$html = str_replace('{recaptcha_mode}', 'true', $html);
				$html = str_replace('{recaptcha_img}', '"'.JText::_('JOOMLACOMMENT_RECAPTCHA_IMG').'"', $html);
				$html = str_replace('{recaptcha_snd}', '"'.JText::_('JOOMLACOMMENT_RECAPTCHA_SND').'"', $html);
				$html = str_replace('{_CAPTCHATXT}', '', $html);
				$html = str_replace('{security_input}', "", $html);
			} else {
				$html = str_replace('{CAPTCHALBL}', '', $html);
				$html = str_replace('{recaptcha_mode}', 'false', $html);
				$html = str_replace('{_CAPTCHATXT}', JText::_('JOOMLACOMMENT_FORMVALIDATE_CAPTCHATXT'), $html);
				$html = str_replace('{security_input}', "<input type='text' name='security_try' id='security_try' size='15' maxlength='10' tabindex='7' class='captchainput' />", $html);
				// TODO - Better tabindex handling, should not be hard-coded here
			}
		}

		/* {_ENTEREMAIL} {email} {notifyselect}			*/
		$display	= $this->_notify_users;
		$html 		= JOSC_utils::checkBlock('BLOCK-_ENTEREMAIL', $display, $html);
		if ($display) {
			$html = str_replace('{_ENTEREMAIL}', JText::_('JOOMLACOMMENT_ENTEREMAIL'), $html);
			$html = str_replace('{email}', $this->_temail, $html);
			$html = str_replace('{notifyselect}', $this->_notify_users ? $this->notifySelect():"", $html);
		}

		/* {_ENTERWEBSITE} {website} 					*/
		$display	= $this->_enter_website;
		$html 		= JOSC_utils::checkBlock('BLOCK-_ENTERWEBSITE', $display, $html);
		if ($display) {
			$html = str_replace('{_ENTERWEBSITE}', JText::_('JOOMLACOMMENT_ENTERWEBSITE'), $html);
			$html = str_replace('{website}', $this->_twebsite, $html);
		}

		/* {emoticons} 									*/
		$display	= $this->emoticons();
		$html 		= JOSC_utils::checkBlock('BLOCK-emoticons', $display, $html);
		if ($display) {
			$html = str_replace('{emoticons}', $display, $html);
		}

		return $html;
	}

}

?>
