<?php
/*
 * Copyright Copyright (C) 2009 Compojoom.com . All rights reserved.
 * License http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * Compojoom Comment is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Compojoom Comment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
*/
defined('_JEXEC') or die('Restricted access');

class JOSC_com_myblog extends JOSC_component {

	private $feedName = 'myblogRSS';
	private $component = 'com_myblog';

	public function __construct($component,&$row,&$list) {
		$appl =& JFactory::getApplication();
		$id	= isset($row->id) ? $row->id : 0; /* document id */
		$this->id = $id;
		$this->_component = 'com_myblog';

		if(!$appl->isAdmin()) {
			JRequest::setVar( 'option' , 'com_myblog' , 'GET' );
		}

		$this->setRowDatas($row); /* get specific properties */

		parent::__construct($component,0,$id);
	}

	public function __desctruct() {
//	We need to return the option to com_content in order for myblog to function
//	properly
		$appl =& JFactory::getApplication();
		if(!$appl->isAdmin()) {
			JRequest::setVar( 'option' , 'com_content' , 'GET' );
		}
	}

	/*
	 * Set specific properties
	 * will be called also in admin.html.php manage comments during row loop
	*/
	public function setRowDatas(&$row) {

	}

	public function getPageId() {
		return $this->id;
	}

	public function createFeed() {
		require_once(JPATH_SITE."/includes/feedcreator.class.php");
		$contentid = JRequest::getInt('contentid','','GET');
		$component = 'com_myblog';

		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__content WHERE id='$contentid'");
		$content = $database->loadObject();
		$rss = new UniversalFeedCreator();
		$folderPath = JPATH_SITE . DS .'media'.DS. 'com_comment'.DS.'rss'.DS .$this->component;
		$folderExists = JFolder::exists($folderPath);
		if(!$folderExists) {
			JFolder::create($folderPath);
		}
		$rss->useCached("RSS2.0", $folderPath .DS.$this->feedName.$contentid.'.xml');
		$rss->title = $content->title. ' - comments' ;
		$rss->description = $content->title_alias;
		$rss->link = JURI :: base();
		$database->setQuery("SELECT *,UNIX_TIMESTAMP( date ) AS rss_date FROM #__comment WHERE contentid='$contentid' AND component='$component' AND published='1' ORDER BY id DESC", '', 100);
		$data = $database->loadAssocList();
		if ($data != null) {
			foreach($data as $item) {
				$rss_item = new FeedItem();
				$rss_item->author = $item['name'];
				if(strcmp($item['title'],'')) {
					$rss_item->title = stripslashes($item['title']);
				}else {
					$rss_item->title = '[No Title]';
				}
				$rss_item->link = $this->linkToContent($contentid, $item['id'], '', true);
				$rss_item->description = stripslashes($item['comment']);
				$rss_item->date = date('r', $item['rss_date']);
				$rss->addItem($rss_item);
			}
		}
		$rss->cssStyleSheet = "http://www.w3.org/2000/08/w3c-synd/style.css";
		$rss->saveFeed("RSS2.0", $folderPath .DS.$this->feedName.$contentid.'.xml');
	}
	/*
	 * This function is executed to check
	 * if section/category of the row are authorized or not (exclude/include from the setting)
	 * return : true for authorized / false for excluded
	*/
	public function checkSectionCategory(&$row, $include, $sections=array(), $catids=array(), $contentitems=array()) {
		if(is_object($row)) {
			/* content ids */
			if (count($contentitems)>0) {
				$result = in_array((($row->id == 0) ? -1 : $row->id), $contentitems);
				if ($include && $result) 	return true; 	/* include and selected */
				if (!$include && $result) 	return false; 	/* exclude and selected */
			}

			/* sections */
			$result = in_array((($row->sectionid == 0) ? -1 : $row->sectionid), $sections);
			if ($include && $result) 	return true; 	/* include and selected */
			if (!$include && $result) 	return false; 	/* exclude and selected */

			/* categories */
			$result = in_array((($row->catid == 0) ? -1 : $row->catid) , $catids);
			if ($include && $result) 	return true; 	/* include and selected */
			if (!$include && $result) 	return false; 	/* exclude and selected */

			if ($include) 	return false; /* was not included */
			if (!$include)	return true; /* was not excluded */
		}

		return true;
	}

