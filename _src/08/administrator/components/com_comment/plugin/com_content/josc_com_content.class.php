<?php
defined('_JEXEC')  or die('Restricted access');
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

class JOSC_com_content extends JOSC_component {
	/* specific properties of content plugin */
	var $_usecatid=false;
	var	$_route_sectionid=0;
	var	$_route_catid=0;
	var $_route_slug=0;

	public function __construct($component,&$row,&$list) {
		$this->_usecatid = $this->getUseCatid();
		if ($this->_usecatid)
			$sectionid = isset($row->catid) ? $row->catid : $list['sectionid'];
		else
			$sectionid = isset($row->sectionid) ? $row->sectionid : $list['sectionid'];

		$id	= isset($row->id) ? $row->id : 0; /* content item id */
		$this->_id = $id;

		$this->setRowDatas($row); /* get specific properties */
		parent::__construct($component,$sectionid,$id);
	}

	/*
	 * Set specific properties
	 * will be called also in admin.html.php manage comments during row loop
	*/
	public function setRowDatas(&$row) {
		/* when sending e-mails the urls are not sef encoded
		 * this is why we need this query
		*/
		$mainframe = JFactory::getApplication();
		if($mainframe->isAdmin()) {
			if (!$row) {
				$db = JFactory::getDBO();
				$cid = JRequest::getVar('cid', array(), 'post', 'array');
				$where = '';

				if(JRequest::getInt('content_id')) {
					$where = 'WHERE  a.id = ' . JRequest::getInt('content_id');
				}

				if(count($cid)) {
					if(count(($cid))) {
						$query = 'SELECT contentid FROM #__comment WHERE id = ' . $cid[0];
						$db->setQuery($query);
						$contentID = $db->loadObject();
						$where = 'WHERE  a.id =' . $contentID->contentid;
					}
				}
				$query = 'SELECT a.*,
							CASE
								WHEN CHAR_LENGTH(a.alias)
								THEN CONCAT_WS(":", a.id, a.alias)
								ELSE a.id END as slug,
							CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias)
								ELSE cc.id END as catslug FROM #__content AS a
							LEFT JOIN #__categories AS cc ON cc.id = a.catid
							LEFT JOIN #__sections AS s ON s.id = cc.section AND s.scope = "content"
							LEFT JOIN #__users AS u ON u.id = a.created_by
							'. $where;
				$db->setQuery($query);
				$row = $db->loadObject();
			}
		}
		$this->_route_sectionid 	= isset($row->sectionid) ? $row->sectionid : 0;
		$this->_route_catid 		= isset($row->catslug) ? $row->catslug : (isset($row->catid) ? $row->catid : 0) ;
		$this->_route_slug			= isset($row->slug) ? $row->slug  : (isset($row->id) ? $row->id : 0);
		$this->_title           = isset($row->title) ? $row->title : 0;  // Part of Feature #79
	}
	/*
     * returns page id - needed for blocking the form if necessary in comment.class.php
     *
	*/
	public function getPageId() {
		return $this->_id;
	}

	public function createFeed() {
		require_once(JPATH_SITE."/includes/feedcreator.class.php");
		$contentid = JRequest::getInt('contentid','','GET');
		$component = 'com_content';

		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__content WHERE id='$contentid'");
		$content = $database->loadObject();
		$rss = new UniversalFeedCreator();
		$folderPath = JPATH_SITE . DS .'media'.DS. 'com_comment'.DS.'rss'.DS .$component;
		$folderExists = JFolder::exists($folderPath);
		if(!$folderExists) {
			JFolder::create($folderPath);
		}
		$rss->useCached("RSS2.0", $folderPath.DS.'joscfeed'.$contentid.'.xml');
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
				$rss_item->link = JRoute::_(JURI :: base()."index.php?option=com_content&task=view&id=$contentid#josc" . $item['id']);/* TODO : adapt to others component */
				$rss_item->description = stripslashes($item['comment']);
				$rss_item->date = date('r', $item['rss_date']);
				$rss->addItem($rss_item);
			}
		}
		$rss->cssStyleSheet = "http://www.w3.org/2000/08/w3c-synd/style.css";
		$rss->saveFeed("RSS2.0", $folderPath.DS.'joscfeed'.$contentid.'.xml');
	}
	/*
	 * This function is executed to check
	 * if section/category of the row are authorized or not (exclude/include from the setting)
	 * return : true for authorized / false for excluded
	 *
	 * obj :
	 * - _include_sc
	 * - _exclude_sections
	 * - _exclude_categories
	 * - _exclude_contentitems
	 *
	*/
	public function checkSectionCategory(&$row, &$obj) {
		$include		= $obj->_include_sc;
		$sections		= $obj->_exclude_sections;
		$catids			= $obj->_exclude_categories;
		$contentitems 	= $obj->_exclude_contentitems;

		/*
		 * Include =  include only : selected Ids OR selected Sections OR Selected Categories)
		 * Exclude =  exclude selected Ids OR selected Sections OR Selected Categories
		*/

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

	/*
     * Condition to active or not the display of the post.
     * If the return is false, show readon will be executed.
	*/
	public function checkVisual($contentId=0) {
		$option	= JRequest::getVar('option', '');
		$task 	= (JRequest::getVar('view', '') == 'article') ? 'view' : '';


		return  (		$option == 'com_content'
						&& 	$task == 'view'
						&& 	$contentId == intval(JRequest::getInt('id'))
		);
	}

	/*
	 * This function will active or deactivate the show readon display
	*/
	public function setShowReadon( &$row, &$params, &$config ) {
		if ($params!=null) {
			$readmore 		= $params->get( 'show_readmore' );
			$link_titles 	= $params->get( 'link_titles' );
			$intro_only		= true; //$params->get( 'show_intro' );
		}
		$show_readon 	= $config->_show_readon;
		if ($config->_menu_readon 	&& $params!=null && !$readmore && !$link_titles) {
			$show_readon = false;
		}

		if ($config->_intro_only 	&& $params!=null && $readmore && $intro_only
				&& isset($row->readmore) && (bool)$row->readmore ) {
			$show_readon = false;
		}
		/* no link if already readmore link */

		return $show_readon;
	}

	/*
     * construct the link to the content item
     * (and also direct link to the comment if commentId set)
	*/
	public function linkToContent($contentId, $commentId='', $joscclean=false, $admin=false) {
		$appl =& JFactory::getApplication();
		$josctask = JRequest::getCmd('josctask');
		if ($appl->scope == 'mod_comments' || $josctask  == 'ajax_search' || $josctask  == 'ajax_insert'|| $josctask  == 'ajax_voting_yes' || $josctask  == 'ajax_voting_no') {
			$mod_data = $this->getCatSecAliasMod($contentId);
			$this->_route_slug = '';
			$this->_route_catid = $mod_data->catid;
			$this->_route_sectionid = $mod_data->sectionid;
			$contentId = $contentId . ':' . $mod_data->alias;
		}

		$add = ( $commentId ? "#josc$commentId" : "" );

		/* IMPORT CLASS IF NOT EXIST */
		if (!class_exists('JSite')) {
			JLoader::import('includes.application',JPATH_SITE);
		}
		if (!class_exists('ContentHelperRoute')) {
			JLoader::import('components.com_content.helpers.route',JPATH_SITE);
		}

		/* CONSTRUCT URL */
		if (!$appl->isAdmin()) {
			$url = JRoute::_(ContentHelperRoute::getArticleRoute(
					($this->_route_slug ? $this->_route_slug :  $contentId),
					$this->_route_catid, $this->_route_sectionid)
					. $add);
		} else {
			$url = JRoute::_(ContentHelperRoute::getArticleRoute(
					($this->_route_slug ? $contentId : $this->_route_slug ),
					$this->_route_catid, $this->_route_sectionid)
					. $add);
			$url = JURI::root().$url;
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
		$cache =& JFactory::getCache('com_content');
		$cache->clean('com_content');
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
	public function getViewTitleField() {
		$title = 'title';
		return($title);
	}
	public function getViewJoinQuery($alias, $contentid) {
		$leftjoin	= "\n LEFT JOIN #__content  AS $alias ON $alias.id = $contentid ";
		return $leftjoin;
	}

	/*----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   MOD_COMMENTS   M O D U L E
	 *----------------------------------------------------------------------------------
	*/
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
		$query     =      "SELECT COUNT(c.id) AS countid, ct.id AS contentid, ct.title "
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
	/*
     * @var $contentid int
     * @return sectionid, categoryid, alias
	*/
	public function getCatSecAliasMod($contentid) {
		$database =& JFactory::getDBO();
		$query = 'SELECT  c.title, c.sectionid, c.catid, c.alias '
				. ' FROM ' . $database->nameQuote('#__content') . 'AS c'
				. ' WHERE c.id = ' . $database->Quote($contentid)
		;
		$database->setQuery($query, '', 1);
		$row = $database->loadObject();
		return $row;
	}

	/*
 * O P T I O N N A L
 * F O R   E X P E R T   M O D E  O N L Y
 * not yet available.
 * do not report theses functions !
	*/
	/*
     * section option list for the admin setting
	*/
	public function getExpertSectionIdOption($sections, $include) {
		$database = JFactory::getDBO();

		$where = "";
		if ($sections) {
			if ($include)
				$where = "\n AND ".($this->_usecatid ? "c.":"s.")."id IN ($sections) ";
			else
				$where = "\n AND ".($this->_usecatid ? "c.":"s.")."id NOT IN ($sections) ";
		}
		$sectoptions = array();
		if ($this->_usecatid) {
			$query 	= "SELECT c.id, CONCAT( s.title, ' | ', c.title ) AS title "
					. "\n FROM #__sections AS s "
					. "\n INNER JOIN #__categories AS c ON s.id = c.section "
					. "\n WHERE s.published = 1 "
					. "\n   AND c.published = 1 "
					. "\n   AND s.scope = 'content' "
					. $where
					//					. "\n   AND access >= 0"
					. "\n ORDER BY s.ordering, c.ordering "
			;
		} else {
			$query 	= "SELECT s.id, s.title"
					. "\n FROM #__sections AS s "
					. "\n WHERE s.published = 1"
					. "\n AND s.scope = 'content'"
					. $where
					//					. "\n AND access >= 0"
					. "\n ORDER BY s.ordering"
			;
		}
		$database->setQuery( $query );
		$sectoptions = $database->loadObjectList();
		// add "All sections" and "Static Content" value
		if ($sections && !(strpos('-1', $sections)===false))
			$static = true;
		else
			$static = false;

		if (($include && $static) || (!$include && !$static)) {
			array_unshift( $sectoptions, JHTML::_('select.option', '-1', 'Static Content', 'id', 'title' ) );
		}
		array_unshift( $sectoptions, JHTML::_('select.option', '0', '-- All --', 'id', 'title' ) );

		return $sectoptions;
	}

	/*
     * return the id,title section object
	*/
	public function getExpertSectionTitle($sectionid) {
		$database = JFactory::getDBO();

		if ($sectionid==-1)
			return '(-1) Static Content';

		if ($sectionid==0)
			return '(0) All ';

		if ($this->_usecatid) {
			$query 	= "SELECT c.id, CONCAT( s.title, ' | ', c.title ) AS title "
					. "\n FROM #__sections AS s "
					. "\n INNER JOIN #__categories AS c ON s.id = c.section "
					. "\n WHERE c.id=$sectionid"
			;
		} else {
			$query 	= "SELECT id, title"
					. "\n FROM #__sections"
					. "\n WHERE id=$sectionid"
			;
		}
		$database->setQuery( $query );
		$row = $database->loadObjectList();
		return "($sectionid) ".$row[0]->title;
	}

	/*
 * S P E C I F I C   T O   C O N T E N T  P L U G I N
	*/
	public function getUseCatid() {
		global  $_MAMBOTS;
		$database = JFactory::getDBO();
		$mambots = 'plugins';


		$mambot=null;
		// check if param query has previously been processed
		if ( !isset($_MAMBOTS) || !isset($_MAMBOTS->_content_mambot_params['joscomment']) ) {
			// load mambot params info
			$query = "SELECT params"
					. "\n FROM #__$mambots"
					. "\n WHERE element LIKE 'joscomment%'"
					. "\n AND folder = 'content'"
					. "\n AND published = 1 "
			;

			$database->setQuery( $query );
			$mambot = $database->loadObject();
		}
		if (isset($_MAMBOTS)) {
			if (!isset($_MAMBOTS->_content_mambot_params['joscomment'])) {
				// save query to class variable
				$_MAMBOTS->_content_mambot_params['joscomment'] = $mambot;
			}
			// pull query data from class variable
			$mambot = $_MAMBOTS->_content_mambot_params['joscomment'];
		}
		$botParams = new JParameter( $mambot->params );
		return $botParams->get( 'usecatid', 0 );
	}

	public function getBotParam($name, $default=0) {
		global $_MAMBOTS;
		$database = JFactory::getDBO();

		$mambots = 'plugins';

		$mambot= null;
		// check if param query has previously been processed
		if ( !isset($_MAMBOTS) || !isset($_MAMBOTS->_content_mambot_params['joscomment']) ) {
			// load mambot params info
			$query = "SELECT params"
					. "\n FROM #__$mambots"
					. "\n WHERE element LIKE 'joscomment%'"
					. "\n AND folder = 'content'"
					. "\n AND published = 1 "
			;
			$database->setQuery( $query );
			$mambot = $database->loadObject();
		}
		if (isset($_MAMBOTS)) {
			if (!isset($_MAMBOTS->_content_mambot_params['joscomment'])) {
				// save query to class variable
				$_MAMBOTS->_content_mambot_params['joscomment'] = $mambot;
			}
			// pull query data from class variable
			$mambot = $_MAMBOTS->_content_mambot_params['joscomment'];
		}
		$botParams = new JParameter($mambot->params );
		return $botParams->get( $name, $default );
	}

}

?>
