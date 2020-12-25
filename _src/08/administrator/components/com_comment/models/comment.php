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

class commentModelComment extends JModel {

	private $_data = null;
	private $_id = null;
	
	public function __construct() {
		parent::__construct();

		$cid = JRequest::getVar('cid', array(0), '', 'array');
		JArrayHelper::toInteger($cid, array(0));
		$this->_id = JRequest::getVar( 'id', $cid[0], '', 'int' );
	}

	public function getData() {
		if (empty($this->_data)) {
			$this->_data =& $this->getTable('comment');
			if ($this->_id) {
				$this->_data->load( $this->_id );
			}
		}
		return $this->_data;
	}

	function store(&$data)
	{
		$row =& $this->getTable('comment');

		// bind the data
		if (!$row->bind($data))
		{
			$this->setError($row->getError());
			return false;
		}

		// store the data
		if (!$row->store())
		{
			$this->setError($row->getError());
			return false;
		}

		$data['id'] = $row->id;

		return true;
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
