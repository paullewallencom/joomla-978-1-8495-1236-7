<?php

defined('_JEXEC') or die('Restricted access');

class JOSC_com_mmsblog extends JOSC_component {

	private $feedName = 'mmsblogRSS';
	private $component = 'com_mmsblog';

	function __construct($component, &$row, &$list) {
		$id = isset($row->id) ? $row->id : 0; /* document id */
		$this->id = $id;

		$this->setRowDatas($row); /* get specific properties */

		parent::__construct($component, 0, $id);
	}

	/*
	 * Set specific properties
	 * will be called also in admin.html.php manage comments during row loop
	 */

	function setRowDatas(&$row) {
		/* for optimization reason, do not save row. save just needed parameters */
	}

	function getPageId() {
		return $this->id;
	}

	function createFeed() {
		require_once(JPATH_SITE . "/includes/feedcreator.class.php");
		$contentid = JRequest::getInt('contentid', '', 'GET');
		$component = 'com_mmsblog';

		$database = JFactory::getDBO();
		$database->setQuery("SELECT subject, content FROM #__mmsblog_item WHERE id='$contentid'");
		$content = $database->loadObject();
		$rss = new UniversalFeedCreator();
		$folderPath = JPATH_SITE . DS . 'media' . DS . 'com_comment' . DS . 'rss' . DS . $this->component;
		$folderExists = JFolder::exists($folderPath);
		if (!$folderExists) {
			JFolder::create($folderPath);
		}
		$rss->useCached("RSS2.0", $folderPath . DS . $this->feedName . $contentid . '.xml');

		$rss->title = $content->subject . ' - comments';
		$rss->description = $content->content;
		$rss->link = JURI :: base();
		$database->setQuery("SELECT *,UNIX_TIMESTAMP( date ) AS rss_date FROM #__comment WHERE contentid='$contentid' AND component='$component' AND published='1' ORDER BY id DESC", '', 100);
		$data = $database->loadAssocList();
		if ($data != null) {
			foreach ($data as $item) {
				$rss_item = new FeedItem();
				$rss_item->author = $item['name'];
				if (strcmp($item['title'], '')) {
					$rss_item->title = stripslashes($item['title']);
				} else {
					$rss_item->title = '[No Title]';
				}
				$rss_item->link = $this->linkToContent($contentid, $item['id']);
				$rss_item->description = stripslashes($item['comment']);
				$rss_item->date = date('r', $item['rss_date']);
				$rss->addItem($rss_item);
			}
		}
		$rss->cssStyleSheet = "http://www.w3.org/2000/08/w3c-synd/style.css";
		$rss->saveFeed("RSS2.0", $folderPath . DS . $this->feedName . $contentid . '.xml');
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

		$mmsblog_catid = $this->getMMSCatId($row->category);

		$result = in_array((($mmsblog_catid->id == 0) ? -1 : $mmsblog_catid->id), $catids);

		if (($include && !$result) || (!$include && $result)) {
			return false; /* include and not found OR exclude and found */
		}
		return true;
	}

	/*
	 * Condition to active or not the display of the post and input form
	 * If the return is false, show readon will be executed.
	 */

	function checkVisual($contentId=0) {
		$option = JRequest::getCMD('option');
		$view = JRequest::getVar('view');

		return ( $option == 'com_mmsblog'
		&& $view == 'item'
		);
	}

	/*
	 * This function will active or deactivate the show readon display
	 */

	function setShowReadon(&$row, &$params, &$config) {
		$show_readon = $config->_show_readon;

		return $show_readon;
	}

	/*
	 * construct the link to the content item
	 * (and also direct to the comment if commentId set)
	 */

	function linkToContent($contentId, $commentId='', $joscclean=false, $admin=false) {
		$appl = & JFactory::getApplication();

		$add = ( $joscclean ? "&joscclean=1" : "" ) . ( $commentId ? "&comment_id=$commentId#josc$commentId" : "" );

		if ($appl->scope == 'mod_comments') {
			$mod_data = $this->getAliasMod($contentId);
			$contentId = $contentId . '-' . $mod_data->alias;
		}

		$url = JRoute::_(JURI::root() . 'index.php?option=com_mmsblog&view=item&id=' . $contentId . $add);

		return ($url);
	}

	/*
	 * clean the cache of the component when comments are inserted/modified...
	 * (if cache is active)
	 */

	function cleanComponentCache() {
		$cache = & JFactory::getCache('com_mmsblog');
		$cache->clean('com_mmsblog');
	}

	/* ----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   A D M I N   P A R T
	 * ----------------------------------------------------------------------------------
	 */

	/*
	 * section option list used to display the include/exclude section list in setting
	 * must return an array of objects (id,title)
	 */

	function getSectionsIdOption() {
		$database = & JFactory::getDBO();

		$sectoptions = array();

		return $sectoptions;
	}

