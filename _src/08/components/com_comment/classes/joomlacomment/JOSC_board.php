<?php

defined('_JEXEC') or die('Direct Access to this location is not allowed.');
/* * *************************************************************
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
 * ************************************************************* */

class JOSC_board extends JOSC_visual {

//    var $_contentId; /* row->id */
	var $_josctask;
	var $_userid;
	var $_usertype;
	var $_tname;
	var $_ttitle;
	var $_tcomment;
	var $_twebsite;
	var $_temail;
	var $_comment_id;
	var $_content_id = 0; /* row-<id OR
	 * decode content_id from url (comes from the add new comment form)
	 * -> deleteall, editpost, getComments, gotoPost
	 */
	var $_search_keyword;
	var $_search_phrase;
	var $_charset;

	function __construct($absolutePath, $liveSite, &$comObject, &$exclude, &$row, &$params) { /* be carefull, board is used in component but also in module !! */
		parent::__construct($absolutePath, $liveSite, $comObject, $exclude, $row, $params);
	}

	function setContentId($value) {
		$this->_content_id = $value;
	}

	function setUser() {
		$database = & JFactory::getDBO();

		/* also in post ! and notification */
		$query = 'SELECT * FROM ' . $database->nameQuote('#__users')
				. ' WHERE ' . $database->nameQuote('id') . ' = ' . $database->Quote($this->_userid);
		$database->SetQuery($query, '', 1);
		$result = $database->loadAssocList();
		if ($result) {
			$user = $result[0];
			$this->_usertype = $user['usertype'];
			$this->_tname = $this->_use_name ? $user['name'] : $user['username'];
			$this->_temail = $user['email'];
		}
	}

