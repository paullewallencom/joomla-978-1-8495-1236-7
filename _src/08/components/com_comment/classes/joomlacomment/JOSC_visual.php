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
*  This script is part of the Compojoom Comment project. The Compojoom comment project is
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


class JOSC_visual extends JOSC_properties {
	var $_parent_id = -1;
	
	function insertMenu() {
		$menu = new JOSC_menu($this->_menu);
		$menu->setContentId($this->_content_id);
		$menu->setTemplate_path($this->_template_path);
		$menu->setTemplate_name($this->_name);
		$menu->setRSS($this->_rss);
		$menu->setModerator($this->_moderator);
		$menu->setOnly_registered($this->_only_registered);
		$menu->setNoSearch($this->_no_search);
		return $menu->menu_htmlCode();
	}

	function insertPoweredby() {
		return '<div id="poweredby" align="center" class="small">Powered by <a target="_blank" href="http://compojoom.com">Compojoom comment '.CC_VERSION.'</a></div>';
		
	}

	function insertSearch() {
		$html = $this->_search;
		$hidden = JOSC_support::formHiddenValues($this->_content_id, $this->_component, $this->_sectionid);
		$html = str_replace('{_HIDDEN_VALUES}', $hidden, $html);
		$html = str_replace('{_JOOMLACOMMENT_SEARCH}', JOSC_utils::filter(JText::_('JOOMLACOMMENT_SEARCH')), $html);
		$html = str_replace('{_JOOMLACOMMENT_PROMPT_KEYWORD}', JOSC_utils::filter(JText::_('JOOMLACOMMENT_PROMPT_KEYWORD')), $html);
		$html = str_replace('{_JOOMLACOMMENT_SEARCH_ANYWORDS}', JOSC_utils::filter(JText::_('JOOMLACOMMENT_SEARCH_ANYWORDS')), $html);
		$html = str_replace('{_JOOMLACOMMENT_SEARCH_ALLWORDS}', JOSC_utils::filter(JText::_('JOOMLACOMMENT_SEARCH_ALLWORDS')), $html);
		$html = str_replace('{_JOOMLACOMMENT_SEARCH_PHRASE}', JOSC_utils::filter(JText::_('JOOMLACOMMENT_SEARCH_PHRASE')), $html);
		return $html;
	}

	function initializePost($item, $postCSS) {   /* post is used in module latest... ! */
		$post = new JOSC_post($this->_post); /* template block */
		$post->setUseName($this->_use_name); /* needed for setItem */
		$post->setItem($item);
		$post->setTemplate_path($this->_template_path);
		$post->setTemplate_name($this->_name);
		$post->setCSS($postCSS);
		$post->setAjax($this->_ajax);
		$post->setTree($this->_tree);
		$post->setMLinkPost($this->_mlink_post);
		$post->setTree_indent($this->_tree_indent);
		$post->setDate_format($this->_date_format);
		$post->setIP_visible($this->_IP_visible);
		$post->setIP_partial($this->_IP_partial);
		$post->setIP_caption($this->_IP_caption);
		$post->setIP_usertypes($this->_IP_usertypes);
		$post->setCensorShip(	$this->_censorship_enable,
				$this->_censorship_case_sensitive,
				$this->_censorship_words,
				$this->_censorship_usertypes
		);
		$post->setContentId($this->_content_id);
		$post->setComponent($this->_component);
		$post->setVoting_visible($this->_voting_visible);
		$post->setSupport_emoticons($this->_support_emoticons);
		$post->setSupport_UBBcode($this->_support_UBBcode);
		$post->setSupport_quotecode($this->_support_UBBcode); /* only module use */
		$post->setSupport_link($this->_support_UBBcode); /* only module use */
		$post->setSupport_pictures($this->_support_pictures, $this->_pictures_maxwidth);
		$post->setEmoticons($this->_emoticons);
		$post->setEmoticons_path($this->_emoticons_path);
		$post->setOnly_registered($this->_only_registered);
		$post->setWebsiteRegistered($this->_website_registered);
		$post->setModerator($this->_moderator);
		$post->setProfilesInfo($this->_profiles);
		if ($post->_item['userid']) {
			$post->setProfile($this->_profile);
			$post->setUser_id( $post->_item['userid']);
			$post->setAvatar($this->_avatar  );
			}
		$post->setNotify_users($this->_notify_users, $this->_notify_moderator);
		$post->setMaxLength_text($this->_maxlength_text);
		$post->setMaxLength_word($this->_maxlength_word);
		$post->setMaxLength_line($this->_maxlength_line);
		$post->setGravatarSupport($this->_gravatar);
		$post->setParagraphHandling(true);
		return $post;
	}

