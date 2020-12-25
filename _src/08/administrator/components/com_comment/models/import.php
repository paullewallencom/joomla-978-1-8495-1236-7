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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once(JPATH_COMPONENT_ADMINISTRATOR. DS.'library' . DS . 'importPatterns.php');
class commentModelImport extends JModel {

	function setImport_ComponentList() {
		$result = array();
		/*
	 * important: the fromcomponent value will be used as function name
	 */
		$fromcomponent = 'mXcomment';
		if (function_exists("getImport_".$fromcomponent) && call_user_func( "getImport_".$fromcomponent, true))
			$result[] = JHTML::_('select.option',  $fromcomponent, $fromcomponent, 'fromcomponent', 'desc' );
		$fromcomponent = 'AkoComment';
		if (function_exists("getImport_".$fromcomponent) && call_user_func( "getImport_".$fromcomponent, true))
			$result[] = JHTML::_('select.option',  $fromcomponent, $fromcomponent, 'fromcomponent', 'desc' );
		$fromcomponent = 'JReaction';
		if (function_exists("getImport_".$fromcomponent) && call_user_func( "getImport_".$fromcomponent, true))
			$result[] = JHTML::_('select.option',  $fromcomponent, $fromcomponent, 'fromcomponent', 'desc' );
		$fromcomponent = 'JomComment';
		if (function_exists("getImport_".$fromcomponent) && call_user_func( "getImport_".$fromcomponent, true))
			$result[] = JHTML::_('select.option',  $fromcomponent, $fromcomponent, 'fromcomponent', 'desc' );
		$fromcomponent = 'YvComment';
		if (function_exists("getImport_".$fromcomponent) && call_user_func( "getImport_".$fromcomponent, true))
			$result[] = JHTML::_('select.option',  $fromcomponent, $fromcomponent, 'fromcomponent', 'desc' );
		$fromcomponent = 'Wordpress';
		if (function_exists("getImport_".$fromcomponent) && call_user_func( "getImport_".$fromcomponent, true))
			$result[] = JHTML::_('select.option',  $fromcomponent, $fromcomponent, 'fromcomponent', 'desc' );

		return $result;
	}


	public function getData() {
		$data = array();
		$onchangecomponent =  JArrayHelper::getValue( $_REQUEST, 'onchangecomponent', null );
		$fromcomponent = JArrayHelper::getValue( $_REQUEST, 'fromcomponent', null );
		$fromtable  	= JArrayHelper::getValue( $_REQUEST, 'fromtable', null );
		$component 	= JArrayHelper::getValue( $_REQUEST, 'component', '' );
		if ($fromcomponent) {
			/*
		* from component = propose automatic columns selection
		*/
			if ($fromcomponent && function_exists("getImport_".$fromcomponent)) {
				if ($result = call_user_func("getImport_".$fromcomponent)) {
					$fromtable 		= $result['fromtable'];
					$sel_columns	= $result['sel_columns']; 	 /* ['sel_columns'][joscolumn] = component_column */
				}
			}
		} else {
			/*
		* get settings Parameters
		*/
			$joscomment = JOSC_TableUtils::TableColumnsGet( '#__comment' );	
			foreach($joscomment as $col) {
				$param = JArrayHelper::getValue( $_REQUEST, $col->Field, null );
				$sel_columns[$col->Field] = $param;
			}
			$param = JArrayHelper::getValue( $_REQUEST, 'componentfield', null );
			$sel_columns['componentfield'] = JArrayHelper::getValue( $_REQUEST, 'componentfield', null );


		}
		$data['sel_columns'] = $sel_columns;
		$database =& JFactory::getDBO();
		$tablelist = JOSC_TableUtils::getTableList();

		if ($fromtable && !in_array( $fromtable, $tablelist )) {
			$fromtable = null;
		}

		if ($this->checkExistFromTableComments($component, $fromtable)) {

			$url = "index.php?option=com_comment&component=$component&search=$fromtable";
			echo "<b>!!!</b> Comments imported <b><u>from ".$fromtable."</u></b> and <b><u>for ".JOSC_utils::getComponentName($component)."</u> ALREADY EXIST !!</b>"
				."<br />This is not expected. Please <a href=\"$url\">CHECK</a> why there are alreay existing imported comments for this key."
				."<br />To be able to import, there must be no existing comment already imported from the (".$fromtable.",".JOSC_utils::getComponentName($component).") key."
				."<br /><br />";
		}

		$data['tablename'] = array();
		$data['columns'] = array();
		foreach ($tablelist as $tn) {

		// make sure we get the right tables based on prefix
			if (!preg_match( "/^".$database->getPrefix()."/i", $tn )) {

				continue;
			}

			if ($tn==$fromtable) {
			/*
			* get all fields of the selected table
			*/

				$tablecolumns = JOSC_TableUtils::TableColumnsGet( $tn );
				foreach($tablecolumns as $col) {
					$data['columns'][] = JHTML::_('select.option',  $col->Field, $col->Field, 'Field', 'desc');
				}
			}
			$data['tablename'][] = JHTML::_('select.option',  $tn, $tn, 'tablename', 'desc');
		}

		$data['fromtable'] = $fromtable;
		$data['fromcomponent'] = $fromcomponent;

		return $data;
	}

