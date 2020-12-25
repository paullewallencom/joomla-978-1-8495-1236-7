<?php
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
defined('_JEXEC') or die();

jimport( 'joomla.application.component.controller' );
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'joscomment' . DS . 'utils.php');
/**
 * Plugins Component Controller
 *
 * @package		Joomla
 * @subpackage	Plugins
 * @since 1.5
 */
class CommentControllerSettings extends CommentController {
	function __construct() {

		parent::__construct();
		$this->registerTask( 'add', 'edit');
		$this->registerTask( 'apply', 'save' );
	}

	function edit() {
		$id = JRequest::getVar('id');


		$viewName = JRequest::getVar('view');
		$view =& $this->getView($viewName, 'html');
		$model =& $this->getModel($viewName);

		$backend = null;
		$config = JOSC_config::getConfig($id, $backend);

		if($id) {
			JToolbarHelper::title('Edit plugin settings for ' . $config->_set_component);
		}
		else {
			JToolbarHelper::title('New plugin settings');
		}

		$view->assignRef('model', $model);
		$view->assignRef('set_id', $id);
		$view->assignRef('config', $config);
		$view->setLayout('edit');
		$view->display();
	}

    /*
     * function to save the configuration
     */
	function save() {
		JRequest::checkToken() or jexit('Invalid Token');
		$mainframe = JFactory::getApplication();
		$set_id = JRequest::getVar('set_id','', $_POST);
		$backend = null;
		$config = JOSC_config::getConfig($set_id, $backend);
		$model =& $this->getModel('settings');

		$option = JRequest::getVar('option','', $_POST);
		$database =& JFactory::getDBO();

		$expert  = (strpos($task, 'expert')===false) ? "" : "expert";
		$simple  = (strpos($task, 'simple')===false) ? "" : "simple";

                /* simple parameters */
		$params = JRequest::getVar('params', '', $_POST);

                /* arrays */
		$params['_moderator'] = implode( ',', JOSC_library::JOSCGetArrayInts('_moderator'));
		$params['_exclude_sections'] = implode( ',', JOSC_library::JOSCGetArrayInts('_exclude_sections') );
		$params['_exclude_categories'] = implode( ',', JOSC_library::JOSCGetArrayInts('_exclude_categories') );
		$params['_IP_usertypes'] = implode( ',', JOSC_library::JOSCGetArrayInts('_IP_usertypes') );
		$params['_captcha_usertypes'] = implode( ',', JOSC_library::JOSCGetArrayInts('_captcha_usertypes') );
		$params['_censorship_usertypes'] = implode( ',', JOSC_library::JOSCGetArrayInts('_censorship_usertypes') );
                /* dependant parameters */
		$params['_template_css'] = $params['_template_css'.$params['_template']];
		$params['_template_custom_css']	= $params['_template_custom_css'.$params['_template_custom']];


		$component = JRequest::getVar('set_component','', $_POST);
		$sectionid = JRequest::getVar('set_sectionid', 0, $_POST );

		if ($set_id!=1 && !$component && $sectionid==0) {
			echo "<script> alert('Select another component'); window.history.go(-1); </script>\n";
			exit();
		}

		if (is_array( $params )) {
			$txt = array();
			foreach ($params as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$_POST['params'] = $model->textareaHandling($txt);
		}
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_comment'.DS.'tables');
		$row =& JTable::getInstance('Setting', 'Table');
		$row->load($set_id);
		if ($row->params == null) {
			$row->id = 0;
		}
		if (!$row->bind($_POST)) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

		if (!$row->store()) {
			echo "<script> alert('" . $row->getError() . "'); window.history.go(-1); </script>\n";
			exit();
		}

        /* Save CURRENT TEMPLATE_CUSTOM HTML AND CSS */
		if ($params['_template_modify']) {

			$model->setTemplateCustomPath(); /* set custom path and make copy if param set... */

			$templateHTML 		= strval( JRequest::getVar('joscTemplateHTML', '', $_POST ) );
			$templateHTMLcontent 	= strval(JRequest::getVar( 'joscTemplateHTMLcontent', '', $_POST, '', 2));
			$enable_writeHTML 	= JRequest::getVar('enable_writeHTML',0,$_POST);
			$disable_writeHTML 	= JRequest::getVar('disable_writeHTML',0, $_POST);


			if (($templateHTML == $params['_template_custom']) 	 && strcmp($templateHTMLcontent,'')) {
				$msg = "";


				if (!$model->saveTemplateHTMLSource( $templateHTML, $templateHTMLcontent, $enable_writeHTML, $disable_writeHTML, $msg, $config->_template_custom_path )) {
					$msg = " Template not saved: " . $msg;
				}
			}


			$templateCSS		= strval( JRequest::getVar('joscTemplateCSS', '',$_POST ) );
			$templateCSSCSS		= strval( JRequest::getVar('joscTemplateCSSCSS', '',$_POST ) );
			$templateCSScontent 	= JRequest::getVar(  'joscTemplateCSScontent', '', $_POST );
			$enable_writeCSS 	= JRequest::getVar('enable_writeCSS',0, $_POST);
			$disable_writeCSS 	=JRequest::getVar('disable_writeCSS',0,$_POST);

			if ($templateCSS == $params['_template_custom'] && $templateCSSCSS == $params['_template_custom_css']	 && $templateCSScontent) {
				$msg = "";
				if (!$model->saveTemplateCSSSource( $templateCSS, $templateCSSCSS, $templateCSScontent, $enable_writeCSS, $disable_writeCSS, $msg, $config->_template_custom_path )) {
					$msg = " Template not saved: " . $msg;
				}
			}
		}



		switch (JRequest::getCmd('task')) {
			case 'apply' :
				if(!$set_id) {
					$set_id = $row->id;
				}
				$link = JRoute::_('index.php?option=com_comment&view=settings&controller=settings&task=edit&id='.$set_id, false);
				break;
			case 'save':
				$link = JRoute::_('index.php?option=com_comment&view=settings', false);
				break;

		}

		$mainframe->redirect($link, 'Settings saved. '.$msg );
	}
	public function remove() {
		JRequest::checkToken() or jexit('Invalid Token');
		$mainframe = JFactory::getApplication();
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$database = JFactory::getDBO();
		if (count($cid)) {
			$cids = implode(',', $cid);
			$query = 'DELETE FROM ' . $database->nameQuote('#__comment_setting')
				. ' WHERE id IN (' . $cids . ')';
			$database->setQuery($query);

			if(!$database->query()) {
				echo "<script> alert('" . $database->getErrorMsg() . "');
		    window.history.go(-1); </script>";
			}
		}

		$mainframe->redirect('index.php?option=com_comment&view=settings');
	}

	public function cancel() {
		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_comment&view=settings');
	}
}
?>