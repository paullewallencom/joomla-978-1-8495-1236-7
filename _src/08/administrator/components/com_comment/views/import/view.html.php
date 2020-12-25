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

jimport( 'joomla.application.component.view');

class CommentViewImport extends JView {
	public function display($tpl = null) {

		$lists = array();
		$sel_columns = false;

		if(!$this->model) {
			$model =& $this->getModel();
		} else {
			$model = $this->model;
		}

		$data = $model->getData();
		$sel_columns = $data['sel_columns'];

		$component 	= JArrayHelper::getValue( $_REQUEST, 'component', 'com_content' );
		$selected = $lists['component'] = $component;
		$componentlist = JOSC_library::getComponentList();
		$lists['componentlist'] = JHTML::_('select.genericlist',$componentlist, 'component', 'class="inputbox" onchange="document.adminForm.submit();"', 'value', 'text', $selected);

		$selected = $data['fromcomponent'];
		$fromcomponents = $model->setImport_ComponentList();
		array_unshift( $fromcomponents, JHTML::_('select.option',  '', '-- from component --', 'fromcomponent', 'desc' ) );
		$lists['fromcomponent'] = JHTML::_('select.genericlist', $fromcomponents, 'fromcomponent', ' class="inputbox" onchange="submitform()" ', 'fromcomponent', 'desc', $selected );

		$selected = $lists['fromtable'] = $data['fromtable'];
		$tablename = $data['tablename'];
		array_unshift( $tablename, JHTML::_('select.option',  '', '-- Select a table name --', 'tablename', 'desc' ) );
		$lists['fromtablelist'] = JHTML::_('select.genericlist', $tablename, 'fromtable', ' class="inputbox" onchange="submitform()" ', 'tablename', 'desc', $selected );

		$selected = '';
		$columns = $data['columns'];
		array_unshift( $columns, JHTML::_('select.option',  '', '-- column --', 'Field', 'desc') );
		$lists['columns'] = $columns; /* ->field, ->desc */
		$lists['sel_columns'] = $sel_columns; 	 /* ['sel_columns][joscolumn] = component_column */

		$lists['savequeries'] = JHTML::_('select.booleanlist', 'savequeries', 'class="inputbox"', false);

		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}
?>