	function insertPost($item, $postCSS) {   /* post is used in module latest... ! */
		$post = $this->initializePost($item, $postCSS);
		return( $post->post_htmlCode() );
	}

	function getPageNav() {
		$document =& JFactory::getDocument();
		if ($this->_total <= $this->_display_num) return '';

		$pageNav = new JOSC_PageNav( $this->_ajax, $this->_total, $this->_limitstart, $this->_display_num );

		$link = $this->_request_uri;
		/* delete limit and limitstart parameters before add new */
		$link = preg_replace("/(.*)(&josclimit=.*)(&.*|)/", '\\1\\3', $link);
		$link = preg_replace("/(.*)(&josclimitstart=.*)(&.*|)/", '\\1\\3', $link);

		$html = "<div id='joscPageNavLink'>".$pageNav->writePagesLinks( $link, "#joscpagenav" )."</div>";

		if ($this->_ajax)
			$html.=  "<div id='joscPageNavNoLink' style='display:none;visibility:hidden;'>".$pageNav->writePagesLinks('')."</div>";

		if ($this->_sort_downward) {
			/* DESC  addeed to begin -> if not begin needs refresh*/
			if ($this->_limitstart <= $this->_display_num)
				$document->addScriptDeclaration("var JOSC_postREFRESH=false;");
			else
				$document->addScriptDeclaration("var JOSC_postREFRESH=true;");
		} else {
			/* ASC addeed to end -> if not end needs refresh */
			if (($this->_limitstart+$this->_display_num)>=$this->_total)
				$document->addScriptDeclaration("var JOSC_postREFRESH=false;");
			else
				$document->addScriptDeclaration("var JOSC_postREFRESH=true;");
		}

		return $html;

	}
	/*
         * $type = type of the navigation (top or bottom)
	*/
	function insertPageNav() {
		return '<div id="joscPageNav">'.$this->getPageNav()."</div>";
	}

