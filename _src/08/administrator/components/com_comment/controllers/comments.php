<?php
defined('_JEXEC') or die('Restricted access');
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

jimport( 'joomla.application.component.controller' );

/**
 * Plugins Component Controller
 *
 * @package		Joomla
 * @subpackage	Plugins
 * @since 1.5
 */
class CommentControllerComments extends CommentController {

	public function edit() {
		JRequest::setVar( 'view', 'comment' );
		JRequest::setVar( 'hidemainmenu', '1' );

		parent::display();
	}

	public function remove() {
		JRequest::checkToken() or jexit('Invalid Token');
		$app =& JFactory::getApplication();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');

		if(!is_array($cid) || count($cid) < 1) {
			echo "<script>alert('" . JText::_('Select an item to remove') . "'); window.history.go(-1);</script>";
			exit;
		}

		$model = $this->getModel('comments');
		if($model->delete($cid)) {
			if(count($cid) == 1) {
				$msg = JText::sprintf('%d Comment Deleted', count($cid));
			}
			else {
				$msg = JText::sprintf('%d Comments Deleted', count($cid));
			}
		}
		else {
			$msg = JText::_('Error Deleting Comment');
		}

		$app->redirect('index.php?option=com_comment&view=comments', $msg);
	}

	public function publish() {
		JRequest::checkToken() or jexit('Invalid Token');
		$app =& JFactory::getApplication();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');

		if(!is_array($cid)|| count($cid) < 1) {
			echo "<script>alert('" . JText::_('Select a comment to publish') . "'); window.history.go(-1);</script>";
			exit;
		}

		$model = $this->getModel('comments');
		if($model->publish($cid, 1)) {
			if(count($cid) == 1) {
				$msg = JText::sprintf('%d Comment published', count($cid));
			}
			else {
				$msg = JText::sprintf('%d Comments published', count($cid));
			}
		}
		else {
			$msg = $model->getError();
		}

		$app->redirect('index.php?option=com_comment&view=comments', $msg);
	}

	public function unpublish() {
		JRequest::checkToken() or jexit('Invalid Token');

		$app =& JFactory::getApplication();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');

		if(!is_array($cid)|| count($cid) < 1) {
			echo "<script>alert('" . JText::_('Select a comment to publish') . "'); window.history.go(-1);</script>";
			exit;
		}

		$model = $this->getModel('comments');
		if($model->publish($cid, 0)) {
			if(count($cid) == 1) {
				$msg = JText::sprintf('%d Comment unpublished', count($cid));
			}
			else {
				$msg = JText::sprintf('%d Comments unpublished', count($cid));
			}
		}
		else {
			$msg = $model->getError();
		}

		$app->redirect('index.php?option=com_comment&view=comments', $msg);
	}

	public function notifypublish() {
		JRequest::checkToken() or jexit('Invalid Token');
		$app =& JFactory::getApplication();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$component 	= JArrayHelper::getValue( $_REQUEST, 'component', 'com_content' );
		$null = null;
		$comObject = JOSC_utils::ComPluginObject($component, $null);
		$config =& JOSC_config::getConfig(0, $comObject);
		
		if(!is_array($cid)|| count($cid) < 1) {
			echo "<script>alert('" . JText::_('Select a comment to publish') . "'); window.history.go(-1);</script>";
			exit;
		}

		$model = $this->getModel('comments');
		if($model->publish($cid, 1)) {
			if(count($cid) == 1) {
				$msg = JText::sprintf('%d Comment published', count($cid));
			}
			else {
				$msg = JText::sprintf('%d Comments published', count($cid));
			}

			$notification = new JOSC_notification($config);
			$sentemail = $notification->notifyComments($cid, 'publish');

			if($sentemail) {
				$msg .= ' ' . JText::_('Mailto:') .$sentemail . JText::_('sent');
			}
			else {
				$msg .= ' ' . JText::_('Could not send mail');
			}

		}
		else {
			$msg = $model->getError();
		}

		$app->redirect('index.php?option=com_comment&view=comments', $msg);
	}

	public function notifyunpublish() {
		$app =& JFactory::getApplication();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$component 	= JArrayHelper::getValue( $_REQUEST, 'component', 'com_content' );
		$null = null;
		$comObject = JOSC_utils::ComPluginObject($component, $null);
		$config =& JOSC_config::getConfig(0, $comObject);

		if(!is_array($cid)|| count($cid) < 1) {
			echo "<script>alert('" . JText::_('Select a comment to publish') . "'); window.history.go(-1);</script>";
			exit;
		}

		$model = $this->getModel('comments');
		if($model->publish($cid, 0)) {
			if(count($cid) == 1) {
				$msg = JText::sprintf('%d Comment unpublished', count($cid));
			}
			else {
				$msg = JText::sprintf('%d Comments unpublished', count($cid));
			}
			$notification = new JOSC_notification($config);
			$sentemail = $notification->notifyComments($cid, 'unpublish');

			if($sentemail) {
				$msg .= ' ' . JText::_('Mailto:') .$sentemail . JText::_('sent');
			}
			else {
				$msg .= ' ' . JText::_('Could not send mail');
			}

		}
		else {
			$msg = $model->getError();
		}

		$app->redirect('index.php?option=com_comment&view=comments', $msg);
	}

	public function notifyremove() {
		$app =& JFactory::getApplication();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$component 	= JArrayHelper::getValue( $_REQUEST, 'component', 'com_content' );
		$null = null;
		$comObject = JOSC_utils::ComPluginObject($component, $null);
		$config =& JOSC_config::getConfig(0, $comObject);

		if(!is_array($cid) || count($cid) < 1) {
			echo "<script>alert('" . JText::_('Select an item to remove') . "'); window.history.go(-1);</script>";
			exit;
		}

		$model = $this->getModel('comments');
		if($model->delete($cid)) {
			if(count($cid) == 1) {
				$msg = JText::sprintf('%d Comment Deleted', count($cid));
			}
			else {
				$msg = JText::sprintf('%d Comments Deleted', count($cid));
			}

			$notification = new JOSC_notification($config);
			$sentemail = $notification->notifyComments($cid, 'delete');

			if($sentemail) {
				$msg .= ' ' . JText::_('Mailto:') .$sentemail . JText::_('sent');
			}
			else {
				$msg .= ' ' . JText::_('Could not send mail');
			}

		}
		else {
			$msg = JText::_('Error Deleting Comment');
		}

		$app->redirect('index.php?option=com_comment&view=comments', $msg);
	}
}
?>
