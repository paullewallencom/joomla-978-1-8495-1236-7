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

/*
 * JOSC_com_virtuemart plugin to enable !joomlacomment 4.0 beta2 to work with virtuemart
 * 
*/

class JOSC_com_virtuemart extends JOSC_component {
	private $vmp = 'vm'; /* change the value to your VM's database prefix */
	private $feedName = 'virtuemartRSS';
	private $component = 'com_virtuemart';
	
	public function __construct($component,&$row,&$list) {
		$this->_id	= isset($row->product_id) ? $row->product_id : 0; /* document id */

		$this->setRowDatas($row); /* get specific properties */

		parent::__construct($component,0,$this->_id);
	}

	/*
	 * Set specific properties
	 * will be called also in admin.html.php manage comments during row loop
	*/
	function setRowDatas(&$row) {
		$this->flypage = $row->flypage;
		$this->category_id = $row->category_id;
	}

	/*
	 * Virtuemart's flypage is necessary to build a valid url
	 * (we don't have the flypage in the backend)
	*/

	private function getVMData($product_id) {
		$database =& JFactory::getDBO();

		$query = 'SELECT c.' . $database->nameQuote('category_flypage') . ', c.' . $database->nameQuote('category_id')
				. ' FROM ' . $database->nameQuote('#__'.$this->vmp.'_category') . ' AS c'
				. ' LEFT JOIN ' . $database->nameQuote('#__'.$this->vmp.'_product_category_xref') . ' AS p'
				. ' ON p.' . $database->nameQuote('category_id') . ' = c.' . $database->nameQuote('category_id')
				. ' WHERE p.' . $database->nameQuote('product_id') . ' = ' . $database->Quote($product_id);
		$database->setQuery( $query, '' , 1 );
		$flypage = $database->loadObject();
		return $flypage;
	}

	/*
     * returns page id - needed for blocking the form if necessary
     *
	*/
	public function getPageId() {
		return $this->_id;
	}

