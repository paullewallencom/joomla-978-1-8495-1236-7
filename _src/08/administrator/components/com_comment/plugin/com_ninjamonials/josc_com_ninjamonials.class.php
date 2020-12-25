<?php
defined('_JEXEC') or die('Restricted access');

class JOSC_com_ninjamonials extends JOSC_component {
	private $id;
	private $component;
	private $componentTable = '#__ninjamonials';
	private $componentTableFieldTitle = 'testimonial';
	private $componentTableFieldDescription = 'summary';
	private $feedName = 'com_ninjamonials';
	private $componentCategoryTable = '#__nmls_categories';
	
	public function __construct($component,&$row,&$list) {
		$this->id	= isset($row->nmid) ? $row->nmid : 0; /* document id */
		$this->component = $component;
		$this->setRowDatas($row); /* get specific properties */

		parent::__construct($component,0,$this->id);
	}

	/*
	 * Set specific properties
	 * will be called also in admin.html.php manage comments during row loop
	 */
	function setRowDatas(&$row) {
		/* for optimization reason, do not save row. save just needed parameters */

		//$this->_specific_data	= isset($row->specific) ? $row->specific : '';
	}

	/*
     * returns page id - needed for blocking the form if necessary
     *
     */
	public function getPageId() {
		return $this->id;
	}

	/*
	 * this function generated the rss feed
	 */
	public function createFeed() {
		require_once(JPATH_SITE."/includes/feedcreator.class.php");
		$contentid = JRequest::getInt('contentid','','GET');

		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM ".$database->nameQuote($this->componentTable)." WHERE id='$contentid'");
		$content = $database->loadObject();
		$rss = new UniversalFeedCreator();
		$folderPath = JPATH_SITE . DS .'media'.DS. 'com_comment'.DS.'rss'.DS .$this->component;
		$folderExists = JFolder::exists($folderPath);
		if(!$folderExists) {
			JFolder::create($folderPath);
		}
		$rss->useCached("RSS2.0", $folderPath .DS.$this->feedName.$contentid.'.xml');
		$title = $this->componentTableFieldTitle;
		$rss->title = $content->$title. ' - ' . JText::_('JOOMLACOMMENT_COMMENTS_TITLE') ;
		$description = $this->componentTableFieldDescription;
		$rss->description = $content->$description;
		$rss->link = JURI :: base();
		$database->setQuery("SELECT *,UNIX_TIMESTAMP( date ) AS rss_date FROM #__comment WHERE contentid='$contentid' AND component='$this->component' AND published='1' ORDER BY id DESC", '', 100);
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
		$rss->saveFeed("RSS2.0", $folderPath . DS.$this->feedName.$contentid.'.xml');
	}

	/*
	 * This function is executed to check 
	 * if section/category of the row are authorized or not (exclude/include from the setting)
	 * return : true for authorized / false for excluded
	 */    
	function checkSectionCategory(&$row, $include, $sections=array(), $catids=array(), $contentids=array()) {
			/* doc id excluded ? */
		if (in_array((($row->nmid == 0) ? -1 : $row->nmid), $contentids))
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
	function checkVisual($contentId=0) {
		$option = JRequest::getVar('option');
		$view = JRequest::getVar('view');
		$id = JRequest::getVar('tmid');

		return  ( $option == 'com_ninjamonials' && $id && $view =='display');
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
		$appl =& JFactory::getApplication();
		$menuid = JOSC_utils::getItemid('com_ninjamonials');

		$url = JRoute::_('index.php?option=com_ninjamonials&view=display'
					.'&tmid=' . $contentId
					. ( $menuid ? "&Itemid=$menuid" : "" ) 
					. ($commentId ? '#josc'.$commentId : '') );

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
	function cleanComponentCache() {
		$cache =& JFactory::getCache($this->component);
		$cache->clean($this->component);
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
		$query 	= "SELECT id, name as title"
			. "\n FROM #__nmls_categories"
			. "\n WHERE published = 1"
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
	function getObjectIdOption($id=0, $select=true) {
		$database = JFactory::getDBO();

		$content = array();
		$query 	= "SELECT id AS id, testimonial AS title"
			. "\n FROM  " . $database->nameQuote($this->componentTable)
			. ($id ? "\n WHERE id = $id":"")
			. "\n ORDER BY createdate"
		;
		$database->setQuery( $query );
		$content = $database->loadObjectList();
		if (!$id && $select && count($content)>0) {
			array_unshift( $content, mosHTML::makeOption( '0', '-- Select ninjamonials Item --', 'id', 'title' ) );
		}

		return $content;
	}
	function getViewTitleField() {
		$title = 'testimonial';
		return($title);
	}
	function getViewJoinQuery($alias, $contentid) {
		$leftjoin	= "\n LEFT JOIN #__ninjamonials  AS $alias ON $alias.id = $contentid ";
		return $leftjoin;
	}

	/*----------------------------------------------------------------------------------
	 *  F U N C T I O N S   F O R   MOD_COMMENTS   M O D U L E 
	 *----------------------------------------------------------------------------------
	 */
	function mod_commentsGetMostCommentedQuery($secids, $catids, $maxlines) {
		$database = JFactory::getDBO();

		$limit = $maxlines>=0 ? " limit $maxlines " : "";

		$where = array();
		if ($catids)
			$where[] = $this->componentCategoryTable.".id IN ($catids)";

	    /* 
	     * Count comment id group by contentid 
   	 	 * TODO: restrict according to user rights, dates and category/secitons published and dates...
	     */
		$query 	=  	"SELECT COUNT(c.id) AS countid, c.contentid, ct.id, ct.".$this->componentTableFieldTitle." AS title "
			.	"\n FROM `#__comment`    AS c "
			.  	"\n INNER JOIN ".$database->nameQuote($this->componentTable)."  AS ct  ON ct.id = c.contentid "
			.  	"\n WHERE c.published='1' "
			.	"\n   AND c.component='$this->component' "
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

		$limit = $maxlines>=0 ? " limit $maxlines " : "";

		$where = array();
		if ($catids)
			$where[] = $this->componentCategoryTable.".id IN ($catids)";

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
		$query 	=  "SELECT c.*, ct.".$this->componentTableFieldTitle." AS ctitle $mostrated "
			.  "\n FROM `#__comment`    AS c "
			.  "\n INNER JOIN ".$database->nameQuote($this->componentTable)."    AS ct  ON ct.id = c.contentid "
			.  "\n WHERE c.published='1' "
			.	"\n  AND c.component='$this->component' "
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