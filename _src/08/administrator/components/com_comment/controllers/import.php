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

class CommentControllerImport extends CommentController {

	public function save() {
		JRequest::checkToken() or jexit('Invalid Token');
		$mainframe =& JFactory::getApplication();
		$database =& JFactory::getDBO();

		$fromcomponent = JArrayHelper::getValue( $_REQUEST, 'fromcomponent', null );
		$fromtable  	= JArrayHelper::getValue( $_REQUEST, 'fromtable', null );
		$savequeries = JArrayHelper::getValue( $_REQUEST, 'savequeries', false );
		$component 	= JArrayHelper::getValue( $_REQUEST, 'component', 'com_content' );
		
		$model = $this->getModel('Import');
		if (!$fromtable) {
			echo "<script> alert('Select at least a table. Check your setting.'); window.history.go(-1);</script>\n";
			exit;
		}

		if ($model->checkExistFromTableComments($component, $fromtable)) {
			$msg = 'Import cancelled ! Comments imported from '.$fromtable.' and for '. $component .' ALREADY EXIST !!';
			$mainframe->redirect("index.php?option=com_comment&view=comments&component=$component&search=$fromtable", $msg);
		}

		$save = $model->save();
		if($save['result']) {
			$message = JText::_('Comments has been imported. Please verify the result below.');
			if ($savequeries) {
				if ($file = $model->save_importQuery( $save['queries'], $fromcomponent )) {
					$message .= JText::_("SQL QUERIES HAVE BEEN SAVED IN THE" ) . $file;
				} else {
					$message .= JText::_("DID NOT SUCCEED TO SAVE SQL QUERIES IN" ) . $file;
				}
			}
		} else {
			$message = JText::_('Import failed');
			$message .= ' [' . $model->getError(). ']';
			$message .= JText::_('Please copy the error message and contact the joomlacomment support');
		}

		$mainframe->redirect("index.php?option=com_comment&view=comments&component=$component&search=$fromtable", $message);

	}
	public function apply() {
		JRequest::checkToken() or jexit('Invalid Token');
		$fromcomponent = JArrayHelper::getValue( $_REQUEST, 'fromcomponent', null );
		$fromtable  	= JArrayHelper::getValue( $_REQUEST, 'fromtable', null );

		$mainframe  = JFactory::getApplication();
		$view = $this->getView('import', 'html');
		$model = $this->getModel('import');

		if (!$fromtable) {
			$msg = "<b>Please, select at least a table.</b>";
			$mainframe->redirect('index.php?option=com_comment&view=import', $msg, 'error');
		}

		$rows = $model->preview();
		$preview = true;

		$view->assignRef('preview', $preview);
		$view->assignRef('comments', $rows);
		$view->assignRef('model', $model);
		$view->display();
	}
}
?>
