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

class JOSC_notification {
    public $_notify_admin;
    public $_notify_email;
    public $_notify_moderator;
    public $_moderator;
    public $_notify_users;
    public $_component;
    public $_comObject;
    public $_comment_id;
    public $_content_id;
    public $lists = array();

    public function __construct(&$object, $_comment_id=-1, $_content_id=-1)
    {

        $this->_comObject 			= $object->_comObject;
        $this->_component 			= $object->_comObject->_component;
        $this->_notify_email     	= $object->_notify_email;
        $this->_notify_moderator	= $object->_notify_moderator;
        $this->_moderator			= is_array($object->_moderator) ? $object->_moderator : explode(',', $object->_moderator);;
        $this->_notify_users		= $object->_notify_users;
        $this->setIDs($_comment_id, $_content_id);
    }

    function setIDs($_comment_id, $_content_id)
    {
        $this->_comment_id = $_comment_id;
        $this->_content_id = $_content_id;
    }

    function resetLists()
    {
        $this->lists = array();
    }

	/*
	 * mail to notify :
	 *   the writer (AT LEAST)
	 *   the users of those contentid (to inform of a new comment)
	 * 	 moderators
	 * 	TYPE = 'publish' or 'delete' or ?
	 */
    function notifyComments($cids, $type)
    {
        $my = JFactory::getUser();

        $database =& JFactory::getDBO();


        if (is_array($cids)) {
            $cids = implode(',',$cids);
        }

        $sentemail = "";
        $database->setQuery("SELECT * FROM #__comment WHERE id IN ($cids)");
        $rows = $database->loadObjectList();
		if ($rows) {
            $query = "SELECT email FROM #__users WHERE id='".$my->id."' LIMIT 1";
            $database->SetQuery($query);
            $myemail = $database->loadResult();
            $_notify_users =  $this->_notify_users;

            foreach($rows as $row) {
                $this->_notify_users = $_notify_users;
                $this->setIDs($row->id, $row->contentid);
                $this->resetLists();
                $this->lists['name'] 	= $row->name;
                $this->lists['title'] 	= $row->title;
                $this->lists['notify'] 	= $row->notify;
                $this->lists['comment']	= $row->comment;

                $email_writer = $row->email;
                                /*
                                 * notify writer of approval
                                 */
                if ($row->userid > 0) {
                    $query = "SELECT email FROM #__users WHERE id='".$row->userid."' LIMIT 1";
                    $database->SetQuery($query);
                    $result = $database->loadAssocList();
                    if ($result) {
                        $user = $result[0];
                        $email_writer    = $user['email'];
                    }
                }

                if ($email_writer && $email_writer != $myemail) {
                    switch ($type) {
       			case 'publish':
                            $this->lists['subject'] = JText::_('JOOMLACOMMENT_NOTIFY_PUBLISH_SUBJECT');
                            $this->lists['message'] = JText::_('JOOMLACOMMENT_NOTIFY_PUBLISH_MESSAGE');
                            break;
                        case 'unpublish':
                            $this->lists['subject'] = JText::_('JOOMLACOMMENT_NOTIFY_UNPUBLISH_SUBJECT');
                            $this->lists['message'] = JText::_('JOOMLACOMMENT_NOTIFY_UNPUBLISH_MESSAGE');
                            break;
                        case 'delete' :
                            $this->lists['subject'] = JText::_('JOOMLACOMMENT_NOTIFY_DELETE_SUBJECT');
                            $this->lists['message'] = JText::_('JOOMLACOMMENT_NOTIFY_DELETE_MESSAGE');
                            break;
                    }

                    $sentemail .=  ($sentemail ? ';' : '').$this->notifyMailList($temp=array($email_writer));
                    $exclude = $myemail ? ($email_writer.','.$myemail): $email_writer;
                } else {
                    $exclude = $myemail ? $myemail:"";
                }
			        /*
			         * notify users, moderators, admin
			         */
                switch ($type) {
                    case 'publish':
                        $this->lists['subject']	= JText::_('JOOMLACOMMENT_NOTIFY_PUBLISH_SUBJECT');
                        $this->lists['message']	= JText::_('JOOMLACOMMENT_NOTIFY_PUBLISH_MESSAGE');
                        break;
                    case 'unpublish':
                        $this->_notify_users = false;
                        $this->lists['subject']	= JText::_('JOOMLACOMMENT_NOTIFY_UNPUBLISH_SUBJECT');
                        $this->lists['message']	= JText::_('JOOMLACOMMENT_NOTIFY_UNPUBLISH_MESSAGE');
                        break;
                    case 'delete' :
                        $this->_notify_users = false;
                        $this->lists['subject']	= JText::_('JOOMLACOMMENT_NOTIFY_DELETE_SUBJECT');
                        $this->lists['message']	= JText::_('JOOMLACOMMENT_NOTIFY_DELETE_MESSAGE');
                        break;
                 }
                $templist = $this->getMailList($row->contentid, $exclude);
                $sentemail .=  ($sentemail ? ';' : '').$this->notifyMailList($templist);
            }
        }
        return $sentemail;
    }