	function voting($item, $mode) {
		$database = & JFactory::getDBO();
		JPluginHelper::importPlugin('compojoomcomment');
		$dispatcher = JDispatcher::getInstance();

		$t = time() - 3 * 86400;
		$database->SetQuery("DELETE FROM #__comment_voting WHERE time<'$t'");
		$database->Query();
		$database->SetQuery("SELECT COUNT(*) FROM #__comment_voting WHERE id='" . $item['id'] . "' AND ip='" . $_SERVER['REMOTE_ADDR'] . "'");
		$exists = $database->loadResult();
		if (!$exists) {
			$item["voting_$mode"]++;
			$database->SetQuery("
			UPDATE #__comment SET
        	voting_$mode='" . $item["voting_$mode"] . "'
        	WHERE id=$this->_comment_id");
			$database->Query() or die('Database error: voting(1)!');
			$database->SetQuery("INSERT INTO #__comment_voting(id,ip,time)
    		VALUES(
      		'" . $item['id'] . "',
      		'" . $_SERVER['REMOTE_ADDR'] . "',
      		'" . time() . "')");
			$database->Query() or die("Database error: voting(2)!");

			$dispatcher->trigger('onAfterCommentVote', array(&$this, $item, $mode));
		}
		$header = 'Content-Type: text/xml; charset=utf-8'; //.$this->_local_charset;
		header($header);
		$xml = '<?xml version="1.0" standalone="yes"?><voting><id>' . $item['id'] . '</id><yes>' . $item["voting_yes"] . '</yes><no>' . $item["voting_no"] . '</no></voting>';
		$this->_comObject->cleanComponentCache();
		exit($xml);
	}

	function isBlocked($ip) {
		if ($this->_ban != '') {
			$ipList = split(',', $this->_ban);
			foreach ($ipList as $item) {
				if (trim($item) == $ip)
					return true;
			}
		}
		return false;
	}

	function censorText($text) {
		return JOSC_utils::censorText($text, $this->_censorship_enable, $this->_censorship_words, $this->_censorship_case_sensitive);
	}

	private function _isPublished($ip, $email, $website, $comment) {
		JPluginHelper::importPlugin('compojoomcomment');
		$dispatcher = JDispatcher::getInstance();
		$result = $dispatcher->trigger('onCommentAutoPublishing', array('comment' => $comment, 'ip' => $ip, 'email' => $email, 'website' => $website));
		$custom_filters = true;
		foreach ($result as $row) {
			$custom_filters = $custom_filters && $row;
		}
		return ($this->_autopublish && $custom_filters) || JOSC_utils::isModerator($this->_moderator);
	}

	function insertNewPost($ajax = false) {
		$ip = $_SERVER['REMOTE_ADDR'];
		if ($this->isBlocked($ip)) {
			return false;
		}

		JPluginHelper::importPlugin('compojoomcomment');
		$dispatcher = JDispatcher::getInstance();

		$user = JFactory::getUser();

		if (!$user->username && $this->_only_registered) {
			die('no anonymous comments allowed');
		}
		$database = & JFactory::getDBO();
		$debug = '';

		$com = $this->_component;
		$userid = $this->_userid;
		$name = $this->censorText(strip_tags($this->_tname));
		$email = $this->censorText(strip_tags($this->_temail));
		$website = $this->censorText(strip_tags($this->_twebsite));
		if ($website && strncmp("http://", $website, 7) != 0) {
			$website = "http://" . $website;
		}
		$website = htmlentities($website); //ampReplace($website);
		$notify = strip_tags($this->_tnotify) ? true : false;
		$title = $this->censorText(strip_tags($this->_ttitle));
		$comment = $this->censorText(htmlspecialchars($this->_tcomment));
		if (!$comment) {
			$comment = Jtext::_('JOOMLACOMMENT_EMPTYCOMMENT');
		}
		$published = $this->_isPublished($ip, $email, $website, $comment);

		$parent_id = $this->_parent_id;

		if ($this->_akismet_use) {
			require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'akismet' . DS . 'Akismet.class.php');
			// START Marcofolio.net Akismet
			$WordPressAPIKey = $this->_akismet_key; // Insert WordPress API Key
			$MyBlogURL = JString::substr_replace(JURI::base(), '', -1);
			$akismet = new Akismet($MyBlogURL, $WordPressAPIKey);
			$akismet->setCommentAuthor($name);
			$akismet->setCommentAuthorEmail($email);
			$akismet->setCommentAuthorURL($website);
			$akismet->setCommentContent($comment);
			$akismet->setPermalink(JURI::current());

			if ($akismet->isCommentSpam()) {
				// store the comment but mark it as spam (in case of a mis-diagnosis)
				$published = 0;
			}
		}
		$createdate = JFactory::getDate();
		$createdate = $createdate->toMySQL();

		$queryInsert = 'INSERT INTO ' . $database->nameQuote('#__comment')
				. ' ('
				. $database->nameQuote('contentid') . ','
				. $database->nameQuote('component') . ','
				. $database->nameQuote('ip') . ','
				. $database->nameQuote('userid') . ','
				. $database->nameQuote('usertype') . ','
				. $database->nameQuote('date') . ','
				. $database->nameQuote('name') . ','
				. $database->nameQuote('email') . ','
				. $database->nameQuote('website') . ','
				. $database->nameQuote('notify') . ','
				. $database->nameQuote('title') . ','
				. $database->nameQuote('comment') . ','
				. $database->nameQuote('published') . ','
				. $database->nameQuote('voting_yes') . ','
				. $database->nameQuote('voting_no') . ','
				. $database->nameQuote('parentid') . ')'
				. 'VALUES ('
				. $database->Quote($this->_content_id) . ','
				. $database->Quote($com) . ','
				. $database->Quote($ip) . ','
				. $database->Quote($userid) . ','
				. '"",'
				. $database->Quote($createdate) . ','
				. $database->Quote($name) . ','
				. $database->Quote($email) . ','
				. $database->Quote($website) . ','
				. $database->Quote($notify) . ','
				. $database->Quote($title) . ','
				. $database->Quote($comment) . ','
				. $database->Quote($published) . ','
				. '0,'
				. '0,'
				. $database->Quote($parent_id)
				. ')';
		$database->SetQuery($queryInsert);
		$result = $database->Query() or die(JText::_('JOOMLACOMMENT_SAVINGFAILED')); //.$database->getQuery());

		$this->_comment_id = $database->insertid();

		$dispatcher->trigger('onAfterCommentSave', array(&$this));

		$notification = new JOSC_notification($this, $this->_comment_id, $this->_content_id);

		$notification->setNotifyAllPostOfUser($userid, $email, $notify);

		$notification->lists['name'] = $name;
		$notification->lists['title'] = $title;
		$notification->lists['notify'] = $notify;
		$notification->lists['comment'] = $comment;

		if ($published) {
			$notification->lists['subject'] = JText::_('JOOMLACOMMENT_NOTIFY_NEW_SUBJECT');
			$notification->lists['message'] = JText::_('JOOMLACOMMENT_NOTIFY_NEW_MESSAGE');
			$templist = $notification->getMailList($this->_content_id, $email);
			$notification->notifyMailList($templist);
		} else {
			$notification->lists['subject'] = JText::_('JOOMLACOMMENT_NOTIFY_TOBEAPPROVED_SUBJECT');
			$notification->lists['message'] = JText::_('JOOMLACOMMENT_NOTIFY_TOBEAPPROVED_MESSAGE');
			if (!JOSC_utils::isModerator($this->_moderator)) {
				$templist = $notification->getMailList_moderator();
				$notification->notifyMailList($templist);
			}
		}

		if ($ajax) {
			$data = $this->getComments(true);

			$after = -1;
			/* look for the right place */
			foreach ($data as $item) {
				if ($item['id'] == $this->_comment_id) {
					$item['after'] = $after;
					$item['view'] = $published;
					$item['debug'] = $debug;
					$item['noerror'] = 1;
					return $item;
				}

				$after = $item['id'];
			}
			$data[0]['view'] = $published;
			$data[0]['debug'] = $debug;
			$data[0]['noerror'] = 1;
			return $data[0];
		} else {
			return $published;
		}
	}

	function editPost() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if ($this->isBlocked($ip)) {
			return false;
		}

		JPluginHelper::importPlugin('compojoomcomment');
		$dispatcher = JDispatcher::getInstance();

		$database = & JFactory::getDBO();

		$debug = '';

		$database->SetQuery("SELECT * FROM #__comment WHERE id='$this->_comment_id'");
		$item = $database->loadAssocList();

		if ($this->checkEditPost($item[0])) {
			if (JOSC_utils::isModerator($this->_moderator)) {
				$published = '';
			} else {
				$published = ' AND published = 1 ';
			}

			$title = $this->censorText($database->getEscaped(strip_tags($this->_ttitle)));
			$comment = $this->censorText($database->getEscaped(strip_tags($this->_tcomment)));
			$website = $this->censorText($database->getEscaped(strip_tags($this->_twebsite)));
			if ($website && strncmp("http://", $website, 7) != 0)
				$website = "http://" . $website;
			$website = htmlentities($website); //ampReplace($website);
			$notify = $database->getEscaped(strip_tags($this->_tnotify)) ? '1' : '0';
			$createdate = JFactory::getDate();
			$createdate = $createdate->toMySQL();
			$query = "
           	UPDATE #__comment SET
           		date='$createdate'
           		,title='$title'
           		,comment='$comment'
          		 	,website='$website'
           		,notify='$notify'
           	WHERE id=$this->_comment_id";
			$database->SetQuery($query);
			$database->Query() or die(JText::_('JOOMLACOMMENT_EDITINGFAILED') . "\n $query");
			$database->SetQuery("SELECT * FROM #__comment WHERE id='$this->_comment_id' $published LIMIT 1");
			$data = $database->loadAssocList() or die('Database error: editPost!');

			$dispatcher->trigger('onAfterCommentEdit', array(&$this));

			$notification = new JOSC_notification($this, $this->_comment_id, $this->_content_id);
			$notification->setNotifyAllPostOfUser($item[0]['userid'], $item[0]['email'], $notify);

			/* send email to Moderator */
			if (!JOSC_utils::isModerator($this->_moderator)) {
				$notification->lists['name'] = $item[0]['name'];
				$notification->lists['title'] = $title;
				$notification->lists['notify'] = $item[0]['notify'];
				$notification->lists['comment'] = $comment;
				$notification->lists['subject'] = JText::_('JOOMLACOMMENT_NOTIFY_EDIT_SUBJECT');
				$notification->lists['message'] = Jtext::_('JOOMLACOMMENT_NOTIFY_EDIT_MESSAGE');
				$templist = $notification->getMailList_moderator();
				$notification->notifyMailList($templist);
			}

			$this->buildUserAvatars($data);
			$data[0]['view'] = 1;
			$data[0]['debug'] = $debug;
			$data[0]['noerror'] = 1;
			return $data[0];
		}
	}