	/*
	 * categories option list used to display the include/exclude category list in setting
	 * must return an array of objects (id,title)
	 */

	function getCategoriesIdOption() {
		$database = & JFactory::getDBO();

		$catoptions = array();
		$query = "SELECT id, title"
				. "\n FROM #__categories"
				. "\n WHERE published = 1"
				. "\n AND section = 'com_mmsblog' "
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

	function getObjectIdOption($id=0, $select=true) {
		$database = & JFactory::getDBO();

		$content = array();
		$query = "SELECT id, subject AS title"
				. "\n FROM #__mmsblog_item "
				. "  WHERE published=1"
				. ($id ? "\n AND id = $id" : "")
		;
		$database->setQuery($query);
		$content = $database->loadObjectList();
		if (!$id && $select && count($content) > 0) {
			array_unshift($content, mosHTML::makeOption('0', '-- Select MMSblog Item --', 'id', 'title'));
		}

		return $content;
	}

	function getViewTitleField() {
		$title = 'subject';
		return($title);
	}

	function getViewJoinQuery($alias, $contentid) {
		$leftjoin = "\n LEFT JOIN #__mmsblog_item  AS $alias ON $alias.id = $contentid ";
		return $leftjoin;
	}

	/* ----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   MOD_COMMENTS   M O D U L E
	 * ----------------------------------------------------------------------------------
	 */

	function mod_commentsGetMostCommentedQuery($secids, $catids, $maxlines) {
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
		$query = "SELECT COUNT(c.id) AS countid, ct.id, ct.subject AS title "
				. "\n FROM `#__comment`    AS c "
				. "\n INNER JOIN `#__mmsblog_item`  AS ct  ON ct.id = c.contentid "
				. "\n INNER JOIN `#__categories` AS cat ON cat.id = ct.category "
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

	function mod_commentsGetOthersQuery($secids, $catids, $maxlines, $orderby) {
		$database = & JFactory::getDBO();

		$component = $this->_component;

		$limit = $maxlines >= 0 ? " limit $maxlines " : "";

		$where = array();
		if ($secids)
			$where[] = "cat.id IN ($secids)";

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
		$query = "SELECT c.*, ct.subject AS ctitle $mostrated "
				. "\n FROM `#__comment`    AS c "
				. "\n INNER JOIN `#__mmsblog_item`    AS ct  ON ct.id = c.contentid "
				. "\n INNER JOIN `#__categories` AS cat ON cat.id = ct.category "
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

	/*
	 * O P T I O N N A L
	 * F O R   E X P E R T   M O D E  O N L Y
	 * not yet available.
	 * do not report theses functions !
	 */
	/*
	 * section option list for the admin setting
	 */

	function getExpertSectionIdOption($sections, $include) {
		$database = & JFactory::getDBO();

		$where = "";
		if ($sections) {
			if ($include)
				$where = "\n AND id IN ($sections) ";
			else
				$where = "\n AND id NOT IN ($sections) ";
		}
		$sectoptions = array();
		$query = "SELECT id, title"
				. "\n FROM #__categories"
				. "\n WHERE published = 1"
				. "\n AND section = 'com_mmsblog' "
				. $where
				//				. "\n AND access >= 0"
				. "\n ORDER BY ordering"
		;
		$database->setQuery($query);
		$sectoptions = $database->loadObjectList();
		// add "All sections"
		array_unshift($sectoptions, JHTML::_('select.option', '0', '-- All --', 'id', 'title'));

		return $sectoptions;
	}

	/*
	 * @var $contentid int
	 * @return sectionid, categoryid, alias
	 */

	function getAliasMod($contentid) {
		$database = & JFactory::getDBO();
		$query = 'SELECT  alias '
				. ' FROM ' . $database->nameQuote('#__mmsblog_item')
				. ' WHERE id = ' . $database->Quote($contentid)
		;
		$database->setQuery($query, '', 1);
		$row = $database->loadObject();
		return $row;
	}

	/*
	 * return the id,title section object
	 */

	function getExpertSectionTitle($sectionid) {
		$database = & JFactory::getDBO();

		if ($sectionid == 0)
			return '(0) All ';

		$query = "SELECT id, title"
				. "\n FROM #__categories"
				. "\n WHERE id=$sectionid"
		;
		$database->setQuery($query);
		$row = $database->loadObjectList();
		return "($sectionid) " . $row[0]->title;
	}

	function getMMSCatId($cat_name) {
		$database = & JFactory::getDBO();
		$query = ' SELECT id '
				. ' FROM ' . $database->nameQuote('#__categories')
				. ' WHERE title = ' . $database->Quote($cat_name);
		$database->setQuery($query);
		$row = $database->loadObject();

		return $row;
	}

}
?>