    /*
     * get all users (unregistered and registered)
     * notified for the given content item
     * AND all moderators if notify_moderator active
     * AND admin if notify admin active (for backward compatibility)
     */
	function getMailList($contentid='', $exclude='')
	{   /* exclude must be an array of values
		 * OR not quoted list separated by ,
		 *
		 * contentid should be an array of values
		 * OR not quoted list separated by ,
		 */

		$database =& JFactory::getDBO();

	    if (is_array($contentid)) {
	        $contentid = implode(',', $contentid);
	    }
	    if (is_array($exclude)) {
	        $exclude = implode(',', $exclude);
	    }

	    if ($this->_notify_users && $contentid) {
	        /* Unregistered users  */
			$query 	= "SELECT DISTINCT email "
					. "\n FROM `#__comment` "
					. "\n   WHERE contentid IN ($contentid) AND component='$this->_component'"
					. "\n     AND ( userid = NULL OR userid = 0 )"
					. "\n     AND email  <> ''"
					. "\n     AND notify = '1'"
					;
			if ($exclude) {
				$quoted = str_replace( ',', "','", $exclude); /* add quotes */
				$query .= "\n     AND email NOT IN ('$quoted')";
			}
			$database->setQuery( $query );
			$unregistered_maillist = $database->loadResultArray();  //tableau

			if ($unregistered_maillist) {
			    $exclude = ($exclude ? $exclude.',' : '') . implode(',', $unregistered_maillist);
			}

  	      	/* Registered users*/
  	      	$registered_maillist = array();
			$query 	= "SELECT DISTINCT u.email "
					. "\n FROM `#__comment` AS c "
					. "\n INNER JOIN `#__users` AS u ON u.id = c.userid "
					. "\n   WHERE c.contentid IN ($contentid) AND component='$this->_component'"
					. "\n     AND u.email  <> ''"
					. "\n     AND c.notify = '1'"
					;
			if ($exclude) {
				$quoted = str_replace( ',', "','", $exclude); /* add quotes */
				$query .= "\n     AND u.email NOT IN ('$quoted')";
			}
			$database->setQuery( $query );
			$registered_maillist = $database->loadResultArray();  //tableau
//			$debugemail  = implode(';' , $maillist); // liste s�par� par des ;

			if ($registered_maillist) {
		    	$exclude = ($exclude ? $exclude.',' : '') . implode(',', $registered_maillist);
			}
	    }

		$moderator_maillist = $this->getMailList_moderator($exclude);

		$maillist = array();
		if (isset($unregistered_maillist) && is_array($unregistered_maillist))
			$maillist = array_merge( $maillist, $unregistered_maillist);
		if (isset($registered_maillist) && is_array($registered_maillist))
			$maillist = array_merge( $maillist, $registered_maillist);
		if (isset($moderator_maillist) && is_array($moderator_maillist))
			$maillist = array_merge( $maillist, $moderator_maillist);

		return ($maillist);
	}

