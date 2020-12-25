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
class JOSC_post extends JOSC_support {
	var $_post;
	var $_item;
	var $_css;
	var $_tree;
	var $_mlink_post;
	var $_tree_indent;
	var $_IP_visible;
	var $_IP_partial;
	var $_IP_caption;
	var $_IP_usertypes;
	var $_voting_visible;
	var $_avatar;
	var $_user_id;
	var $_use_name;
	var $_notify_users;
	var $_notify_moderator;
	private $_profile;

	function __construct($value) {
		$this->_post = $value;
	}

	/*
	 * AGE comment: the use of the following methods to just set the value seems heavy
	 * but this is a professional way to code from joomlacomment developpers
	*/
	function setItem($value) {
		$this->_item = $value;
		$this->setUser(); /* _use_name has to be set ! refresh comment user values according to */
	}

	function setUser() {
		$database =& JFactory::getDBO();

		if (!$this->_item || !$this->_item['userid'])
			return;

		$query = "SELECT * FROM #__users WHERE id='".$this->_item['userid']."' LIMIT 1";
		$database->SetQuery($query);
		$result = $database->loadAssocList();
		if ($result) {
			$user = $result[0];
			$this->_item['name']     = $this->_use_name ? $user['name'] : $user['username'];
			$this->_item['usertype'] = $user['usertype'];
			$this->_item['email']    = $user['email'];
		}

	}

	/**
	 *
	 * @param <type> $value
	 */
	public function setProfile($value) {
		$this->_profile = $value;
	}

	/**
	 *
	 * @param <type> $value
	 */
	public function setProfilesInfo($value) {
		$this->_profiles = $value;
	}

	function setCSS($value) {
		$this->_css = $value;
	}

	function setTree($value) {
		$this->_tree = $value;
	}

	function setMLinkPost($value) {
		$this->_mlink_post = $value;
	}

	function setTree_indent($value) {
		$this->_tree_indent = $value;
	}

	function setIP_visible($value) {
		$this->_IP_visible = $value;
	}

	function setIP_partial($value) {
		$this->_IP_partial = $value;
	}

	function setIP_caption($value) {
		$this->_IP_caption = $value;
	}

	function setIP_usertypes($value) {
		$this->_IP_usertypes = $value;
	}

	function setVoting_visible($value) {
		$this->_voting_visible = $value;
	}

	function setAvatar($value) {
		$this->_avatar = $value;
	}
	function setGravatarSupport($value) {
		$this->_gravatar = $value;
	}
	function setUseName($value) {
		$this->_use_name = $value;
	}

	function setUser_id($value) {
		$this->_user_id = $value;
	}

	function setNotify_users($users, $moderators) {
		$this->_notify_users = $users;
		$this->_notify_moderator = $moderators;
	}
	function setParagraphHandling($value) {
		$this->paragraphHandling = $value;
	}
	function highlightAdmin($usertype) {
		if ($usertype=='Super Administrator') $usertype = 'SAdministrator';

		if (JString::strpos($usertype, 'Administrator'))
			$usertype = "<span class='administrator'>$usertype</span>";
		return $usertype;
	}

	public function anonymous($name) {
		if ($name == '') {
			$name = JText::_('JOOMLACOMMENT_ANONYMOUS');
		}
		return $name;
	}

	function IP($ip, $usertype, $visible, $partial, $caption) {
		$user = JFactory::getUser();

		$int_usertype 	= JOSC_utils::getJOSCUserType($usertype); /* -1 for unresgistered */
		$int_myusertype = JOSC_utils::getJOSCUserType($user->usertype); /* -1 for unregistered */

		$html = "";

		if ($visible) {
			/* only if comment writer usertype is in _IP_usertypes */
			$visible = in_array($int_usertype, $this->_IP_usertypes);
		} elseif ($int_myusertype>=0) {
			/* not visible: only if my->usertype is in _IP_usertypes */
			$visible = in_array($int_myusertype, $this->_IP_usertypes);
		}

		if ($visible) {
			if ($int_usertype<0) {
				/* IP address */
				if ($partial) {
					$ip = JOSC_utils::partialIP($ip);
				}
				$html = $caption . $ip;
			} else {
				/* usertype string */
				$html = $this->highlightAdmin($usertype);
			}
		}
		return $html;
	}