	/*
     * Condition to active or not the display of the post and input form
     * If the return is false, show readon will be executed.
	*/
	public function checkVisual($contentId=0) {
		$mainframe = JFactory::getApplication();
		$option = $mainframe->scope;
		$show = JRequest::getVar('show' );

		return  ( $option == 'com_myblog'
						&&	$show
		);
	}

	/*
	 * This function will active or deactivate the show readon display
	*/
	public  function setShowReadon( &$row, &$params, &$config ) {
		$show_readon 	= $config->_show_readon;

		return $show_readon;
	}

	/*
     * construct the link to the content item
     * (and also direct to the comment if commentId set)
	*/
	public function linkToContent($contentId, $commentId='', $joscclean=false, $rss = false) {
		$appl =& JFactory::getApplication();

		$contentId = $this->getPermalink($contentId);

		$add =  ( $commentId ? "#josc$commentId" : "" ) ;
		$itemId = '&Itemid=' . $this->getMyBlogItemId();
		if (!$appl->isAdmin() && !$rss) {
			$url = JRoute::_('index.php?option=com_myblog&show=' . $contentId . $itemId . $add);
		} else {
			$url = JRoute::_(JURI::root().'index.php?option=com_myblog&show=' . $contentId . $itemId . $add);
		}
		return $url;
	}

	private function getPermalink($contentId) {
		$db = JFactory::getDBO();
		$id			= $contentId;

		$query	= 'SELECT ' . $db->nameQuote( 'permalink' ) . ' FROM ' . $db->nameQuote( '#__myblog_permalinks' )
				. 'WHERE ' . $db->nameQuote('contentid') . '=' . $db->Quote( $contentId );
		$db->setQuery( $query );

		$link	= $db->loadResult();

		return $link;
	}

	private function getMyBlogItemId() {
		static $mbItemid = -1;

		if($mbItemid == -1) {
			global $Itemid;
			$db			=& JFactory::getDBO();
			$mbItemid	= $Itemid;
			$query = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__menu')
					. ' WHERE ' . $db->nameQuote('link') . ' LIKE ' . $db->Quote('%option=com_myblog%')
					. ' AND ' . $db->nameQuote('published') . '=' . $db->Quote('1')
					. ' AND ' . $db->nameQuote('id') . '=' . $db->Quote($Itemid);
			$db->setQuery($query);
			if(!$db->loadResult()) {
				$query = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__menu')
						. ' WHERE ' .$db->nameQuote('type') . '='  .$db->Quote('component') . ' AND ' . $db->nameQuote('link')
						. ' = ' . $db->Quote('index.php?option=com_myblog') . ' AND ' . $db->nameQuote('published') . '=' .$db->Quote('1');
				$db->setQuery($query);
				$mbItemid = $db->loadResult();
			}
		}

		return $mbItemid;
	}

	/*
     * clean the cache of the component when comments are inserted/modified...
     * (if cache is active)
	*/
	public function cleanComponentCache() {
		$cache =& JFactory::getCache('com_myblog');
		$cache->clean('com_myblog');
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
		$database = JFactory::getDBO();

		$sectoptions = array();
		$query 	= "SELECT s.id, s.title"
				. "\n FROM #__sections AS s "
				. "\n WHERE s.published = 1"
				. "\n AND s.scope = 'content'"
				//					. "\n AND access >= 0"
				. "\n ORDER BY s.ordering"
		;
		$database->setQuery( $query );
		$sectoptions = $database->loadObjectList();
		// add "Static Content" value
		array_unshift( $sectoptions, JHTML::_('select.option', '-1', 'Static Content', 'id', 'title' ) );

		return $sectoptions;
	}
	/*
     * categories option list used to display the include/exclude category list in setting
     * must return an array of objects (id,title)
	*/
	public function getCategoriesIdOption() {
		$database = JFactory::getDBO();

		$catoptions = array();
		$query 	= "SELECT c.id, CONCAT( s.title, ' | ', c.title ) AS title "
				. "\n FROM #__sections AS s "
				. "\n INNER JOIN #__categories AS c ON s.id = c.section "
				. "\n WHERE s.published = 1 "
				. "\n   AND c.published = 1 "
				. "\n   AND s.scope = 'content' "
				//					. "\n   AND access >= 0"
				. "\n ORDER BY s.ordering, c.ordering "
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
				. "\n FROM #__content "
				. ($id ? "\n WHERE id = $id":"")
				//					. "\n AND access >= 0"
				. "\n ORDER BY ordering"
		;
		$database->setQuery( $query );
		$content = $database->loadObjectList();
		if (!$id && $select && count($content)>0) {
			array_unshift( $content, JHTML::_('select.option', '0', '-- Select Content Item --', 'id', 'title' ) );
		}

		return $content;
	}