	/*
	 *  same as isCommentModerator
	 */

	function checkEditPost($item) {
		if (!$item)
			return false;
		/* edit if  registered or comment is own or is moderator */
		if (JOSC_utils::isModerator($this->_moderator, $item['userid']) ||
				JOSC_utils::isCommentModerator($this->_moderator, $item['userid'])) {
			return true;
		} else {
			return false;
		}
	}

	/* Function modidy($event = false)
	 * example of call : $this->modify(editPost)
	 * 	  	event is a method which will be called below as  $this->$event(true)
	 * 		where true means ajax call.
	 */

	function modify($event = false) {
		$mainframe = & JFactory::getApplication();
		$user = & JFactory::getUser();
		if (!$event) {
			if (!$user->username && $this->_only_registered) {
				/* only registered */
				JOSC_utils::showMessage(JText::_('JOOMLACOMMENT_ONLYREGISTERED'));
			} else {
				if (!($this->_captcha && !JOSC_security::captchaResult(true, $this->_captcha_type, $this->_recaptcha_private_key))) {
					/* captcha ok */
					$published = $this->insertNewPost();
					unset($this->_tcomment);
					$this->_comObject->cleanComponentCache();
					if ($published) {
						$mainframe->redirect($this->_comObject->linkToContent($this->_content_id, $this->_comment_id));
					} else {
						$mainframe->redirect($this->_comObject->linkToContent($this->_content_id), JText::_('JOOMLACOMMENT_BEFORE_APPROVAL'));
					}
				}
			}
			$mainframe->redirect($this->_comObject->linkToContent($this->_content_id, $this->_comment_id));
		}
		$header = 'Content-Type: text/xml; charset=utf-8'; //.$this->_local_charset;
		header($header);
		if (!($this->_captcha && !JOSC_security::captchaResult(true, $this->_captcha_type, $this->_recaptcha_private_key))) {
			$item = $this->$event(true);
			if (!$item)
				exit();
			$this->parse();
			$xml = '<?xml version="1.0" standalone="yes"?>';
			$xml .= '<post>';
			if ($this->_tree && isset($item['after'])) {
				$xml .= '<after>' . $item['after'] . '</after>';
			}
			$xml .= '<published>' . $item['view'] . '</published>';
			$xml .= '<noerror>' . $item['noerror'] . '</noerror>';
			$xml .= '<debug>' . $item['debug'] . '</debug>';
			if ($item['view']) {
				$xml .= '<id>' . $item['id'] . '</id>';
				$html = JOSC_utils::cdata(JOSC_utils::filter($this->insertPost($item, '')));
				$xml .= "<body>$html</body>";
			}
			if ($this->_captcha) {
				/* $captcha = JOSC_utils::cdata(JOSC_security::insertCaptcha('security_refid')); */
				if ($this->_captcha_type == "recaptch") {
					$captcha = "true";
				} else {
					$captcha = JOSC_utils::cdata(JOSC_security::insertCaptcha('security_refid', $this->_captcha_type, $this->_recaptcha_public_key));
				}
				$xml .= "<captcha>$captcha</captcha>";
			}
			$xml .= '</post>';
			$this->_comObject->cleanComponentCache();
			exit($xml);
		} else if ($this->_captcha) {
			$xml = $this->xml_refreshCaptcha(true);
			exit($xml);
		} else {
			exit;
		}
	}

