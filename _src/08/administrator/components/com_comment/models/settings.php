<?php
/***************************************************************
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
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

jimport('joomla.application.component.model');

class commentModelSettings extends JModel {
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	function __construct() {
		parent::__construct();

	}

	function getData() {
		if(empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

	public function _getList($query, $start, $limit) {
		$database = JFactory::getDBO();
		$database->setQuery( $query , $start, $limit );
		$rows = $database->loadObjectList();

		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
		return $rows;

	}

	private function _buildQuery() {
		$database = JFactory::getDBO();
		$query = 'SELECT s.* FROM ' . $database->nameQuote('#__comment_setting') . ' AS s'
				. $this->_buildWhereQuery()
				. $this->_buildOrderByQuery();

		return $query;
	}

	private function _buildWhereQuery() {
		$application = JFactory::getApplication();
		$database = JFactory::getDBO();

		$search = $application->getUserStateFromRequest('com_comment.settings.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$where = array();
		if (strlen($search)) {
			$search = '%' . $database->getEscaped($search, true) . '%';
			$search = $database->Quote($search, false);
			$where[] = "LOWER(s.set_name) LIKE $search";
			$where[] = "LOWER(s.set_component) LIKE $search";
		}

		if(count($where)) {
			$where = ' WHERE ' . implode(' OR ', $where);
		} else {
			$where = '';
		}

		return $where;
	}

	private function _buildOrderByQuery() {
		$orderby = ' ORDER BY s.id, s.set_component, s.set_sectionid ';
		return $orderby;
	}

	/* transform the parameters to a format that we can save in the param field
* in the database
	*/
	function textareaHandling($txt) {
		$total = count( $txt );
		for( $i=0; $i < $total; $i++ ) {
			if ( strstr( $txt[$i], "\n" ) ) {
				$txt[$i] = str_replace( "\n", '<br />', $txt[$i] );
			}
		}
		$txt = implode( "\n", $txt );
		return $txt;
	}

	function saveTemplateHTMLSource( $template, $filecontent, $enable_write=0, $disable_write=0, &$msg, $custom_path ) {

		if ( !$template ) {
			$msg = '<b>Operation failed: No template specified.</b>';
			return false;
		}
		if ( !$filecontent ) {
			$msg = '<b>Operation failed: Content empty.</b>';
			return false;
		}
		$file = $custom_path. DS . $template .DS.'index.html';


		$oldperms 	= fileperms($file);

		if ($enable_write) @chmod($file, $oldperms | 0222);

		clearstatcache(); /* ????????????????????? */
		if ( is_writable( $file ) == false ) {
			$msg = '<b>Operation failed: '. $file .' is not writable.</b>';
			return false;
		}

		if ( $fp = fopen ($file, 'w' ) ) {
			fputs( $fp, stripslashes( $filecontent ), strlen( $filecontent ) );
			fclose( $fp );

			if ($enable_write) {
				@chmod($file, $oldperms);
			} else {
				if ($disable_write)
					@chmod($file, $oldperms & 0777555);
			}
			return true;
		} else {
			if ($enable_write) @chmod($file, $oldperms);
			$msg = '<b>Operation failed: Failed to open file for writing.</b>';
			return false;
		}

	}

	function saveTemplateCSSSource( $template, $templateCSS, $filecontent, $enable_write=0, $disable_write=0, &$msg, $custom_path ) {

		if ( !$template || !$templateCSS ) {
			$msg = '<b>Operation failed: No CSS specified.</b>';
			return false;
		}
		if ( !$filecontent ) {
			$msg = '<b>Operation failed: Content empty.</b>';
			return false;
		}


		$file = $custom_path. DS . $template . DS . 'css' .DS. $templateCSS;

		$oldperms 	= fileperms($file);

		if ($enable_write) @chmod($file, $oldperms | 0222);

		clearstatcache(); /* clean PHP file cache */
		if ( is_writable( $file ) == false ) {
			$msg = '<b>Operation failed: '. $file .' is not writable.</b>';
			return false;
		}

		if ( $fp = fopen ($file, 'w' ) ) {
			fputs( $fp, stripslashes( $filecontent ), strlen( $filecontent ) );
			fclose( $fp );

			if ($enable_write) {
				@chmod($file, $oldperms);
			} else {
				if ($disable_write)
					@chmod($file, $oldperms & 0777555);
			}
			return true;
		} else {
			if ($enable_write) @chmod($file, $oldperms);
			$msg = '<b>Operation failed: Failed to open file for writing.</b>';
			return false;
		}

	}


	public function setTemplateCustomPath($check=false,$copytemplate='') {
		if (!$check) {
			$params['_template_custom_path'] = '';
			$params['_template_custom_livepath'] = '';
		}
		$mediapath 		= JPATH_SITE . DS . "media";
		$absolute_path	= $mediapath. DS ."myjosctemplates";
		$livepath		= JURI::base() . "media/myjosctemplates";
		$standardpath 	= JPATH_SITE. DS . "components". DS. "com_comment". DS . "joscomment".DS."templates";

		if (!is_writable("$mediapath")) {
			return ($check ? "<SPAN style=\"color:red;\">$mediapath is not writable</SPAN>":"");
		}
		/*
    	 * check directory and create if not exist
		*/
		if (!@is_dir($absolute_path)) {
			if (!@mkdir($absolute_path, 0755))
				return ($check ? "<SPAN style=\"color:red;\">Unable to create directory '$absolute_path'</SPAN>":"");
		}
		if ($copytemplate) {
			/*
		 	 * if copytemplate = '*'
		 	 *      copy all standard templates (which are not already copied) in custom directory if not exist
		 	 * 	else copy only copytemplate to 'my'copytemplate
			*/
			$folderlist	= JOSC_library::folderList($standardpath, false, false);
			if ($folderlist) {
				foreach($folderlist as $template) {
					if ($copytemplate!='*' && $copytemplate!=$template)
						continue;
					if (!@is_dir($absolute_path.DS."my$template"))
						JOSC_library::copyDir($standardpath.DS . "$template", $absolute_path.DS ."my$template");
				}
			}
		}

		if ($check) {
			return "<SPAN style=\"color:green;\">$absolute_path is writable</SPAN>";
		} else {
			$params['_template_custom_path'] = $absolute_path;
			$params['_template_custom_livepath'] = $livepath;
		}

		return $params;

	}

	/**
	 * Check if tho component folders are existing - if true assume that the components are installed
	 * @return <array> components
	 */
	public function componentsExist() {
		$folderPath = JPATH_SITE . DS .'components'.DS. 'com_comprofiler';
		$cb = JFolder::exists($folderPath);

		$folderPath = JPATH_SITE . DS .'components'.DS. 'com_community';
		$jomSocial = JFolder::exists($folderPath);

		$folderPath = JPATH_SITE . DS .'components'.DS. 'com_k2';
		$k2 = JFolder::exists($folderPath);

		$components = array(
			'CB'		=> $cb,
			'jomSocial' => $jomSocial,
			'k2' =>  $k2
		);
		return $components;
	}
}
?>