    /*
     * get moderators maillist
     */
	function getMailList_moderator($exclude='')
	{
		/* exclude must be an array of values
		 * OR not quoted list separated by ,
		 */

		$database =& JFactory::getDBO();

	    if (is_array($exclude)) {
	        $exclude = implode(',', $exclude);
	    }

        /* Moderators(if requested) */

        $moderator_maillist = array();

        if ($this->_notify_moderator && $this->_moderator) {
            $usertype = '';
            foreach($this->_moderator as $moderator) {
                $usertype .= ($usertype ? ',':'') . "'" . JOSC_utils::getJoomlaUserType($moderator) . "'";
            }
			$query 	= "SELECT DISTINCT email "
					. "\n FROM `#__users` "
					. "\n   WHERE email <> '' "
					. "\n     AND usertype IN ($usertype)"
					;
			if ($exclude) {
				$quoted = str_replace( ',', "','", $exclude); /* add quotes */
				$query .= "\n     AND email NOT IN ('$quoted')";
			}
			$database->setQuery( $query );
			$moderator_maillist = $database->loadResultArray();  //tableau
			//echo  implode(';' , $moderator_maillist); // liste s�par� par des ;
        } elseif ($this->_notify_admin && $this->_notify_email <> '') {
            $moderator_maillist[] = $this->_notify_email;
        }
        return $moderator_maillist;
	}

	/*
	 * mail to the given maillist
	 * 	object->lists must be set (at least commentid and contentid)
	 */
    function notifyMailList( &$maillist )
    {
		
        $mailer =& JFactory::getMailer();

		$sentmail = '';

		if (!is_array($maillist) || count($maillist)<=0) {
			return $sentmail;
		}

        $comment_id     = $this->_comment_id;	/* obligatory */
        $contentid      = $this->_content_id;	/* obligatory */
        $component		= $this->_component;	/* obligatory */
        $comObject		= $this->_comObject;	/* obligatory */
        $name           = stripslashes($this->lists['name']);
        $title          = stripslashes($this->lists['title']);
        $notify         = stripslashes($this->lists['notify']);
        $comment  		= stripslashes($this->lists['comment']);

        $subject		= stripslashes($this->lists['subject']);
        $message		= stripslashes($this->lists['message']);

        $articlelink = $comObject->linkToContent($contentid, $comment_id, true);

		$subject = str_replace('{title}'	, $title,$subject);
		$subject = str_replace('{name}'		, $name,$subject);
		$subject = str_replace('{notify}'	, ($notify ? "yes" : "no"),$subject);

		$message = str_replace('{livesite}'	, JURI::base(),$message);
		$message = str_replace('{title}'	, $title,$message);
		$message = str_replace('{name}'		, $name,$message);
		$message = str_replace('{notify}'	, ($notify ? "yes" : "no"),$message);
		$message = str_replace('{comment}'	, $comment,$message);
		$message = str_replace('{linkURL}'	, $articlelink,$message);
/*
        $subject = 'NewComment :'.$title."[from:".$name."][notify:".($notify ? "yes" : "no")."]";

        $message = '<p>A user has posted a new comment to a content item you have subscribed <br />in '.JURI::base().':</p>';
        $message .= '<p><b>Name: </b>'.$name.'<br />';
        $message .= '<b>Title: </b>'.$title.'<br />';
        $message .= '<b>Text: </b>'.$comment.'<br />';
        $message .= '<b>Content item: </b><a href="'.$articlelink.'">'.$articlelink.'</a></p>';

        $message .= "<p>Please do not respond to this message as it is automatically generated and is for information purposes only.</p>";
*/
        foreach($maillist as $mail) {

            if (JUTility::sendMail($mailer->From, $mailer->FromName, $mail, $subject, $message, true, $mailer->cc, $mailer->bcc, $mailer->attachment, $mailer->ReplyTo, $mailer->FromName )) {
				$sentmail .= ($sentmail ? ';' : '').$mail;
            }
        }
		return $sentmail;
    }

    function setNotifyAllPostOfUser($userid, $email, $notify)
    {
		$database =& JFactory::getDBO();

    	if ((!$userid && !$email) || !$this->_content_id) return false;

    	$where  = $userid ? " userid=$userid " : ( $email ? " email='$email' " : "" );

    	$query 	= "UPDATE #__comment SET notify='$notify' "
    			. "\n  WHERE contentid=$this->_content_id "
    			. "\n    AND $where "
    			;
        $database->SetQuery($query);
        return ($database->Query());

    }
}
?>