	function xml_refreshCaptcha($withalert=true) {
		/* $captcha = JOSC_utils::cdata(JOSC_security::insertCaptcha('security_refid')); */
		if ($this->_captcha_type == "recaptch") {
			$captcha = "true";
		} else {
			$captcha = JOSC_utils::cdata(JOSC_security::insertCaptcha('security_refid', $this->_captcha_type, $this->_recaptcha_public_key));
		}
		$xml = '<?xml version="1.0" standalone="yes"?>';
		$xml .= '<post>';
		$xml .= '<id>' . ($withalert ? 'captchaalert' : 'captcha' ) . '</id>';
		$xml .= "<captcha>$captcha</captcha>";
		$xml .= '<noerror>1</noerror>';
		$xml .= '</post>';
		return $xml;
	}

	function deletePost($id = -1) {
		$database = & JFactory::getDBO();
		JPluginHelper::importPlugin('compojoomcomment');
		$dispatcher = JDispatcher::getInstance();

		$com = $this->_component;
		$contentid_where = "WHERE contentid='$this->_content_id' AND component='$com' ";
		$where = ($id == -1) ? $contentid_where : "WHERE id='$id'";
		$database->SetQuery("DELETE FROM #__comment $where");
		$database->Query() or die(JText::_('JOOMLACOMMENT_DELETINGFAILED'));

		$dispatcher->trigger('onAfterCommentDelete', array(&$this));

		$this->_comObject->cleanComponentCache();

		/* send mail to the moderators and to the notified writers */
		if ($id == -1) {
			$database->SetQuery("SELECT id FROM #__comment $where");
			$cids = $database->loadResultArray();
		} else {
			$cids = $id;   /* TODO : this has no sens :D */
		}
		$notification = new JOSC_notification($this);
		$notification->notifyComments($cids, 'delete');
	}