	function checkExistFromTableComments($component, $fromtable) {
		if (!$fromtable) {
			return false;
		}

		$database =& JFactory::getDBO();


	    /* check no comment for fromtable AND component */
		$query =  "SELECT id FROM #__comment "
			. "\n   WHERE component='$component'"
			. "\n     AND importtable='$fromtable'"
			. "\n   LIMIT 1"
		;
		$database->setQuery($query);
		if ($database->loadResult()) {
			return true;
		} else {
			return false;
		}

	}

	public function save() {
		$database =& JFactory::getDBO();
		$this->component 	= JArrayHelper::getValue( $_REQUEST, 'component', '' );
		$fromcomponent = JArrayHelper::getValue( $_REQUEST, 'fromcomponent', null );
		$fromtable  	= JArrayHelper::getValue( $_REQUEST, 'fromtable', null );
			/*
	 * construct SELECT [FIELDS] FROM [FROMTABLE]
	 */
		$columns = JOSC_TableUtils::TableColumnsGet( '#__comment' );

	/*
	 * construct SELECT clause from GetParam
	 * 		$fromlist 	= from columns list (component)
	 * 		$tolist 	= to columns list  (joomlacomment)
	 */
		$tolist   = array();
		$fromlist = array();
		$idfound  = false;
		$parentidfound  = false;
		$contentidfound  = false;

		foreach($columns as $col) {
			$param = JArrayHelper::getValue( $_REQUEST, $col->Field, null );
			
			
			if ($param) {
				if ($col->Field == 'id') {
					$idfound = true;
					$col->Field = 'importid';
				}
				if ($col->Field == 'parentid') {
					$parentidfound = true;
					$col->Field = 'importparentid';
				}
				if ($col->Field == 'contentid') {
					$contentidfound = true;
				}
				if ($col->Field == 'component') {
					continue;
				}
				$tolist[] 	= $database->nameQuote($col->Field);
				$fromlist[] = $database->nameQuote($param);
			}
		}

		$tolist[] 	= $database->nameQuote('component');
		if($this->component) {
			$fromlist[] = $database->Quote($this->component);
		} else {
			$fromlist[] = "'com_content'";
		}

		$tolist[] 	= $database->nameQuote('importtable'); /* the importable name in the importtable field - in case of problem... */
		$fromlist[] = $database->Quote($fromtable);

		if (!$fromlist) {
			echo "<script> alert('Select at least one field. Check your setting.'); window.history.go(-1);</script>\n";
			exit;
		}

		if (!$idfound) {
			echo "<script> alert('The Id field is obligatory. Check your setting.'); window.history.go(-1);</script>\n";
			exit;
		}

		if (!$contentidfound) {
			echo "<script> alert('The ContentId field is obligatory. Check your setting.'); window.history.go(-1);</script>\n";
			exit;
		}

		$queries = ""; // will contain queries history

	/*
	 * INSERT
	 *  and save source id and source parentid
	 * 			in importid/importparentid field.
	 */
		$query 	= 'INSERT INTO ' .  $database->nameQuote('#__comment') . '('.implode(',', $tolist).')'
			. ' SELECT '.implode(',', $fromlist).' FROM ' . $database->nameQuote($fromtable) . ' AS f'
			. $this->_getWhereQuery()
			. $this->_getOrderByQuery($fromlist);


		$database->setQuery($query);
		$queries .= $database->_sql.";";
		$result = $database->query();
		if ($result) {
	/* importparentid > 0 and parentid <= 0
	 *      parentid = id of the importid = parentid
	 */
			$query = ' UPDATE ' . $database->nameQuote('#__comment') . ' AS ' . $database->nameQuote('cupdate')
				. ' JOIN ' . $database->nameQuote('#__comment') .  ' AS ' . $database->nameQuote('cselect')
				. ' ON ' . $database->nameQuote('cselect.importtable') . ' = ' . $database->nameQuote('cupdate.importtable')
				. ' AND ' . $database->nameQuote('cselect.importid') . ' = ' . $database->nameQuote('cupdate.importparentid')
				. ' SET ' . $database->nameQuote('cupdate.parentid') . ' = ' . $database->nameQuote('cselect.id')
				. ' WHERE ' . $database->nameQuote('cupdate.parentid') . ' <= 0 '
				. '	AND ' . $database->nameQuote('cupdate.importparentid') .' > 0 ';

			$database->setQuery($query);
			$queries .= "\n" . $database->_sql.";";
			$result = $database->query();
		}
		if ($result) {
		/*
		 * set -1 to parentid not found (or because in other component it is 0 and not -1)
		 * it must be -1 in joomlacomment.
		 */
			$query 	= ' UPDATE ' . $database->nameQuote('#__comment')
				. ' SET ' . $database->nameQuote('parentid') . '=-1'
				. ' WHERE ' . $database->nameQuote('parentid') . ' = 0';
			$database->setQuery($query);
			$queries .= "\n" . $database->_sql.";";
			$result = $database->query();
		}
		$return['result'] = $result;
		$return['queries'] = $queries;
		return $return;
	}

