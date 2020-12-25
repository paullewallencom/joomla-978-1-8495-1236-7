<?php
defined('_JEXEC')  or die('Restricted access');
/***************************************************************
*  Copyright notice
*
*  THIS IS A COMMERCIAL PLUGIN! Please make sure that you have
* purchased it from compojoom.com
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

class JOSC_com_hwdvideoshareGroup extends JOSC_component {

	private $feedName = 'hwdvideoshareGroup';
	private $component = 'com_hwdvideosharegroup';
    public function __construct($component,&$row,&$list) {

		$this->id	= isset($row->id) ? $row->id : 0; /* document id */

		parent::__construct($component,0,$this->id);
    }

	/*
	 * Set specific properties
	 * will be called also in admin.html.php manage comments during row loop
	 */
    function setRowDatas(&$row) {
		
    }

    function getPageId() {
		return $this->id;
    }

    function createFeed() {
		require_once(JPATH_SITE."/includes/feedcreator.class.php");
		$contentid = JRequest::getInt('contentid','','GET');
		$component = 'com_hwdvideoshareGroup';

		$database = JFactory::getDBO();
		$database->setQuery("SELECT group_name FROM #__hwdvidsvideos WHERE id='$contentid'");
		$content = $database->loadObject();
		$rss = new UniversalFeedCreator();
		$folderPath = JPATH_SITE . DS .'media'.DS. 'com_comment'.DS.'rss'.DS .$this->component;
		$folderExists = JFolder::exists($folderPath);
		if(!$folderExists) {
			JFolder::create($folderPath);
		}
		$rss->useCached("RSS2.0", $folderPath .DS.$this->feedName.$contentid.'.xml');

		$rss->title = $content->group_name. ' - comments' ;
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
			$rss_item->link = $this->linkToContent($contentid,$item['id']);
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
	 * 
	 */
    function checkSectionCategory(&$row, $include, $sections=array(), $catids=array(), $contentids=array()) {
			/* doc id excluded ? */
		if (in_array((($row->id == 0) ? -1 : $row->id), $contentids))
		return false;

		$groupId = $row->id;
		if($groupId) {
			$result = in_array($groupId, $catids);
		}

		if (($include && !$result) || (!$include && $result))
		{
			return false; /* include and not found OR exclude and found */
		}
		return true;
    }

    /*
     * Condition to active or not the display of the post and input form
     * If the return is false, show readon will be executed.
     */
    public function checkVisual($contentId=0) {
		$option = JRequest::getVar('option');

		$task = JRequest::getVar( 'task' );

		return ($option == 'com_hwdvideoshare' && $task == 'viewgroup');
    }

	/*
	 * This function will active or deactivate the show readon display 
	 */	
    public function setShowReadon( &$row, &$params, &$config ) {
		$show_readon 	= $config->_show_readon;

		return $show_readon;
    }

    /*
     * construct the link to the content item
     * (and also direct to the comment if commentId set)
     */
    public function linkToContent($contentId, $commentId='', $joscclean=false, $admin=false) {
		$appl =& JFactory::getApplication();
		$menuid = $this->getItemid();

		$url = JRoute::_('index.php?option=com_hwdvideoshare&task=viewgroup'.
					( $menuid ? "&Itemid=$menuid" : "" ) .'&group_id='.
					$contentId.'#josc'.$commentId);
	
		if ($appl->isAdmin()) {
			$url = JURI::root().$url;
		}

		/* for notification email links and not root directory - ! */
		if (substr(ltrim($url),0,7)!='http://') {
			$uri   =& JURI::getInstance();
			$base  = $uri->toString( array('scheme', 'host', 'port'));
			$url = $base.$url;
		}
		return $url;
    }



    /*
     * clean the cache of the component when comments are inserted/modified...
     * (if cache is active) 
     */
    public function cleanComponentCache() {
		$cache =& JFactory::getCache('com_hwdvideoshare');
		$cache->clean('com_hwdvideoshare');
    }

	/*
	 *  getItemid
	 */
    function getItemid( $component='com_hwdvideoshare') {
		static $ids;
		if( !isset($ids) ) {
			$ids = array();
		}
		if( !isset($ids[$component]) ) {
			$database =& JFactory::getDBO();
			$query = "SELECT id FROM #__menu"
			."\n WHERE link LIKE '%option=$component%'"
			."\n AND type = 'component'"
			."\n AND published = 1 LIMIT 1";
			$database->setQuery($query);
			$ids[$component] = $database->loadResult();
		}
		return $ids[$component];
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
		$database =& JFactory::getDBO();

		$sectoptions = array();

		return $sectoptions;
    }

    /*
     * categories option list used to display the include/exclude category list in setting 
     * must return an array of objects (id,title)
     */
    public function getCategoriesIdOption() {
		$database =& JFactory::getDBO();

		$catoptions = array();
		$query 	= "SELECT id, group_name as title"
			. "\n FROM #__hwdvidsgroups"
			. "\n WHERE published = 1"
			. "\n ORDER BY ordering"
		;
		$database->setQuery( $query );
		$catoptions = $database->loadObjectList();

		return $catoptions;
    }

	/*
	 * document list (or single) for new and edit comment
	 * must return an array of objects (id,title)
	 */
    public function getObjectIdOption($id=0, $select=true) {
		$database =& JFactory::getDBO();

		$content = array();
		$query 	= "SELECT id, group_name as title"
			. "\n FROM #__hwdvidsgroups "
			. "  WHERE published=1"
			. ($id ? "\n AND id = $id":"")
		;
		$database->setQuery( $query );
		
		$content = $database->loadObjectList();
		if (!$id && $select && count($content)>0) {
			array_unshift( $content, JHTML::_('select.option', '0', '-- Select Document --', 'id', 'title' ) );
		}
		return $content;
    }

    public function getViewTitleField() {
		$title = 'group_name';
		return($title);
    }
    public function getViewJoinQuery($alias, $contentid) {
		$leftjoin	= "\n LEFT JOIN #__hwdvidsgroups  AS $alias ON $alias.id = $contentid ";
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
			$where[] = "ct.category_id IN ($catids)";

			/*
			 * Count comment id group by contentid
			 * TODO: restrict according to user rights, dates and category/secitons published and dates...
			 */
		$query 	=  	"SELECT COUNT(c.id) AS countid, ct.id, ct.group_name AS title "
			.	"\n FROM `#__comment`    AS c "
			.  "\n INNER JOIN `#__hwdvidsgroups`    AS ct  ON ct.id = c.contentid "
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

		$database =& JFactory::getDBO();

		$component = $this->_component;

		$limit = $maxlines>=0 ? " limit $maxlines " : "";

		$where = array();
		if ($catids)
			$where[] = "ct.category_id IN ($catids)";

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
		$query 	=  "SELECT c.*, ct.group_name AS ctitle $mostrated "
			.  "\n FROM `#__comment`    AS c "
			.  "\n INNER JOIN `#__hwdvidsgroups`    AS ct  ON ct.id = c.contentid "
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