	public function publishUnpublishComment($id = -1, $publish = 0) {
		$database = & JFactory::getDBO();

		$com = $this->_component;
		$contentid_where = "WHERE contentid='$this->_content_id' AND component='$com' ";
		$where = ($id == -1) ? $contentid_where : "WHERE id='$id'";
		$database->SetQuery("UPDATE #__comment SET published=$publish $where");
		$database->Query() or die(JText::_('JOOMLACOMMENT_DELETINGFAILED'));

		$this->_comObject->cleanComponentCache();

		/* send mail to the moderators and to the notified writers */
		if ($id == -1) {
			$database->SetQuery("SELECT id FROM #__comment $where");
			$cids = $database->loadResultArray();
		} else {
			$cids = $id;   /* TODO : this has no sens :D */
		}
		$notification = new JOSC_notification($this);
		$notification->notifyComments($cids, 'delete');
	}

	function search() {
		$this->parse();
		$search = new JOSC_search($this->_searchResults, $this->_comObject);
		$search->setKeyword($this->_search_keyword);
		$search->setPhrase($this->_search_phrase);
		$search->setDate_format($this->_date_format);
		$search->setAjax($this->_ajax);
		$search->setLocalCharset($this->_local_charset);
		$search->setCensorShip($this->_censorship_enable,
				$this->_censorship_case_sensitive,
				$this->_censorship_words,
				$this->_censorship_usertypes
		);
		$search->setMaxLength_text($this->_maxlength_text);
		$search->setMaxLength_word($this->_maxlength_word);
		$search->setMaxLength_line($this->_maxlength_line);
		$search->setContentId($this->_content_id);
		$search->setComponent($this->_component);
		$search->setSectionid($this->_sectionid);
		return $search->search_htmlCode();
	}

	function filterAll($item) { /* used also by search class */
		$item['name'] = JOSC_utils::filter($item['name']);
		$item['title'] = JOSC_utils::filter($item['title']);
		$item['comment'] = JOSC_utils::filter($item['comment']);
		return $item;
	}

