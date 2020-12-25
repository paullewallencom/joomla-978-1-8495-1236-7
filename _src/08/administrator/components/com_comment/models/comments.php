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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class commentModelComments extends JModel {
	public $_total = null;
	private $_pagination = null;
	private $_data = null;

	public function __construct() {
		parent::__construct();
		$mainframe =& JFactory::getApplication();

		$this->component = $mainframe->getUserStateFromRequest('com_comment.component', 'component','com_content', '');
		$limit 		= intval( $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' ) );
		$limitstart = JRequest::getVar('limitstart', 0, '', 0 ) ;
		$limitstart = ($limit !=0 ? (floor($limitstart/$limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getData() {
		if(empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	public function getTotal() {
		if(empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}

	public function getPagination() {
		if(empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_pagination;
	}

	private function _buildQuery() {
		$database = JFactory::getDBO();

		$null=null;
		$this->componentObj = JOSC_utils::ComPluginObject($this->component,$null);
		$ctitle 	= $this->componentObj->getViewTitleField();
		$leftjoin 	= $this->componentObj->getViewJoinQuery('ct', 'c.contentid');
		
		$query 	= "SELECT c.*, u.email AS usermail,ct.$ctitle AS ctitle FROM #__comment AS c"
			. " LEFT JOIN #__users  AS u ON u.id = c.userid "
			. $leftjoin
			. $this->_buildWhereQuery()
			. $this->_buildOrderByQuery()
		;
		return $query;
	}

	private function _buildWhereQuery() {
		$mainframe =& JFactory::getApplication();
		$database =& JFactory::getDBO();
		$ctitle 	= $this->componentObj->getViewTitleField();
		$search = $mainframe->getUserStateFromRequest("com_comment.search", 'search', '');
		$search = $database->getEscaped(trim(strtolower($search)));

		$publishedState = $mainframe->getUserStateFromRequest('com_comment.filterState', 'filter_state', '', 'word');
		$queryWhere =  " WHERE c.component = '$this->component' ";

		if($publishedState == 'P') {
			$queryWhere .= ' AND c.' . $database->nameQuote('published') . ' = 1';
		} elseif($publishedState == 'U') {
			$queryWhere .= ' AND c.' . $database->nameQuote('published') . ' = 0';
		}

		$where = array();
		if ($search) {
			$where[] = "LOWER(c.comment) LIKE '%$search%'";
			$where[] = "LOWER(ct.$ctitle) LIKE '%$search%'";
			$where[] = "LOWER(c.name) LIKE '%$search%'";
			$where[] = "LOWER(c.website) LIKE '%$search%'";
			$where[] = "LOWER(c.email) LIKE '%$search%'";
			$where[] = "LOWER(c.ip) LIKE '%$search%'";
			$where[] = "LOWER(c.importtable) LIKE '%$search%'";
		}

		if(count($where)) {
			$queryWhere .=  " AND ( ".implode(' OR ', $where)." )";
		}

		return $queryWhere;
	}

	private function _buildOrderByQuery() {
		$app =& JFactory::getApplication();
		$db =& JFactory::getDBO();

		$defaultOrderField = 'date';
		$order = $app->getUserStateFromRequest('com_comment.filterOrder', 'filter_order', $defaultOrderField, 'word');
		$orderDirection = $app->getUserStateFromRequest('com_comment.filterOrderDirection', 'filter_order_Dir', 'DESC', 'cmd');

		$orderBy = ' ORDER BY ' . $db->nameQuote($order) . ' ' . $orderDirection;
		return  $orderBy;
	}

	public function getComment($id) {
		$database = JFactory::getDBO();
		$query = 'SELECT * FROM ' . $database->nameQuote('#__comment')
			. ' WHERE id = ' . $database->Quote($id);

		$database->setQuery($query);
		$comment = $database->loadObject();
		return $comment;
	}

	public function delete($cid = array()) {
		if(count($cid)) {
			$database =& JFactory::getDBO();

			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);

			$query = 'DELETE FROM ' . $database->nameQuote('#__comment') . ' WHERE id IN (' . $cids . ')';
			$database->setQuery($query);
			if(!$database->query()) {
				$this->setError($database->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	public function publish( $cid, $publish ) {
		$database =& JFactory::getDBO();
		if (count( $cid )) {
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query ='UPDATE' . $database->nameQuote('#__comment')
			. ' SET published = ' . intval( $publish )
			. " WHERE id IN ( $cids )"
			;

			$database->setQuery( $query );
			if (!$database->query()) {
				$this->setError($database->getErrorMsg());
				return false;
			}
		}

		return true;
	}

}
?>