	/*
	 * this function generated the rss feed
	*/
	public function createFeed() {
		require_once(JPATH_SITE."/includes/feedcreator.class.php");
		$contentid = JRequest::getInt('contentid', '', 'GET');
		$component = 'com_virtuemart';

		$database = JFactory::getDBO();
		$database->setQuery('SELECT product_name as title, product_s_desc FROM ' . $database->nameQuote('#__'. $this->vmp . '_product') . ' WHERE product_id=' . $contentid );
		$content = $database->loadObject();
		$rss = new UniversalFeedCreator();
		$folderPath = JPATH_SITE . DS .'media'.DS. 'com_comment'.DS.'rss'.DS .$this->component;
		$folderExists = JFolder::exists($folderPath);
		if(!$folderExists) {
			JFolder::create($folderPath);
		}
		$rss->useCached("RSS2.0", $folderPath .DS.$this->feedName.$contentid.'.xml');

		$rss->title = $content->title. ' - comments' ;
		$rss->description = $content->product_s_desc . ' - comments';
		$rss->link = JURI :: base();
		$database->setQuery("SELECT *,UNIX_TIMESTAMP( date ) AS rss_date FROM #__comment WHERE contentid='$contentid' AND component='$component' AND published='1' ORDER BY id DESC", '', 100);
		$data = $database->loadAssocList();
		if ($data != null) {
			foreach($data as $item) {
				$rss_item = new FeedItem();
				$rss_item->author = $item['name'];
				if(strcmp($item['title'],'')) {
					$rss_item->title = stripslashes($item['title']);
				}
				else {
					$rss_item->title = '[No Title]';
				}
				$rss_item->link = $this->linkToContent($contentid, $item['id']);
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
	public function checkSectionCategory(&$row, $include, $sections=array(), $catids=array(), $contentids=array()) {
		/* doc id excluded ? */

		if (in_array((($row->product_id == 0) ? -1 : $row->product_id), $contentids))
			return false;

		/* category included or excluded ? */
		$result = in_array((($row->category_id == 0) ? -1 : $row->category_id), $catids);
		if (($include && !$result) || (!$include && $result))
			return false; /* include and not found OR exclude and found */

		return true;
	}

	/*
     * Condition to active or not the display of the post and input form
     * If the return is false, show readon will be executed.
	*/
	public function checkVisual($contentId=0) {
		$option = JRequest::getVar('option');
		$page = JRequest::getVar('page');

		return  (		$option == 'com_virtuemart'
						&& 	$page == 'shop.product_details'
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
	public function linkToContent($contentId, $commentId='') {
		$appl = JFactory::getApplication();
		$menuid = $this->getItemid();

		/* flypage and cat exist only in frontend, but not in backend and notification mails*/
		if(!($this->flypage) || !($this->category_id)) {
			$data = $this->getVMData($contentId);
			$this->flypage = $data->category_flypage;
			$this->category_id = $data->category_id;
		}

		$url = JRoute::_('index.php?option=com_virtuemart&page=shop.product_details&flypage='
				.$this->flypage.'&product_id='.$contentId . '&category_id=' . $this->category_id
				.'&Itemid=' . $menuid . '#josc'.$commentId);

		/* for backend urls */
		if ($appl->isAdmin()) {
			$url = JURI::root().$url;
		}
		/* for notification email links */
		if (substr(ltrim($url),0,7)!='http://') {
			$uri   =& JURI::getInstance();
			$base  = $uri->toString( array('scheme', 'host', 'port'));
			$url = $base.$url;
		}

		return $url;
	}

	/*
	 *  getItemid
	*/
	function getItemid( $component='com_virtuemart') {
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

	/*
     * clean the cache of the component when comments are inserted/modified...
     * (if cache is active)
	*/
	function cleanComponentCache() {
		$cache =& JFactory::getCache('com_virtuemart');
		$cache->clean('com_virtuemart');
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
		$query 	= 'SELECT ' . $database->nameQuote('category_id') . ' AS id , '
				. $database->nameQuote('category_name') . 'AS title'
				. ' FROM ' . $database->nameQuote('#__'.$this->vmp.'_category')
				. ' WHERE ' . $database->nameQuote('category_publish') . ' = ' . $database->Quote('Y')
				. ' ORDER BY ' . $database->nameQuote('list_order');
		$database->setQuery( $query );
		$catoptions = $database->loadObjectList();

		return $catoptions;
	}

	/*
	 * content items list (or single) for new and edit comment
	 * must return an array of objects (id,title)
	*/
	function getObjectIdOption($id=0, $select=true) {
		$database = JFactory::getDBO();

		$content = array();
		$query 	= "SELECT product_id AS id, product_name AS title"
				. ' FROM ' . $database->nameQuote('#__'.$this->vmp.'_product')
				. ($id ? "\n WHERE product_id = $id":"")
				. ' ORDER BY ' . $database->nameQuote('product_id');
		$database->setQuery( $query );
		$content = $database->loadObjectList();

		return $content;
	}
	function getViewTitleField() {
		$title = 'product_name';
		return($title);
	}
	function getViewJoinQuery($alias, $contentid) {
		$leftjoin	= ' LEFT JOIN #__'.$this->vmp.'_product  AS '."$alias ON $alias.product_id = $contentid ";
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
			$where[] = "cat.category_id IN ($catids)";

		/*
	     * Count comment id group by contentid
   	 	 * TODO: restrict according to user rights, dates and category/secitons published and dates...
		*/
		$query 	=  	"SELECT COUNT(c.id) AS countid, ct.product_id as id, ct.product_name AS title "
				.	"\n FROM `#__comment`    AS c "
				.  	"\n INNER JOIN `#__vm_product`  AS ct  ON ct.product_id = c.contentid "
				.  	" LEFT JOIN `#__vm_product_category_xref` AS cat ON cat.product_id = ct.product_id "
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
		if ($secids)
			$where[] = "cat.category_id IN ($secids)";

		if ($orderby=='mostrated') {
			$mostrated =  ", (c.voting_yes-c.voting_no)/2 AS mostrated";
			$where[]  = "(c.voting_yes > 0 OR c.voting_no > 0)";
		}
		else {
			$mostrated = "";
			$orderby = "c.$orderby";
		}

		/*
   	 	 * TODO: restrict according to user rights, dates and category/secitons published and dates...
		*/
		$query 	=  "SELECT c.*, ct.product_name AS ctitle $mostrated "
				.  "\n FROM `#__comment`    AS c "
				.  	"\n INNER JOIN `#__vm_product`  AS ct  ON ct.product_id = c.contentid "
				.  	" LEFT JOIN `#__vm_product_category_xref` AS cat ON cat.product_id = ct.product_id "
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