	function decodeURI() {
		$user = & JFactory::getUser();

		$this->_request_uri = JArrayHelper::getValue($_SERVER, 'REQUEST_URI', ''); // _JOSC_MOS_ALLOWHTML

		$this->_josctask = JRequest::getCmd('josctask', '', 'POST');

		if ($user->username) {
			$this->_userid = $user->id;
			$this->_usertype = $user->usertype;
			$this->_tname = $user->username;
			$this->setUser();
		} else {
			$this->_userid = 0;
			$this->_usertype = 'Unregistered';

			$cookieNameChanged = false;
			$cookieEmailChanged = false;
			$decodedName = JRequest::getString('tname','','POST');
			$decodedEmail = JRequest::getString('temail','','POST');
			$cookieName = JRequest::getString('com_comment_name', '', 'COOKIE');
			$cookieEmail = JRequest::getString('com_comment_email', '', 'COOKIE');
			if ($decodedName) {
				if ($decodedName != $cookieName) {
					$cookieNameChanged = true;
					setcookie('com_comment_name',
							$decodedName,
							time() + 60 * 60 * 24 * 30
					);
				}
			}
			if ($decodedEmail) {
				if ($decodedEmail != $cookieEmail) {
					$cookieEmailChanged = true;
					setcookie('com_comment_email',
							$decodedEmail,
							time() + 60 * 60 * 24 * 30
					);
				}
			}
			if (!$cookieNameChanged) {
				$name = $cookieName;
			} else {
				$name = $decodedName;
			}

			$this->_tname = $name;

			if (!$cookieEmailChanged) {
				$email = $cookieEmail;
			} else {
				$email = $decodedEmail;
			}

			$this->_temail = $email;
		}

		$this->_tnotify = JRequest::getInt('tnotify','','POST');
		$this->_twebsite = JRequest::getString('twebsite','','POST');
		$this->_ttitle = JRequest::getString('ttitle','','POST');
		$this->_tcomment = JRequest::getVar('tcomment','','POST');

		$this->_comment_id = JRequest::getInt('comment_id','','POST');

		if (!$this->_content_id) {
			$this->_content_id = JRequest::getInt('content_id', '', 'POST');
		}
		if (!$this->_component) {
			$this->_component = JRequest::getCmd('component','','POST');
		}
		$this->_search_keyword = JRequest::getVar('search_keyword','','POST');
		$this->_search_phrase = JRequest::getVar('search_phrase','','POST');
		$this->_parent_id = JRequest::getInt('parent_id','','POST');
		if ($this->_parent_id == '') {
			$this->_parent_id = '-1';
		}
		$this->_limitstart = JRequest::getInt('josclimitstart','','POST');
	}

	/*
	 * decode URI
	 * and execute josctask if 'josctask'(ajax mode)
	 * 			OR insertnewPost if 'tcomment'(not ajax mode)
	 */