	/*----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   MOD_COMMENTS   M O D U L E
	 *----------------------------------------------------------------------------------
	*/
	public function getViewTitleField() {
		$title = 'title';
		return($title);
	}
	public function getViewJoinQuery($alias, $contentid) {
		$leftjoin	= "\n LEFT JOIN #__content  AS $alias ON $alias.id = $contentid ";
		return $leftjoin;
	}

	public function mod_commentsGetMostCommentedQuery($secids, $catids, $maxlines) {
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$gid = $user->gid;
		$component = $this->_component;

		$limit = $maxlines>=0 ? " limit $maxlines " : "";

		$where = array();
		if ($catids)
			$where[] = "cat.id IN ($catids)";
		if ($secids)
			$where[] = "cat.section IN ($secids)";

		/*
	     * Count comment id group by contentid
		 * TODO: restrict according content item user rights, dates and category/secitons published and dates...
		*/
		$query     =      "SELECT COUNT(c.id) AS countid, ct.id, ct.title "
				.    "\n FROM `#__comment`    AS c "
				.      "\n INNER JOIN `#__content`    AS ct  ON ct.id = c.contentid "
				.      "\n INNER JOIN `#__categories` AS cat ON cat.id = ct.catid "
				.   "\n INNER JOIN `#__sections`   AS sec ON sec.id = cat.section "
				.      "\n WHERE c.published='1' "
				.    "\n   AND c.component='$component' "
				.    "\n   AND sec.access <= " . (int) $gid
				.      "\n   AND sec.published='1' "
				.    "\n   AND cat.access <= " . (int) $gid
				.      "\n   AND cat.published='1' "
				.   "\n   AND ct.access <= " . (int) $gid
				.      "\n ". (count($where) ? (" AND ".implode(" AND ", $where)) : "")
				.    "\n GROUP BY c.contentid"
				.    "\n ORDER BY countid DESC"
				.    "\n $limit"
		;

		$database->SetQuery($query);
		$rows = $database->loadAssocList();

		return $rows;
	}

	public function mod_commentsGetOthersQuery($secids, $catids, $maxlines, $orderby) {
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$gid = $user->gid;
		$component = $this->_component;

		$limit = $maxlines>=0 ? " limit $maxlines " : "";

		$where = array();
		if ($catids)
			$where[] = "cat.id IN ($catids)";
		if ($secids)
			$where[] = "cat.section IN ($secids)";

		if ($orderby=='mostrated') {
			$mostrated =  ", (c.voting_yes-c.voting_no)/2 AS mostrated";
			$where[]  = "(c.voting_yes > 0 OR c.voting_no > 0)";
		} else {
			$mostrated = "";
			$orderby = "c.$orderby";
		}

		$query 	=  "SELECT c.*, ct.title AS ctitle $mostrated "
				.  "\n FROM `#__comment`    AS c "
				.  "\n INNER JOIN `#__content`    AS ct  ON ct.id = c.contentid "
				.  "\n INNER JOIN `#__categories` AS cat ON cat.id = ct.catid "
				.  "\n INNER JOIN `#__sections`   AS sec ON sec.id = cat.section "
				.  "\n WHERE c.published='1' "
				.	"\n   AND c.component='$component' "
				.	"\n   AND sec.access <= " . (int) $gid
				.  	"\n   AND sec.published='1' "
				.	"\n   AND cat.access <= " . (int) $gid
				.  	"\n   AND cat.published='1' "
				.   "\n   AND ct.access <= " . (int) $gid
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