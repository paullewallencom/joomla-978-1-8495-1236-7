<?php
/***************************************************************
*  $Revision$
*
*  Copyright notice
*
*  Copyright 2010 Daniel Dimitrov. (http://compojoom.com)
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

class JOSC_com_sobi2 extends JOSC_component {
	private $feedName = 'sobi2RSS';
	private $component = 'com_sobi2';
	
	public function __construct($component, &$row, &$list) {
		$this->id = isset($row->id) ? $row->id : 0; /* document id */
		$this->component = $component;
		$this->setRowDatas($row); /* get specific properties */
		parent::__construct($component, 0, $this->id);
	}

	/*
	 * Set specific properties
	 * will be called also in admin.html.php manage comments during row loop
	 */

	public function setRowDatas(&$row) {
		/* for optimization reason, do not save row. save just needed parameters */

		//$this->_specific_data	= isset($row->specific) ? $row->specific : '';
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
		/*
		 * this is pretty complex since a single item can be ordered to multiple
		 * categories .
		 * If we have a catid in the url we will use this, if we don't then we
		 * will have to check the whole array for cat values that are forbidden
		 */
		$catid = JRequest::getCmd('catid');
		if ($catid) {
			$result = in_array($catid, $catids);
		} else {
			foreach ($row->myCategories as $key => $value) {
				if (in_array($key, $catids)) {
					$result = true;
				}
			}
		}

		if (($include && !$result) || (!$include && $result)) {
			return false; /* include and not found OR exclude and found */
		}
		return true;
	}

	public function getPageId() {
		return $this->id;
	}

	public function createFeed() {
		require_once(JPATH_SITE . "/includes/feedcreator.class.php");
		$contentid = JRequest::getInt('contentid', '', 'GET');
		$component = 'com_sobi2';

		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__sobi2_item WHERE itemid='$contentid'");
		$content = $database->loadObject();

		$folderPath = JPATH_SITE . DS .'media'.DS. 'com_comment'.DS.'rss'.DS .$this->component;
		$folderExists = JFolder::exists($folderPath);
		if(!$folderExists) {
			JFolder::create($folderPath);
		}

		$rss = new UniversalFeedCreator();
		$rss->useCached("RSS2.0", $folderPath . DS . $this->feedName . $contentid . '.xml');
		$rss->title = $content->title . ' - comments';
		$rss->link = JURI :: base();
		$database->setQuery("SELECT *,UNIX_TIMESTAMP( date ) AS rss_date FROM #__comment WHERE contentid='$contentid' AND component='$component' AND published='1' ORDER BY id ASC", '', 100);
		$data = $database->loadAssocList();
		if ($data != null) {
			foreach ($data as $item) {
				$rss_item = new FeedItem();
				if (strcmp($item['title'], '')) {
					$rss_item->title = $item['title'];
				} else {
					$rss_item->title = 'no comment title';
				}

				$rss_item->author = $item['name'];
				$rss_item->link = JRoute::_(JURI::base() . "index.php?option=com_sobi2&sobi2Task=sobi2Details&sobi2Id=$contentid#josc" . $item['id']);
				$rss_item->description = $item['comment'];
				$rss_item->date = date('r', $item['rss_date']);
				$rss->addItem($rss_item);
			}
		}
		$rss->cssStyleSheet = "http://www.w3.org/2000/08/w3c-synd/style.css";
		$rss->saveFeed("RSS2.0", $folderPath . DS . $this->feedName . $contentid . '.xml');
	}

	/*
	 * Condition to active or not the display of the post and input form
	 * If the return is false, show readon will be executed.
	 */

	public function checkVisual($contentId=0) {
		$option = JRequest::getCMD('option');
		$view = JRequest::getVar('sobi2Task');

		return ( $option == 'com_sobi2'
		&& $view == 'sobi2Details'
		);
	}

	/*
	 * This function will active or deactivate the show readon display
	 */

	public function setShowReadon(&$row, &$params, &$config) {
		$show_readon = $config->_show_readon;

		return $show_readon;
	}

	/*
	 * construct the link to the content item
	 * (and also direct to the comment if commentId set)
	 */

	public function linkToContent($contentId, $commentId='', $joscclean=false, $admin=false) {
		$appl = & JFactory::getApplication();

		$add = ( $commentId ? "#josc$commentId" : "" );

		if (!$appl->isAdmin()) {
			$url = JRoute::_('index.php?option=com_sobi2&sobi2Task=sobi2Details&sobi2Id=' . $contentId . $add);
		} else {
			$url = JRoute::_('index.php?option=com_sobi2&sobi2Task=sobi2Details&sobi2Id=' . $contentId . $add);
			$url = JURI::root() . $url;
		}

		/* for notification email links and not root directory - ! */
		if (substr(ltrim($url),0,7)!='http://') {
			$uri   =& JURI::getInstance();
			$base  = $uri->toString( array('scheme', 'host', 'port'));
			$url = $base.$url;
		}

		return ($url);
	}

	/*
	 * clean the cache of the component when comments are inserted/modified...
	 * (if cache is active)
	 */

	public function cleanComponentCache() {
		$cache = & JFactory::getCache('com_sobi2');
		$cache->clean('com_sobi2');
	}

	/* ----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   A D M I N   P A R T
	 * ----------------------------------------------------------------------------------
	 */

	/*
	 * section option list used to display the include/exclude section list in setting
	 * must return an array of objects (id,title)
	 */

	public function getSectionsIdOption() {
		$database = & JFactory::getDBO();

		$sectoptions = array();

		return $sectoptions;
	}

	/*
	 * categories option list used to display the include/exclude category list in setting
	 * must return an array of objects (id,title)
	 */

	public function getCategoriesIdOption() {
		$database = & JFactory::getDBO();

		$catoptions = array();
		$query = "SELECT catid as id, name as title"
				. "\n FROM #__sobi2_categories"
				. "\n WHERE published = 1"
				//				. "\n AND access >= 0"
				. "\n ORDER BY ordering"
		;
		$database->setQuery($query);
		$catoptions = $database->loadObjectList();
		return $catoptions;
	}

	/*
	 * document list (or single) for new and edit comment
	 * must return an array of objects (id,title)
	 */

	public function getObjectIdOption($id=0, $select=true) {
		$database = & JFactory::getDBO();

		$content = array();
		$query = "SELECT itemid as id, title"
				. "\n FROM #__sobi2_item "
				. "  WHERE published=1"
				. ($id ? "\n AND itemid = $id" : "")
		;
		$database->setQuery($query);
		$content = $database->loadObjectList();
		if (!$id && $select && count($content) > 0) {
			array_unshift($content, mosHTML::makeOption('0', '-- Select Sobi2 Item --', 'id', 'title'));
		}

		return $content;
	}

	public function getViewTitleField() {
		$title = 'title';
		return($title);
	}

	public function getViewJoinQuery($alias, $contentid) {
		$leftjoin = "\n LEFT JOIN #__sobi2_item  AS $alias ON $alias.itemid = $contentid ";
		return $leftjoin;
	}

	/* ----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   MOD_COMMENTS   M O D U L E
	 * ----------------------------------------------------------------------------------
	 */

	public function mod_commentsGetMostCommentedQuery($secids, $catids, $maxlines) {

		$database = & JFactory::getDBO();

		$component = $this->_component;

		$limit = $maxlines >= 0 ? " limit $maxlines " : "";

		$where = array();
		if ($secids)
			$where[] = "cat.id IN ($secids)";

		/*
		 * Count comment id group by contentid
		 * TODO: restrict according to user rights, dates and category/secitons published and dates...
		 */
		$query = "SELECT COUNT(c.id) AS countid, ct.itemid as id, ct.title AS title "
				. "\n FROM `#__comment`    AS c "
				. "\n INNER JOIN `#__sobi2_item`  AS ct  ON ct.itemid = c.contentid "
				. "\n WHERE c.published='1' "
				. "\n   AND c.component='$component' "
				. "\n " . (count($where) ? (" AND " . implode(" AND ", $where)) : "")
				. "\n GROUP BY c.contentid"
				. "\n ORDER BY countid DESC"
				. "\n $limit"
		;
		$database->SetQuery($query);
		$rows = $database->loadAssocList();

		return $rows;
	}

	public function mod_commentsGetOthersQuery($secids, $catids, $maxlines, $orderby) {
		$database = & JFactory::getDBO();
		$component = $this->_component;
		$limit = $maxlines >= 0 ? " limit $maxlines " : "";

		$where = array();
		if ($secids) {
			$where[] = "cat.id IN ($secids)";
		}


		if ($orderby == 'mostrated') {
			$mostrated = ", (c.voting_yes-c.voting_no)/2 AS mostrated";
			$where[] = "(c.voting_yes > 0 OR c.voting_no > 0)";
		} else {
			$mostrated = "";
			$orderby = "c.$orderby";
		}

		/*
		 * TODO: restrict according to user rights, dates and category/secitons published and dates...
		 */
		$query = "SELECT c.*, ct.title AS ctitle $mostrated "
				. "\n FROM `#__comment`    AS c "
				. "\n INNER JOIN `#__sobi2_item`    AS ct  ON ct.itemid = c.contentid "
				. "\n WHERE c.published='1' "
				. "\n  AND c.component='$component' "
				. "\n " . (count($where) ? (" AND " . implode(" AND ", $where)) : "")
				. "\n ORDER BY $orderby DESC "
				. "\n $limit"
		;
		$database->SetQuery($query);
		$rows = $database->loadAssocList();

		return $rows;
	}

}
?>