	public function execute() {

		$database = & JFactory::getDBO();

		/* don't forget if modify josctask
		 * that it is first checked in comment.php !
		 */

		$this->decodeURI();
		if ($this->_josctask == 'noajax') {
			if ($this->_tcomment)
				$this->modify(false); /* modify in not ajax mode */
		} else {
			/*
			 * If we have a commentId then we can start a query. If we don't(like on frontpage) we skip this.
			 */
			$itemsave = '';
			if ($this->_comment_id) {
				$item = $this->getItem();
				$itemsave = $item ? $item[0] : "";
			}

			if (JOSC_utils::isModerator($this->_moderator)) {
				if ($this->_josctask == 'ajax_unpublish') {
					$this->publishUnpublishComment($this->_comment_id, 0);

					$this->getXMLResponse();
				}
				if ($this->_josctask == 'ajax_publish') {
					$this->publishUnpublishComment($this->_comment_id, 1);

					$this->getXMLResponse();
				}
			}
			/* 			if ($this->_josctask == 'unsubscribe') {
			  $notification = new JOSC_notification($this, $this->_comment_id, $this->_content_id);
			  $notification->setNotifyAllPostOfUser($userid, $email, $notify);
			  } */
			if ($this->checkEditPost($itemsave)) {
				if ($this->_josctask == 'ajax_delete') {
					$this->deletePost($this->_comment_id);
					exit;
				}
				if ($this->_josctask == 'ajax_edit') {
					$this->modify('editPost');
				}
			}
			if (JOSC_utils::isModerator($this->_moderator)) {
				if ($this->_josctask == 'ajax_delete_all') {
					$this->deletePost();
					exit;
				}
			}
			if ($this->_josctask == 'ajax_insert') {
				/*
				 * if parent_id AND only moderator reply -> exit if not moderator
				 * because javascript reply deactivated, should not be possible except volontary spam...or delay change of setting
				 * else ok.
				 */
				if ($this->_parent_id < 1 || !$this->_mlink_post || JOSC_utils::isModerator($this->_moderator)) {
					$this->modify('insertNewPost');
				} else {
					exit();
				}
			}
			if ($this->_josctask == 'ajax_modify' || $this->_josctask == 'ajax_quote') {
				/*
				 * return <post> content of current comment to the FORM
				 */
				$item = $this->filterAll($item[0]);
				$header = 'Content-Type: text/xml; charset=utf-8'; //.$this->_local_charset; not ok for IE ! only utf-8 is possible
				header($header);
				$item['email'] = ($item['userid'] ? JOSC_utils::filter(Jtext::_('JOOMLACOMMENT_AUTOMATICEMAIL')) : $item['email']);
				$xml = '<?xml version="1.0" standalone="yes"?><post>'
						. '<name>' . JOSC_utils::cdata($item['name']) . '</name>'
						. '<title>' . JOSC_utils::cdata($item['title']) . '</title>'
						. '<comment>' . JOSC_utils::cdata($item['comment']) . '</comment>'
				;
				if ($this->_josctask == 'ajax_modify' && $this->checkEditPost($itemsave)) {
					$xml .= '<email>' . JOSC_utils::cdata($item['email']) . '</email>'
							//					 		. '<userid>'   . JOSC_utils::cdata($item['userid'])  . '</userid>'
							. '<notify>' . JOSC_utils::cdata($item['notify']) . '</notify>'
							. '<website>' . JOSC_utils::cdata($item['website']) . '</website>'
					;
				}

				$xml .= '</post>'
				;
				exit($xml);
			}
			if ($this->_josctask == 'ajax_reload_captcha') {
				$header = 'Content-Type: text/xml; charset=utf-8'; //.$this->_local_charset;
				header($header);
				$xml = $this->xml_refreshCaptcha(false);
				exit($xml);
			}
			if ($this->_josctask == 'ajax_voting_yes') {
				$this->voting($itemsave, 'yes');
			}
			if ($this->_josctask == 'ajax_voting_no') {
				$this->voting($itemsave, 'no');
			}
			if ($this->_josctask == 'ajax_insert_search') {
				$this->parse();
				$header = 'Content-Type: text/xml; charset=utf-8'; //.$this->_local_charset;
				header($header);
				exit($this->insertSearch());
			}
			if ($this->_josctask == 'ajax_search') {
				$header = 'Content-Type: text/xml; charset=utf-8'; //charset='.$this->_local_charset;
				header($header);
				exit($this->search());
			}
			if ($this->_josctask == 'ajax_getcomments') {
				$this->getXMLResponse();
			}
		}
	}

	public function getXMLResponse() {
		$header = 'Content-Type: text/xml; charset=utf-8'; //charset='.$this->_local_charset;
		header($header);
		$this->parse();
		$html = $this->getComments();
		if (!$html)
			exit();
		$pagenav = $this->getPageNav();
		$xml = '<?xml version="1.0" standalone="yes"?>';
		$xml .= '<getpost>';
		$xml .= '<limitstart>' . $this->_limitstart . '</limitstart>';
		$xml .= '<body>' . JOSC_utils::cdata(JOSC_utils::filter(($html))) . '</body>';
		$xml .= '<pagenav>' . JOSC_utils::cdata(JOSC_utils::filter(($pagenav))) . '</pagenav>';
		//	            $xml .= '<debug>'.$this->_display_num.'</debug>';
		$xml .= '</getpost>';
		$this->_comObject->cleanComponentCache();
		exit($xml);
	}

	public function getItem() {
		$database = & JFactory::getDBO();
		$query = 'SELECT * FROM ' . $database->nameQuote('#__comment')
				. ' WHERE ' . $database->nameQuote('id') . ' = ' . $database->Quote($this->_comment_id);
		$database->SetQuery($query, '', 1);
		$item = $database->loadAssocList();
		return $item;
	}

}
?>