	function getComments($onlydata=false) {
		$database =& JFactory::getDBO();

		if ($this->_sort_downward) {
			$sort = 'DESC'; /* new first */
		} else {
			$sort = 'ASC'; /* last first */
		}
		$html = '';
		$com = $this->_component;
		JPluginHelper::importPlugin( 'compojoomcomment' );
        $dispatcher = JDispatcher::getInstance();
        $alternativeComments = $dispatcher->trigger('onCommentGetAlternativeComments', array($this));


		if(isset($alternativeComments[0]) && count($alternativeComments[0])) {
			$data = $alternativeComments[0];

		} else {
			$data = '';
		}

		if(!$data) {

			if(JOSC_utils::isModerator($this->_moderator)) {
				$published = '';
			} else {
				$published = ' AND published = 1';
			}
			/*
			 * ORDER must be done only on high level
			 * because children must be ordered ascending for tree construction
			*/
			$queryselect 	= "SELECT * ";
			$querycount 	= "SELECT COUNT(*) ";
			$queryfrom 		= "\nFROM #__comment"
					. "\n WHERE contentid='$this->_content_id' AND component='$com' "
					. $published;
			$queryparent	= $this->_tree ? "\n   AND parentid<=0 " : "";
			$querychildren	= $this->_tree ? "\n   AND parentid>0 "  : "";
			$queryorder		= "\n ORDER BY id $sort";

			if ($this->_display_num>0) {

				/*
				 * pages -> use limitstart on root id (childs are not counted - always attached to root id)
				*/

				if ($this->_comment_id) {
					/*
					 * - get the limitstart(page) of the comment_id
					 * - comment id can be a root id but also a child !
					 * in this case, we must search for its root id.
					*/
					$parentid = $id = $this->_comment_id;
					for ($i=1; $i<=20 && $parentid>0; $i++) {   /* LEFT JOIN is for loop optimization : 1 loop = 2 levels */
						/* 20 times is for infinity loop limit = maximum 40 levels.  it should be enough....? :) */
						$query 	= "SELECT c.id, c.parentid, p.id AS p_id, p.parentid AS p_parentid "
								. "\n FROM #__comment AS c LEFT JOIN #__comment AS p ON c.parentid=p.id "
								. "\n    WHERE c.id=$parentid LIMIT 1";
						$database->SetQuery($query);
						$row = $database->loadAssocList();
						if ($row=$row[0]) {
							$id = $row['id'];
							$parentid = $row['parentid'];
							if ($row['parentid']>0) {
								$id = $row['p_id'];
								$parentid=$row['p_parentid'];
							}
						} else {
							$id = $parentid = -1;
						}
					}
					if ($id) {
						/* get the limitstart from the root id */
						$database->SetQuery("SELECT id ".$queryfrom.$queryparent.$queryorder);
						$data = $database->loadResultArray();
						$i = array_search($id, $data);
						if ($i) {
							$this->_limitstart = $i;
						}
					}
				}

				$database->SetQuery($querycount.$queryfrom.$queryparent.$queryorder);
				$this->_total = $database->loadResult();
				$checklimit = new JOSC_PageNav($this->_ajax, $this->_total, $this->_limitstart, $this->_display_num);
				$this->_limitstart = $checklimit->limitstart;
				$database->SetQuery($queryselect.$queryfrom.$queryparent.$queryorder, $this->_limitstart, $this->_display_num);
				$dataparent = $database->loadAssocList();

			} else {
				$database->SetQuery($queryselect.$queryfrom.$queryparent.$queryorder);
				$dataparent = $database->loadAssocList();
			}
			if ($this->_tree) {
				$database->SetQuery($queryselect.$queryfrom.$querychildren.$queryorder);
				$datachildren = $database->loadAssocList();
				$data = ($dataparent && count($datachildren)>0) ?  array_merge($dataparent,$datachildren) : $dataparent;
			} else {
				$data = $dataparent;
			}
			$postCSS = 1;

		if (!$data && $onlydata) {
			return $data;
		}

		if ($data != null) {
			$this->buildUserAvatars($data);
			
			if ($this->_tree) {
				$data = JOSC_utils::buildTree($data);
			}

			//return $data;
			if ($onlydata) {
				return $data; /* after the foreach */
			}

			if ($data != null) {
				foreach($data as $item) {
					$html .= $this->insertPost($item, $postCSS);
					$postCSS++;
					if ($postCSS == 3) $postCSS = 1;
				}
			}
		}

		$document = JFactory::getDocument();

		$addjs = " var JOSC_postCSS=$postCSS;";

		$document->addScriptDeclaration($addjs);

		/* Daniel add-on for Allvideo Reloaded */
		if (JPluginHelper::importPlugin('content', 'avreloaded')) {
			$app = &JFactory::getApplication();
			$res = $app->triggerEvent('onAvReloadedGetVideo', array($html));
			if (is_array($res) && (count($res) == 1)) {
				$html = $res[0];
			}
		}
		} else {
			$html = $data;
		}
		/*
		 * $data is composed of ALL  or   ROOT array + CHILDREN array
		 * 	this means that position of a ROOT gives the page position.
		*/


		
		return $html;
	}

