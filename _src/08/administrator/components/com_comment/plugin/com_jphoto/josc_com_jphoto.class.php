<?php
defined('_JEXEC') or die('Restricted access');
/***************************************************************
*  Copyright notice
* 
*  THIS IS A COMMERCIAL PLUGIN! Please make sure that you have
* purchased it from compojoom.com 
*
*  Copyright 2010 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the !JoomlaComment project. The !JoomlaComment project is
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
class JOSC_com_jphoto extends JOSC_component {
	/* specific properties of REPLACEnewplugin if needed */

	public function __construct($component,&$row,&$list) {
		$this->id	= isset($row->id) ? $row->id : 0; /* document id */

		$this->setRowDatas($row); /* get specific properties */

		parent::__construct($component,0,$this->id);
	}

	/*
	 * Set specific properties
	 * will be called also in admin.html.php manage comments during row loop
	*/
	function setRowDatas(&$row) {
		if(is_object($row)) {
			$this->id	= isset($row->id) ? $row->id : 0;
			$this->alias  = $row->alias ? ':'.$row->alias : '';
		} else {
			$this->id = 0;
			$this->alias = '';
		}

	}

	/*
	 * This function is executed to check 
	 * if section/category of the row are authorized or not (exclude/include from the setting)
	 * return : true for authorized / false for excluded
	*/
	function checkSectionCategory(&$row, $include, $sections=array(), $catids=array(), $contentids=array()) {
		/* doc id excluded ? */
		if (in_array((($row->id == 0) ? -1 : $row->id), $contentids))
			return false;

		/* category included or excluded ? */
		$result = in_array((($row->cat_id == 0) ? -1 : $row->cat_id), $catids);
		if (($include && !$result) || (!$include && $result)) {
			return false;
		}


		return true;
	}

	function getPageId() {
		return $this->id;
	}

	/*
     * Condition to active or not the display of the post and input form
     * If the return is false, show readon will be executed.
	*/
	function checkVisual($contentId=0) {
		$option = JRequest::getCMD('option');
		$view = JRequest::getVar('view');

		return  (		$option == 'com_jphoto'
						&& 	$view == 'image'
		);
	}

	/*
	 * This function will active or deactivate the show readon display 
	*/
	function setShowReadon( &$row, &$params, &$config ) {
		$show_readon 	= $config->_show_readon;

		return $show_readon;
	}

	/*
     * construct the link to the content item  
     * (and also direct to the comment if commentId set)
	*/
	function linkToContent($contentId, $commentId='') {
		$application = JFactory::getApplication();

		if(!$this->alias) {
			$this->alias = ':'.$this->getAlias($contentId);
		}

		$itemId = '&Itemid='.JOSC_utils::getItemid('com_jphoto');

		$url = JRoute::_('index.php?option=com_jphoto&view=image&id='.$contentId  . $this->alias . $itemId).'#josc' . $commentId;

		if($application->isAdmin()) {
			$url = JURI::root().$url;
		}
			

		return $url;
	}

	function getAlias($id) {
		$database = JFactory::getDBO();
		$query = 'SELECT alias FROM #__jphoto_imgs WHERE id = ' . $id;
		$database->setQuery($query);
		$result = $database->loadObject();
		return $result->alias;
	}

	/*
     * clean the cache of the component when comments are inserted/modified...
     * (if cache is active) 
	*/
	function cleanComponentCache() {
		$cache =& JFactory::getCache('com_jphoto');
		$cache->clean('com_jphoto');
	}

	/*----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   A D M I N   P A R T 
	 *----------------------------------------------------------------------------------
	*/

	/*
     * section option list used to display the include/exclude section list in setting 
     * must return an array of objects (id,title)
	*/
	function getSectionsIdOption() {
		$database = JFactory::getDBO();

		$sectoptions = array();
		return $sectoptions;
	}

	/*
     * categories option list used to display the include/exclude category list in setting 
     * must return an array of objects (id,title)
	*/
	function getCategoriesIdOption() {
		$database = JFactory::getDBO();
		$catoptions = array();
		$query 	= "SELECT id, title"
				. "\n FROM #__jphoto_cats"
				. "\n WHERE published = 1"
				. "\n AND access >= 0"
				. "\n ORDER BY ordering"
		;
		$database->setQuery( $query );
		$catoptions= $database->loadObjectList();

		return $catoptions;

	}

	/*
	 * content items list (or single) for new and edit comment
	 * must return an array of objects (id,title)
	*/
	function getObjectIdOption($id=0, $select=true) {
		$database = JFactory::getDBO();

		$content = array();
		$query 	= "SELECT id AS id, title AS title"
				. "\n FROM #__jphoto_imgs "
				. ($id ? "\n WHERE id = $id":"")
				. "\n ORDER BY ordering"
		;
		$database->setQuery( $query );
		$content = $database->loadObjectList();
		if (!$id && $select && count($content)>0) {
			array_unshift( $content, mosHTML::makeOption( '0', '-- Select Img Item --', 'id', 'title' ) );
		}

		return $content;
	}
	function getViewTitleField() {
		$title = 'title';
		return($title);
	}
	function getViewJoinQuery($alias, $contentid) {
		$leftjoin	= "\n LEFT JOIN #__jphoto_imgs  AS $alias ON $alias.id = $contentid ";
		return $leftjoin;
	}

	/*----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   MOD_COMMENTS   M O D U L E 
	 *----------------------------------------------------------------------------------
	*/
	function mod_commentsGetMostCommentedQuery($secids, $catids, $maxlines) {
		$database = JFactory::getDBO();

		$component = $this->_component;

		$limit = $maxlines>=0 ? " limit $maxlines " : "";

		$where = array();
		if ($catids)
			$where[] = "gals.catid IN ($catids)";

		$query 	=  	"SELECT COUNT(c.id) AS countid, ct.id, ct.title AS title "
				.	"\n FROM `#__comment`    AS c "
				.  	"\n INNER JOIN `#__jphoto_imgs`  AS ct  ON ct.id = c.contentid "
				.  	"\n INNER JOIN `#__jphoto_gals` AS gals ON gals.catid = ct.gallery "
				. " INNER JOIN `#__jphoto_cats` AS cats ON cats.id = gals.catid"
				.  	"\n WHERE c.published='1' "
				.	"\n   AND c.component='$component' "
				.  	"\n ". (count($where) ? (" AND ".implode(" AND ", $where)) : "")
				.	"\n GROUP BY c.contentid"
				.	"\n ORDER BY countid DESC"
				.	"\n $limit"
		;

		$database->SetQuery($query);
		$rows = $database->loadAssocList();

		return $rows;
	}

	function mod_commentsGetOthersQuery($secids, $catids, $maxlines, $orderby) {
		$database = JFactory::getDBO();

		$component = $this->_component;

		$limit = $maxlines>=0 ? " limit $maxlines " : "";

		$where = array();
		if ($catids)
			$where[] = "gals.catid IN ($catids)";

		if ($orderby=='mostrated') {
			$mostrated =  ", (c.voting_yes-c.voting_no)/2 AS mostrated";
			$where[]  = "(c.voting_yes > 0 OR c.voting_no > 0)";
		} else {
			$mostrated = "";
			$orderby = "c.$orderby";
		}

		/*
   	 	 * TODO: restrict according to user rights, dates and category/secitons published and dates...
		*/
		$query 	=  "SELECT c.*, imgs.title AS ctitle $mostrated "
				.  "\n FROM `#__comment`    AS c "
				.  "\n INNER JOIN `#__jphoto_imgs`    AS imgs  ON imgs.id = c.contentid "
				.  "\n INNER JOIN `#__jphoto_gals` AS gals ON gals.id = imgs.gallery "
				. " INNER JOIN `#__jphoto_cats` AS cats ON cats.id = gals.catid"
				.  "\n WHERE c.published='1' "
				.	"\n  AND c.component='$component' "
				.  "\n ". (count($where) ? (" AND ".implode(" AND ", $where)) : "")
				.  "\n ORDER BY $orderby DESC "
				.  "\n $limit"
		;

		$database->SetQuery($query);
		$rows = $database->loadAssocList();

		return $rows;
	}

}
?>