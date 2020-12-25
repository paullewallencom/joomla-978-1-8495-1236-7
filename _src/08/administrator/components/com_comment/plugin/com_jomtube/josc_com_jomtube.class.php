<?php
defined('_JEXEC') or die('Restricted access');

class JOSC_com_jomtube extends JOSC_component {
	private $id;
	private $feedName = 'jomtubeRSS';
	private $component = 'com_jomtube';

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
		$option = JRequest::getCMD('option');
		$view = JRequest::getVar('view');

		return ($option == 'com_jomtube' && $view == 'video');

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
		$component = 'com_jomtube';

		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__jomtube_videos WHERE id='$contentid'");
		$content = $database->loadObject();
		$rss = new UniversalFeedCreator();
		$folderPath = JPATH_SITE . DS .'media'.DS. 'com_comment'.DS.'rss'.DS .$this->component;
		$folderExists = JFolder::exists($folderPath);
		if(!$folderExists) {
			JFolder::create($folderPath);
		}
		$rss->useCached("RSS2.0", $folderPath .DS.$this->feedName.$contentid.'.xml');

		$rss->title = $content->video_title. ' - comments' ;
		$rss->description = $content->title_alias;
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
		$rss->saveFeed("RSS2.0",  $folderPath .DS.$this->feedName.$contentid.'.xml');
	}

	/*
     * construct the link to the content item  
     * (and also direct to the comment if commentId set)
	*/
	public function linkToContent($contentId, $commentId='') {

		$application =& JFactory::getApplication();

		if ($commentId) {
			$commentId = '#josc' . $commentId;
		} else {
			$commentId = '';
		}
		$menuid = $this->getItemid();
		if(!$application->isAdmin()) {
			$url = 'index.php?option=com_jomtube&view=video&id='.$contentId . ( $menuid ? "&Itemid=$menuid" : "" );
		} else {
			$url = JURI::root() . 'index.php?option=com_jomtube&view=video&id='.$contentId . ( $menuid ? "&Itemid=$menuid" : "" );
		}

		if(JRequest::getVar('josctask') == 'rss') {
			$url = JURI::root() . $url;
		}
		$url = urldecode(JRoute::_($url) . $commentId);
		return $url;
	}

	function getItemid( $component='com_jomtube') {
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
		$query 	= "SELECT id, category_name  AS title"
				. "\n FROM #__jomtube_categories"
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
		$query 	= "SELECT id AS id, video_title AS title"
				. "\n FROM #__jomtube_videos "
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
		$title = 'video_title';
		return $title;
	}
	public function getViewJoinQuery($alias, $contentid) {
		$leftjoin	= "\n LEFT JOIN #__jomtube_videos  AS $alias ON $alias.id = $contentid ";
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

		$query 	=  	"SELECT COUNT(c.id) AS countid, ct.id, ct.video_title AS title "
				.	"\n FROM `#__comment`    AS c "
				.  "\n INNER JOIN `#__jomtube_videos` AS ct  ON ct.id = c.contentid "
				.  "\n INNER JOIN `#__jomtube_categories` AS cat ON cat.id = ct.category_id "
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
		$query 	=  "SELECT c.*, ct.video_title AS ctitle $mostrated "
				.  "\n FROM `#__comment`    AS c "
				.  "\n INNER JOIN `#__jomtube_videos` AS ct  ON ct.id = c.contentid "
				.  "\n INNER JOIN `#__jomtube_categories` AS cat ON cat.id = ct.category_id "
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