	/**
	 * Builds an array with all users Ids and calls the appropriate function
	 * @param <array> $comments
	 */
	protected function buildUserAvatars($comments) {
		$userIds = array();
		foreach($comments as $singleComment) {
			if($singleComment['userid']) {
				$userIds[] = $singleComment['userid'];
			}
		}
		switch($this->_avatar) {
			case 'CB':
				$this->buildUserAvatarCB($userIds);
				break;
			case 'JOMSOCIAL':
				$this->buildUserAvatarJomSocial($userIds);
				break;
		}	
	}

	/**
	 * Build an array with all avatars from JomSocial
	 * @param <array> $userIds
	 */
	private function buildUserAvatarJomSocial($userIds) {
		if($userIds) {
			$db =& JFactory::getDBO();
			$query = 'SELECT userid, thumb FROM #__community_users WHERE userid IN (' . implode(',',$userIds) . ')';
			$db->setQuery($query);
			$userList = $db->loadAssocList();
			$this->_profiles = array();
			foreach ($userList as $item) {
				if ($this->_avatar)
					$this->_profiles[$item['userid']]['avatar'] = $item['thumb'];
				else
					$this->_profiles[$item['userid']]['avatar'] = false;

			}
		}
	}

	/**
	 * Build an array with all avatars from Community Builder
	 * @param <array> $userIds
	 */
	private function buildUserAvatarCB($userIds) {
		if($userIds) {
			$db =& JFactory::getDBO();
			$query = 'SELECT '.$db->nameQuote('u.username')
					. ',' . $db->nameQuote('c.user_id')
					. ',' . $db->nameQuote('c.avatar')
					. ' FROM ' . $db->nameQuote('#__users') . 'AS u,'
					. ' ' . $db->nameQuote('#__comprofiler') . 'AS c'
					. ' WHERE ' . $db->nameQuote('u.id') . '=' . $db->nameQuote('c.user_id')
					. ' AND ' . $db->nameQuote('u.id') . ' IN (' . implode(',',$userIds) . ')';

			$db->setQuery($query);
			$userList = $db->loadAssocList();
			$this->_profiles = array();
			foreach ($userList as $item) {
				if ($this->_avatar)
					$this->_profiles[$item['user_id']]['avatar'] = $item['avatar'];
				else
					$this->_profiles[$item['user_id']]['avatar'] = false;

			}
			unset($userList);
		}
	}

	function insertComments() {
		$html = $this->getComments();
		if(strcmp($html,'')) {
			$output = $html;
		} else {
			//			TODO: show some text when there is no comment - needs changes in the javascript file.
			$output = '';
		}

		return "<div id='Comments'>".$output."</div>";
	}

	public function insertForm() {
		$form = new JOSC_form($this->_form); /* template block */
		$form->setAbsolute_path($this->_absolute_path);
		$form->setLive_site($this->_live_site);
		$form->setOnly_registered($this->_only_registered);
		$form->setSupport_emoticons($this->_support_emoticons);
		$form->setSupport_UBBcode($this->_support_UBBcode);
		$form->setEmoticons($this->_emoticons);
		$form->setEmoticons_path($this->_emoticons_path);
		$form->setTemplate_path($this->_template_path);
		$form->setTemplateAbsolutePath($this->_template_absolute_path);
		$form->setTemplate_name($this->_name);
		$form->setContentId($this->_content_id);
		$form->setComponent($this->_component);
		$form->setSectionid($this->_sectionid);
		$form->setCaptcha($this->_captcha);
		$form->setCaptchaType($this->_captcha_type);
		$form->setRecaptchaPublicKey($this->_recaptcha_public_key);
		$form->setRecaptchaPrivateKey($this->_recaptcha_private_key);
		$form->setRecaptchaLanguage(JFactory::getLanguage()->getTag());
		$form->setNotifyUsers($this->_notify_users);
		$form->setEnterWebsite($this->_enter_website);
		$form->setEmoticonWCount($this->_emoticon_wcount);
		$form->setFormAreaCols($this->_form_area_cols);
		$form->setUserId($this->_userid);
		$form->set_tname($this->_tname);
		$form->set_temail($this->_temail);
		$form->set_twebsite($this->_twebsite);
		$form->set_tnotify($this->_tnotify);
		$form->setGravatarSupport($this->_gravatar);
		$form->setAdditionalComments($this->_disable_additional_comments);
		$form->setComObject($this->_comObject);
		return $form->form_htmlCode();
	}