	public function linkQuote($id) {
		return "<a href = 'javascript:JOSC_quote($id)'>" . JText::_('JOOMLACOMMENT_QUOTE', true) . "</a>";
	}

	public function linkPost($id) {
		return "<a href='javascript:JOSC_reply($id)'>" . JText::_('JOOMLACOMMENT_REPLY', true) . '</a>';
	}

	public function linkEdit($id) {
		return "<a href='javascript:JOSC_editComment($id)'>" . JText::_('JOOMLACOMMENT_EDIT') . '</a>';
	}

	public function linkPublishUnpublish($id) {
		if($this->_item['published']) {
			$button = "<a href='javascript:JOSC_publishUnpublishComment($id, 0)'>" . JText::_('COMPOJOOMCOMMENT_UNPUBLISH') . '</a>';
		} else {
			$button = "<a href='javascript:JOSC_publishUnpublishComment($id, 1)'>" . JText::_('COMPOJOOMCOMMENT_PUBLISH') . '</a>';
		}
		return $button;
	}

	public function linkDelete($id) {
		return "<a href='javascript:JOSC_deleteComment($id)'>" . JText::_('JOOMLACOMMENT_DELETE') . '</a>';
	}

	public function voting_cell($mode, $num, $id) {
		return "<li><div id='$mode$id' class='voting_$mode' onclick='JOSC_voting($id,\"$mode\")'><span>$num</span></div></li>";
	}

	function voting($voting_no, $voting_yes, $id, $contentId) {
		$html = '';
		if ($this->_voting_visible) {
			if ($voting_yes == '') {
				$voting_yes = 0;
				$voting_no = 0;
			}
			$html .= "<ul class='voting'>";
			$html .= $this->voting_cell('yes', $voting_yes, $id);
			$html .= $this->voting_cell('no', $voting_no, $id) ;
			$html .= '</ul>';
		}
		/*
		 * If voting no are 2x greater than voting yes => mode hide
		*/
		$this->_hide = (($voting_no + 1) > (($voting_yes + 1) * 2));
		return $html;
	}

	function parseUBBCode($html) {
		$ubbcode = new JOSC_ubbcode($html);
		$ubbcode->setMaxlength($this->_maxlength_word, $this->_maxlength_text, $this->_maxlength_line);
		$ubbcode->setSupport_emoticons($this->_support_emoticons);
		$ubbcode->setSupport_UBBcode($this->_support_UBBcode);
		$ubbcode->setSupport_quotecode($this->_support_quotecode);
		$ubbcode->setSupport_link($this->_support_link);
		$ubbcode->setSupport_pictures($this->_support_pictures,$this->_pictures_maxwidth);
		$ubbcode->setHide($this->_hide);
		$ubbcode->setEmoticons($this->_emoticons);
		$ubbcode->setEmoticons_path($this->_emoticons_path);
		$ubbcode->setParagraphHandling($this->paragraphHandling);
		$html = $ubbcode->ubbcode_parse();
		return($html);
	}

	function envelope($html, $id, $wrapnum) {
		$wrapnum = ($this->_tree) ? $wrapnum : 0;
		/*        $result = "<table class='postcontainer' id='post$id' width='100%' cellpadding='0' cellspacing='0' style='padding-left: $wrapnum;'>";
	$result .= "<tr><td><a name='josc$id'></a>$html</td></tr>";
	$result .= "</table>";*/
		$result = str_replace('{wrapnum}',$wrapnum, $html);
		return $result;
	}

	function setMaxLength($text) {
		return JOSC_utils::setMaxLength($text,$this->_maxlength_text);
	}