	private function _getWhereQuery() {
		$database =& JFactory::getDBO();
		$comfield = JArrayHelper::getValue( $_REQUEST, 'componentfield', null );

		$where = array();
		if ($comfield) {
			$where[] = $database->nameQuote('f.'.$comfield) . '='. $database->Quote($this->component);
		}

		if(count($where)) {
			$where = ' WHERE ' . implode(' AND ', $where);
		} else {
			$where = '';
		}
		return $where;
	}

	private function _getOrderByQuery($fromlist) {
		$database =& JFactory::getDBO();
		$oderBy = ' ORDER BY ' . $database->nameQuote(" f.$fromlist[0]");
		return $orderBy;
	}

	public function save_importQuery( $query, $component='', $option='') {
		$app = JFactory::getApplication();
		$File = "media/joscomment_importquery_$component.sql";

		if ($fp = fopen( JPATH_SITE."/".$File , "w")) {
			fputs($fp, $query, strlen($query));
			fclose ($fp);
		} elseif ($option) {
			$app->redirect("index.php?option=$option", 'File $File creation error!');
			break;
		} else {
			return false;
		}

		if ($option) {
			$app->redirect("index.php?option=$option", 'Query saved');
		} else {
			return $File;
		}
	}

	public function preview() {
		$database =& JFactory::getDBO();
		$app = JFactory::getApplication();
		$component 	= JArrayHelper::getValue( $_REQUEST, 'component', '' );
		/*
	 * construct SELECT [FIELDS] FROM [FROMTABLE]
	 */
		$columns = JOSC_TableUtils::TableColumnsGet( '#__comment' );

	/*
	 * construct SELECT clause from GetParam
	 */
		$fields = array();
		$idfound = false;
		$contentidfound = false;
		$forcomponent = $component;

		foreach($columns as $col) {
			$param = JArrayHelper::getValue( $_REQUEST, $col->Field, null );
			if ($param) {
				if (!($col->Field == 'component')) {
					$fields[] = $database->nameQuote('f.'.$param) . ' AS ' . $database->nameQuote($col->Field);
					if ($col->Field == 'id') {
						$idfound = true;
					}
					if ($col->Field == 'contentid') {
						$joincontentid = $database->Quote('f.'.$param); /* for the left join content item */
						$contentidfound = true;
					}
				}
			}
		}

		if (!$idfound) {
			$msg =  "<b>Please select a commentId column</b>";
			$app->redirect('index.php?option=com_comment&view=import', $msg, 'error');
		}

		if (!$contentidfound) {
			$msg = "<b>Please select a ContentId column!</b>";
			$app->redirect('index.php?option=com_comment&view=import', $msg, 'error');
		}
		$this->fields = $fields;
		if(empty($this->_data)) {
			$query = $this->_selectQuery($fields, $joincontentid);
			$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}

	private function _selectQuery($fields, $joincontentid) {
		$database = JFactory::getDBO();
		$fromtable  	= JArrayHelper::getValue( $_REQUEST, 'fromtable', null );
		$queryfrom  = ' FROM ' . $database->nameQuote($fromtable) .' AS ' . $database->nameQuote('f');

		if ($joincontentid) {
			$queryfrom .= ' LEFT JOIN ' . $database->nameQuote('#__content') . ' AS ' . $database->nameQuote('ct') . ' ON ' . $database->nameQuote('ct.id') . ' = ' . $database->Quote($joincontentid);
			$fields[] 	= $database->nameQuote('ct.title') . ' AS ' . $database->nameQuote('ctitle'); /* for the left join content item */
		}

		$query	= 'SELECT ' . implode(', ', $fields)
					. $queryfrom
					. $this->_selectWhereQuery()
					. $this->_selectOrderByQuery();


		return $query;
	}

	private function _selectWhereQuery() {
		$database =& JFactory::getDBO();
		$component 	= JArrayHelper::getValue( $_REQUEST, 'component', '' );
		$forcomponent = $component;
		$comfield = JArrayHelper::getValue( $_REQUEST, 'componentfield', null );
		$where = array();
		if ($comfield) {
			$where[] = $database->nameQuote('f.'.$comfield) . '='. $database->Quote($forcomponent );
		}

		if(count($where)) {
			$querywhere = ' WHERE ' . implode(' OR ', $where);
		} else {
			$querywhere = '';
		}
		return $querywhere;
	}

	private function _selectOrderByQuery() {
		$orderBy = ' ORDER BY id ';
		return $orderBy;
	}
}
?>