	public function comments($number) {
		$document = & JFactory::getDocument();
		$curlang = $document->language;
		if (($curlang == 'ru-ru') || ($curlang == 'uk-ua')) {
			$count_id = (int)fmod($number,100);
			if($count_id>=11 && $count_id<=19)
				$comments =  JText::_('JOOMLACOMMENT_COMMENTS_0');
			else switch((int)fmod($count_id,10)) {
					case 1: $comments = JText::_('JOOMLACOMMENT_COMMENTS_1');
						break;
					case 2: $comments = JText::_('JOOMLACOMMENT_COMMENTS_2_4');
						break;
					case 3: $comments = JText::_('JOOMLACOMMENT_COMMENTS_2_4');
						break;
					case 4: $comments = JText::_('JOOMLACOMMENT_COMMENTS_2_4');
						break;
					default: $comments =  JText::_('JOOMLACOMMENT_COMMENTS_0');
				}
		}
		else {
			if ($number < 1) {
				$comments = JText::_('JOOMLACOMMENT_COMMENTS_0');
			} elseif        ($number == 1) {
				$comments = JText::_('JOOMLACOMMENT_COMMENTS_1');
			} elseif        ($number >= 2 && $number <= 4) {
				$comments = JText::_('JOOMLACOMMENT_COMMENTS_2_4');
			} else {
				$comments = JText::_('JOOMLACOMMENT_COMMENTS_MORE');
			}
		}
		return $comments;
	}

	/**
	 * creates the write more( Number of comments) button
	 * and generated the comment preview
	 * @return html
	 */
	function insertCountButton() {
		$address = $this->_comObject->linkToContent( $this->_content_id );
		$number = $this->countComments();

		$html = $this->_readon;

		/* {READON_xxx} 	*/
		$html	= str_replace('{READON_LINK}', $address , $html);
		$html	= str_replace('{READON_WRITE_COMMENT}', JText::_('JOOMLACOMMENT_WRITECOMMENT'), $html);
		$html	= str_replace('{READON_COUNT}', $number, $html);
		$html	= str_replace('{READON_COMMENTS}', $this->comments($number), $html);
		
		/* {BLOCK-preview} */
		if ($this->_preview_visible) {
			$database =& JFactory::getDBO();
			$database->SetQuery("SELECT * FROM #__comment WHERE contentid='$this->_content_id' AND component='$this->_component' AND published='1' ORDER BY date DESC");
			$data = $database->loadAssocList();
		}
		$display	= $this->_preview_visible && ($data!=null);
		$html 		= JOSC_utils::checkBlock('BLOCK-preview', $display, $html);
		if ($display) {
			$index = 0;
			$previewlines = '';
			foreach($data as $item) {
				if ($index >= $this->_preview_lines)
					break;
				if ($item['title'] != '') {
					$title = stripslashes($item['title']);
				} else {
					$title = stripslashes($item['comment']);
				}
				if (JString::strlen($title) > $this->_preview_length)
					$title = JString::substr($title, 0, $this->_preview_length) . '...';

				$previewline 	= $this->_previewline;
				/* {PREVIEW_LINK} */
				$previewline	= str_replace('{PREVIEW_LINK}', $address, $previewline);
				/* {PREVIEW_DATE} */
				$previewline	= str_replace('{PREVIEW_DATE}', JOSC_utils::getLocalDate($item['date'],$this->_date_format) , $previewline);//date($this->_date_format,strtotime($item['date'])) , $previewline);
				/* {PREVIEW_TITLE} */
				$previewline	= str_replace('{PREVIEW_TITLE}', $title, $previewline);
				/* {PREVIEW_TITLE} */
				$previewline	= str_replace('{id}', $item['id'], $previewline);

				$index++;
				$previewlines	.= $previewline;
			}
			/* {preview-lines} */
			$html = str_replace('{preview-lines}', $previewlines, $html);

		}
		return $html;
	}