	/*
     * makes a profile link any of the supported systems
     *
     * @access public
     * @param mixed $s - user name
     * @param int $id - user id
     * @return - html link to profile or just the user name if id is missing
	*/
	function profileLink($s, $id) {
		if(!strcmp($id, '')) {
			return $this->profileLinkNone($s);
		}
		switch($this->_profile) {
			case 'CB':
				$profile = $this->profileLinkCB($s, $id);
				break;
			case 'JOMSOCIAL':
				$profile = $this->profileLinkJomSocial($s, $id);
				break;
			case 'K2':
				$profile = $this->profileLinkK2($s, $id);
				break;
			case '0' :
			default :
				$profile = $this->profileLinkNone($s);
				break;
		}

		return $profile;
	}

	/**
	 * Creates a link to Community Builder profile
     * @param mixed $s - user name
     * @param int $id - user id
     * @return - html link to profile
	 */
	public function profileLinkCB($s, $id) {
		$itemId = '';
		if(JOSC_utils::getItemid('com_community')) {
			$itemId = '&Itemid='.JOSC_utils::getItemid('com_comprofiler');
		}
		$link = JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$id.$itemId);
		return "<a title='".JText::_('JOOMLACOMMENT_USER_MEMBER')."' class='username' href='$link'>".$s."</a>";
	}

	/**
	 * Creates a link to JomSocial profile
     * @param mixed $s - user name or image
     * @param int $id - user id
     * @return - html link to profile
	 */
	public function profileLinkJomSocial($s, $id) {
		$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
		include_once($jspath.DS.'libraries'.DS.'core.php');

		$link = CRoute::_('index.php?option=com_community&view=profile&userid='.$id);

		return "<a title='".JText::_('JOOMLACOMMENT_USER_MEMBER')."' class='username' href='$link'>".$s."</a>";
	}

	/**
	 * Creates a link to K2 profile
     * @param mixed $s - user name or image
     * @param int $id - user id
     * @return - html link to profile
	 */
	public function profileLinkK2($s, $id) {
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php');
		$link = K2HelperRoute::getUserRoute($id);
		return "<a title='".JText::_('JOOMLACOMMENT_USER_MEMBER')."' class='username' href='$link'>".$s."</a>";
	}

	/**
	 * Just output the user name or real name
     * @param mixed $s - user name
     * @return - html link to profile
	 */
	public function profileLinkNone($s) {
		return "<span title='".JText::_('JOOMLACOMMENT_USER_ANONYMOUS')."' class='username'>".$s."</span>";
	}

	/**
	 *
	 * @param <type> $notify
	 * @param <type> $notify_users
	 * @return <type>
	 */
	function notifyLink($notify, $notify_users ) {
		$notifyactive = ($notify_users && $notify) ? '1' : '0';
		return "<span class='postnotify$notifyactive' title='".( $notifyactive ? ( JText::_('_JOOMLACOMMENT_NOTIFYTXT1') ) : JText::_('_JOOMLACOMMENT_NOTIFYTXT0') )."'></span>";
	}

	/**
	 * Checks which system is active and calls the appropriate avatar function
	 * @return <html> avatar
	 */
	public function getUserAvatar() {
		switch($this->_avatar) {
			case 'CB':
				$avatar = $this->getUserAvatarCB();
				break;
			case 'JOMSOCIAL':
				$avatar = $this->getUserAvatarJomSocial();
				break;
			case '0':
			default:
				$avatar = '';
				break;
		}

		return $avatar;
	}

	/**
	 * Gets JomSocial Avatar
	 * @return <html>
	 */
	public function getUserAvatarJomSocial() {
		$profile = '';
		$avatar = '';
		if(is_array($this->_profiles[$this->_item['userid']])) {
			$avatar = $this->_profiles[$this->_item['userid']]['avatar'];
		}
		if($avatar) {
			$path = JURI::base() . $avatar;
			$profile = $this->profileLink("<img class='avatar' src='$path' alt='avatar' />", $this->_user_id);
		} else {
			$profile = $this->getUserGravatar();
		}
		return $profile;
	}

	/**
	 * Gets community builder avatar
	 * @return <html>
	 */
	public function getUserAvatarCB() {
		$profile = '';
		$avatar = '';
		if(is_array($this->_profiles[$this->_item['userid']])) {
			$avatar = $this->_profiles[$this->_item['userid']]['avatar'];
		}
		if($avatar) {
			if(JString::strpos($avatar,"gallery/")===false) {
				$path = JURI::base()."images/comprofiler/tn$avatar";
			} else {
				$path = JURI::base()."images/comprofiler/$avatar";
			}
			$profile = $this->profileLink("<img class='avatar' src='$path' alt='avatar' />", $this->_user_id);
		} else {
			$profile = $this->getUserGravatar();
		}
		return $profile;
	}

	/**
	 * Gets the gravatar image
	 * @return <html>
	 */
	public function getUserGravatar() {
		$profile = '';
		if(!$this->_gravatar) {
			return $profile;
		}
		$gravatar_email = $this->_item['email'];
		if (file_exists(JPATH_BASE.DS.'components'.DS.'com_comment'.DS.'joscomment'.DS.'templates'.DS.$this->_template_name.DS.'images'.DS.'nophoto.png')) {
			$default = "$this->_template_path/$this->_template_name/images/nophoto.png";
		} else {
			$default = JURI::base().'components/com_comment/assets/images/nophoto.jpg';
		}
		$size = 64;
		// Prepare the gravatar image
		$path = "http://www.gravatar.com/avatar.php?gravatar_id=".md5(strtolower($gravatar_email)).
				"&amp;default=".urlencode($default)."&amp;size=".$size;

		$profile = $this->profileLink("<img class='avatar' src='$path' alt='avatar' />", $this->_user_id);

		return $profile;
	}

	public function restrictModerationByTime() {
		$now = strtotime(gmdate("M d Y H:i:s", time()-100*60));
		$date = new JDate($now);

		if($this->_item['date'] > $date->toFormat()) {
			return true;
		} else {
			return false;
		}
	}

	/* function to replace the markers in the html template with html output
	 * THIS LOGIC IS USED ALSO FOR MODULE
	 * so if changes are made, check also in module the result...
	 * use ' character instead of \" in html code
	 *
	 * @access public
	 * @return - markers replaced with html output
	*/
	function post_htmlCode() {

		$user = JFactory::getUser();
		$gid = $user->gid;

		/*
		 * prepare datas
		*/
		$id 		= $this->_item['id'];
		$name	 	= $this->censorText(JOSC_utils::filter($this->anonymous($this->_item['name'])));
		$website 	= $this->censorText(JOSC_utils::filter($this->_item['website']));
		$website	= htmlentities($website, ENT_QUOTES, 'UTF-8');
		$title 		= $this->censorText(JOSC_utils::filter($this->_item['title']));
		$comment 	= $this->censorText(JOSC_utils::filter($this->_item['comment']));
		$usertype 	= $this->_item['usertype'];
		$ip 		= $this->_item['ip'];
		$date 		= JOSC_utils::getLocalDate($this->_item['date'],$this->_date_format);

		if($this->_item['published']) {
			$published = 'josc_published';
		} else {
			$published = 'josc_unpublished';
		}

		/* */
		$isCommentModerator = JOSC_utils::isCommentModerator($this->_moderator, $this->_item['userid']); 
		$isModerator		= JOSC_utils::isModerator($this->_moderator);
		$notify 	= ($this->_notify_moderator && $usertype && $isModerator) ? '1' : $this->_item['notify'];

		if($isCommentModerator) {
			$isCommentModerator = $this->restrictModerationByTime();
		}
		$edit 		= '';
		/* reply : if not only moderator OR user is moderator */
		if ($this->_tree) {
			if ( !$this->_mlink_post || ($isModerator) )
				$edit = $this->linkPost($id);
		}
		/* quote */
		if ($this->_support_UBBcode) {
			$edit .= $this->linkQuote($id);
		}
		/* edit and delete */
		if ($isModerator || $isCommentModerator) {
			$edit .= $this->linkEdit($id);
			$edit .= $this->linkDelete($id);
		}
		if($isModerator) {
			$edit .= $this->linkPublishUnpublish($id);
		}

		$voting 	= $this->voting($this->_item['voting_no'], $this->_item['voting_yes'], $id, $this->_content_id);
		$comment	= $this->parseUBBCode($comment);

		/*
	 * parse template block
		*/
		$html 		= $this->_post;

		$NLsearch  = array();
		$NLsearch[]  = "\n";
		$NLsearch[] = "\r";
		$BRreplace = array();
		$BRreplace[] = "<br />";
		$BRreplace[] = " ";

		/*
	 * no blocks
		*/
		/* {id} 	*/
		$html 		= str_replace('{id}', $id , $html);

		/* {template_live_site} 	*/
		$html 		= str_replace('{template_live_site}', $this->_template_path.'/'.$this->_template_name, $html);
		/* {postclass} 	*/
		$html 		= str_replace('{postclass}', 'sectiontableentry' . $this->_css, $html);
		/* {username} 	*/
		$html 		= str_replace('{username}', $this->profileLink($name, $this->_user_id), $html);
		/* {date} 		*/
		$html 		= str_replace('{date}', $date, $html);
		/* {content}	*/
		$html 		= str_replace('{content}', $comment, $html);
		/* {content_js}	*/
		$html 		= str_replace('{content_js}', addslashes(str_replace($NLsearch, $BRreplace,$comment)), $html);
		/* {notify} 	*/
		$html 		= str_replace('{notify}', $this->notifyLink($notify, $this->_notify_users) , $html);

		/* {published} class */
		$html 		= str_replace('{published}', $published , $html);
		/*
	 * with blocks
		*/
		/* {avatar_picture} */
		if ($this->_avatar) {
			$display = true;
		} else if($this->_gravatar) {
			$display = $this->_gravatar;
		} else {
			$display = 0;
		}
		$html 		= JOSC_utils::checkBlock('BLOCK-avatar_picture', $display, $html);
		if ($this->_avatar) {
			$avatar_picture = $this->getUserAvatar();
			$html 	= str_replace('{avatar_picture}', $avatar_picture , $html);
		} else if ($this->_gravatar) {
			$gravatar = $this->getUserGravatar();
			$html = str_replace('{avatar_picture}', $gravatar, $html);
		}

		/* {website} 		*/
		$display	= ($website && (!$this->_website_registered || $user->gid > 0)) ? true : false;
		$html 		= JOSC_utils::checkBlock('BLOCK-website', $display, $html);
		if ($display) {
			$website = "<a class='postwebsite' rel='external nofollow' href='".$website."' title='".$website."' target='_blank'></a>";
			$html 		= str_replace('{website}', $website, $html);
		}
		/* {title} 		*/
		$display	= $title ? true : false;
		$html 		= JOSC_utils::checkBlock('BLOCK-title', $display, $html);
		if ($display) {
			$html	= str_replace('{title}', $title, $html);
			$html	= str_replace('{title_js}', addslashes(str_replace($NLsearch, $BRreplace,$title)), $html);
		}

		/* {usertype} 		*/
		$display	= $this->IP($ip, $usertype, $this->_IP_visible, $this->_IP_partial, $this->_IP_caption);
		$html 		= JOSC_utils::checkBlock('BLOCK-usertype', $display, $html);
		if ($display) {
			$html 	= str_replace('{usertype}', $display, $html);
		}

		$display 	= ((!$user->username && $this->_only_registered) || !$this->_ajax || ($edit == '')) ? false : true;
		$html 		= JOSC_utils::checkBlock('BLOCK-footer', $display, $html);
		if ($display) {
			/* {editbuttons} */
			$html 		= str_replace('{editbuttons}', $edit, $html);
			/* {voting} */
			$html 		= str_replace('{voting}', $voting, $html);
		}

		$wrapnum    = isset($this->_item['wrapnum']) ? $this->_item['wrapnum'] : 0;
		return $this->envelope($html, $id, ($wrapnum * $this->_tree_indent) . 'px');

	}

}

?>