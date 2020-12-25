<?php
/***************************************************************
*  $Revision$
*
*  Copyright notice
*
*  Copyright 2010 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the CompojoomComment project. The CompojoomComment project is
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
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

defined('_JEXEC') or die('Restricted access');

class JOSC_com_k2 extends JOSC_component {
	private $id;
	private $feedName = 'k2Rss';
	private $component = 'com_k2';
	
	public function __construct($component,&$row,&$list) {
		$this->id	= isset($row->id) ? $row->id : 0; /* document id */
		$this->setRowDatas($row); /* get specific properties */
		parent::__construct($component,0,$this->id);
	}

	/*
	 * Set specific properties
	 * will be called also in admin.html.php manage comments during row loop
	*/
	public function setRowDatas(&$row) {

	}

	/*
	 * This function is executed to check
	 * if section/category of the row are authorized or not (exclude/include from the setting)
	 * return : true for authorized / false for excluded
	*/
	public function checkSectionCategory(&$row, $include, $sections=array(), $catids=array(), $contentids=array()) {
		/* doc id excluded ? */
		if (in_array((($row->id == 0) ? -1 : $row->id), $contentids))
			return false;

		/* category included or excluded ? */
		$result = in_array((($row->catid == 0) ? -1 : $row->catid), $catids);
		if (($include && !$result) || (!$include && $result))
			return false; /* include and not found OR exclude and found */

		return true;
	}

	/*
     * Condition to active or not the display of the post and input form
     * If the return is false, show readon will be executed.
	*/
	public function checkVisual($contentId=0) {
		// we need to make this hack in case the plugin is called from the module
		$appl =& JFactory::getApplication();
		if($appl->scope == 'mod_k2_content') {
		  return false;
		}

		$option = JRequest::getCMD('option');
		$view = JRequest::getVar('view');

		return ($option == 'com_k2' && $view == 'item');

	}

	/*
	 * This function will active or deactivate the show readon display
	*/
	public function setShowReadon( &$row, &$params, &$config ) {
		$show_readon 	= $config->_show_readon;
		return $show_readon;
	}

	public function getPageId() {
		return $this->id;
	}

	public function createFeed() {
		require_once(JPATH_SITE."/includes/feedcreator.class.php");
		$contentid = JRequest::getInt('contentid','','GET');
		$component = 'com_k2';

		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__k2_items WHERE id='$contentid'");
		$content = $database->loadObject();
		$rss = new UniversalFeedCreator();
		$folderPath = JPATH_SITE . DS .'media'.DS. 'com_comment'.DS.'rss'.DS .$this->component;
		$folderExists = JFolder::exists($folderPath);
		if(!$folderExists) {
			JFolder::create($folderPath);
		}
		$rss->useCached("RSS2.0", $folderPath .DS.$this->feedName.$contentid.'.xml');
		$rss->title = $content->title. ' - comments' ;
		$rss->description = $content->alias;
		$rss->link = JURI :: base();
		$database->setQuery("SELECT *,UNIX_TIMESTAMP( date ) AS rss_date FROM #__comment WHERE contentid='$contentid' AND component='$component' AND published='1' ORDER BY id ASC", '', 100);
		$data = $database->loadAssocList();

		if ($data != null) {
			foreach($data as $item) {
				$rss_item = new FeedItem();
				$rss_item->author = $item['name'];
				if(strcmp($item['title'],'')) {
					$rss_item->title = $item['title'];
				}else {
					$rss_item->title = 'no comment title';
				}
				$rss_item->link = $this->linkToContent($contentid, $item['id']);
				$rss_item->description = $item['comment'];
				$rss_item->date = date('r', $item['rss_date']);
				$rss->addItem($rss_item);
			}
		}
		$rss->cssStyleSheet = "http://www.w3.org/2000/08/w3c-synd/style.css";
		$rss->saveFeed("RSS2.0", $folderPath .DS.$this->feedName.$contentid.'.xml');
	}

	/*
     * construct the link to the content item
     * (and also direct to the comment if commentId set)
	*/
	public function linkToContent($contentId, $commentId='') {

		$application =& JFactory::getApplication();
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php');

		$data = $this->getData($contentId);
		$this->alias = $data->alias;
		$this->catid = $data->catid;
		if ($commentId) {
			$commentId = '#josc' . $commentId;
		} else {
			$commentId = '';
		}

		if(!$application->isAdmin()) {
			$url = (K2HelperRoute::getItemRoute($contentId.':'.urlencode($this->alias),$this->catid.':'.urlencode($data->catalias))) ;
		} else {
			$url = JURI::root() . K2HelperRoute::getItemRoute($contentId.':'.urlencode($this->alias),$this->catid.':'.urlencode($data->catalias)) ;
		}

		if(JRequest::getVar('josctask') == 'rss') {
			$url = JURI::root() . $url;
		}
		$url = urldecode(JRoute::_($url) . $commentId);
		return $url;
	}

	private function getData($id) {
		$database = JFactory::getDBO();
		$query = "SELECT a.alias, a.catid, c.alias as catalias FROM #__k2_items AS a "
				. ' LEFT JOIN #__k2_categories as c ON a.catid = c.id'
				. ' WHERE a.id='.$id;
		$database->setQuery($query);
		$result = $database->loadObject();
		return $result;
	}

	/*
     * clean the cache of the component when comments are inserted/modified...
     * (if cache is active)
	*/
	public function cleanComponentCache() {
		$cache =& JFactory::getCache('com_k2');
		$cache->clean('com_k2');
	}

	/*----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   A D M I N   P A R T
	 *----------------------------------------------------------------------------------
	*/

	/*
     * section option list used to display the include/exclude section list in setting
     * must return an array of objects (id,title)
	*/
	public function getSectionsIdOption() {
		$sectoptions = array();
		return $sectoptions;
	}

	/*
     * categories option list used to display the include/exclude category list in setting
     * must return an array of objects (id,title)
	*/
	public function getCategoriesIdOption() {
		$database = JFactory::getDBO();

		$catoptions = array();
		$query 	= "SELECT id, name AS title"
				. "\n FROM #__k2_categories"
				. "\n WHERE published = 1"
				. "\n AND access >= 0"
				. "\n ORDER BY id"
		;
		$database->setQuery( $query );
		$catoptions = $database->loadObjectList();

		return $catoptions;
	}

	/*
	 * content items list (or single) for new and edit comment
	 * must return an array of objects (id,title)
	*/
	public function getObjectIdOption($id=0, $select=true) {
		$database = JFactory::getDBO();

		$content = array();
		$query 	= "SELECT id AS id, title AS title"
				. "\n FROM #__k2_items "
				. ($id ? "\n WHERE id = $id":"")
				. "\n ORDER BY id"
		;
		$database->setQuery( $query );
		$content = $database->loadObjectList();
		if (!$id && $select && count($content)>0) {
			array_unshift( $content, mosHTML::makeOption( '0', '-- Select Content Item --', 'id', 'title' ) );
		}

		return $content;
	}
	public function getViewTitleField() {
		$title = 'title';
		return $title;
	}
	public function getViewJoinQuery($alias, $contentid) {
		$leftjoin	= "\n LEFT JOIN #__k2_items  AS $alias ON $alias.id = $contentid ";
		return $leftjoin;
	}

	/*----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   MOD_COMMENTS   M O D U L E
	 *----------------------------------------------------------------------------------
	*/
	public function mod_commentsGetMostCommentedQuery($secids, $catids, $maxlines) {
		$database =& JFactory::getDBO();

		$component = $this->_component;

		$limit = $maxlines>=0 ? " limit $maxlines " : "";

		$where = array();
		if ($catids)
			$where[] = "cat.id IN ($catids)";

		$query 	=  	"SELECT COUNT(c.id) AS countid, ct.id, ct.title AS title "
				.	"\n FROM `#__comment`    AS c "
				.  	"\n INNER JOIN `#__k2_items`  AS ct  ON ct.id = c.contentid "
				.  	"\n INNER JOIN `#__k2_categories` AS cat ON cat.id = ct.catid "
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

	public function mod_commentsGetOthersQuery($secids, $catids, $maxlines, $orderby) {
		$database = JFactory::getDBO();

		$component = $this->_component;

		$limit = $maxlines>=0 ? " limit $maxlines " : "";

		$where = array();
		if ($catids)
			$where[] = "cat.id IN ($catids)";

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
		$query 	=  "SELECT c.*, ct.title AS ctitle $mostrated "
				.  "\n FROM `#__comment`    AS c "
				.  "\n INNER JOIN `#__k2_items`    AS ct  ON ct.id = c.contentid "
				.  "\n INNER JOIN `#__k2_categories` AS cat ON cat.id = ct.catid "
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