	function countComments() {
		$database =& JFactory::getDBO();
		$com = $this->_component;
		$query = "SELECT COUNT(*) FROM #__comment WHERE contentid='$this->_content_id' AND component='$com' AND published='1'";
		$database->SetQuery($query);
		$countNumber = $database->loadResult();
		if (!$countNumber) {
			$countNumber = 0;
		}
		return $countNumber;
	}

	function insertAds() {
		jimport('joomla.filesystem.file');
		$db =& JFactory::getDBO();
		$query = 'SELECT code FROM ' . $db->nameQuote('#__comment_joomvertising')
				.' WHERE type="standard_banner"';
		$db->setQuery($query);
		$data = $db->loadObject();

		if ($data && strcmp($data->code,'')) {
			$html = '<div class="josc_ads">' . $data->code . '</div>';
		} else {
			$html = '';
		}

		return $html;
	}

	function visual_htmlCode() {
		$html = "";
		$css  = $this->CSS(); /* empty if no cache */

		/*
         * if check htmlCode -> html code
         * else if check readon -> readon
         * else nothing
         *
		*/
		$checkVisual = $this->_comObject->checkVisual( $this->_content_id );
		if ($checkVisual) {
			$html .= JOSC_jscript::insertJavaScript($this->_live_site);
			/*
         	 * get template blocks
         	 * 		_body (container)
        	 * 		_menu
        	 * 		_search
        	 * 		_searchResults
        	 * 		_post
        	 * 		_form
        	 * 		_poweredby
			*/
			$this->parse(false);

			/*
             * construct HTML (by replacement...)
			*/
			$html .= "<div id='comment'>";
			if ($this->_body) {
				$html .= $this->_body;

				$html = JOSC_utils::checkBlock('library', $this->_template_library, $html); /* js scripts ... */
				$html = JOSC_utils::checkBlock('ads', false, $html, $this->insertAds());
				$html = JOSC_utils::checkBlock('menu', false, $html, $this->insertMenu());
				$html = JOSC_utils::checkBlock('post', false, $html, $this->insertComments());
				$html = str_replace('{NUMBER_OF_COMMENTS}', $this->countComments() , $html);
				$html = JOSC_utils::checkBlock('form', false, $html, $this->insertForm());
				$html = JOSC_utils::checkBlock('pagenav', false, $html, $this->insertPageNav());
				$html = JOSC_utils::checkBlock('poweredby', false, $html, $this->insertPoweredby());
			} else {
				$html .= $this->insertMenu();
				if ($this->_sort_downward) {
					$html .= $this->insertForm();
					$html .= $this->insertComments();
				} else {
					$html .= $this->insertComments();
					$html .= $this->insertForm();
				}
				$html .= $this->insertPoweredby();
			}
			$html .= "</div>";
			$html .= $this->jscriptInit();
			$html .= $css;

		} elseif ($this->_show_readon) {
			/*
    	     * get template blocks
        	 * 		_readon
         	 * 		_previewlines
			*/
			$this->parse(true);

			$html .= $this->insertCountButton();
			$html .= $css;
		} else
			return "";

		return $html;
	}
}

?>