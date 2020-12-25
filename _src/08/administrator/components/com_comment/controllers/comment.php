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
class CommentControllerComment extends CommentController {
	public function __construct() {
		parent::__construct( );
		$this->registerTask( 'apply', 'save' );
	}
	
	public function save() {
		JRequest::checkToken() or jexit('Invalid Token');
		$mainframe = JFactory::getApplication();

		$post = JRequest::get('post');

		$model =& $this->getModel('comment');
		if ($model->store($post)) {
			$msg = JText::_('Comment Saved');
		} else {
			$msg = JText::_('Error Saving Comment');
		}

		switch (JRequest::getCmd('task'))
		{
			case 'apply':
				$link = 'index.php?option=com_comment&view=comments&task=edit&controller=comments&cid[]='. $post['id'];
				break;
			case 'save':
			default:
				$selectedcomponent = $mainframe->getUserStateFromRequest('com_comment.component', 'component','com_content', '');
				$link = 'index.php?option=com_comment&view=comments&component='.$selectedcomponent;
				break;
		}
		$mainframe->redirect($link, $msg);
	}
	public function cancel() {
		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_comment&view=comments');
	}
}
?>
