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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CommentViewSettings extends JView {

	function display($tmpl = null) {
		$conf['onlydisplay'] = (isset($lists['noedit']) && $lists['noedit']) ? true  : false;
		$conf['adminform']	 = (isset($lists['noform']) && $lists['noform']) ? false : true;
		$conf['checkread']	 = (isset($lists['checkread']) && $lists['checkread']) ? true : false;
		$conf['expertmode']  = (isset($lists['expert']) && $lists['expert']) ? true : false ; //(strpos($task, 'expert')===false) ? false : true;


		$model =& $this->getModel('settings');
		if(!$model) {
			$model = $this->model;
		}
		$rows = $this->get('Data');

		if(isset($this->config)) {
			if ($this->config->_template_modify && $this->config->_template_custom) {
				$this->editTemplateCSSSource();
				$this->editTemplateHTMLSource();
			}
		}

		$componentsExist = $model->componentsExist();

		$this->assignRef('lists', $lists);
		$this->assignRef('conf', $conf);
		$this->assignRef('rows', $rows);
		$this->assignRef('componentsExist', $componentsExist);
		parent::display($tmpl);
	}

	function editTemplateHTMLSource() {

		$file = $this->config->_template_custom_path . DS . $this->config->_template_custom . DS . "index.html";
		if ( $fp = fopen( $file, 'r' ) ) {
			$html = fread( $fp, filesize( $file ) );
			$html = htmlspecialchars( $html );

		} else {
			echo "<b>Operation Failed: Could not open". $file . "</b>";
			return;
		}
		$this->assignRef('file', $file);
		$this->assignRef('html', $html);
	}

	function editTemplateCSSSource() {
		$file = $this->config->_template_custom_path . DS . $this->config->_template_custom . DS . "css" . DS . $this->config->_template_custom_css;
		if ( $fp = fopen( $file, 'r' ) ) {
			$css = fread( $fp, filesize( $file ) );
			$css = htmlspecialchars( $css );
		} else {
			echo "<b>Operation Failed: Could not open". $file . "</b>";
			return;
		}
		$this->assignRef('file_css', $file);
		$this->assignRef('css', $css);
	}

	/*
     * showing the emoticons in backend.
	*/
	function emoticons_confightml($pack) {
		$GLOBALS["JOSC_emoticon"] = array(); /* reset ! */
		$_emoticons_path = JURI::root()."components/com_comment/joscomment/emoticons/$pack/images";


		$file = JPATH_SITE.DS.'components'.DS.'com_comment'.DS.'joscomment'.DS.'emoticons'.DS.$pack.DS.'index.php';
		if (file_exists($file)) {
			require_once($file);
			require_once(JPATH_SITE.DS.'components'.DS.'com_comment'.DS.'classes'.DS. 'joomlacomment' .DS .'JOSC_form.php');

			$form = new JOSC_form(null);
			$form->setSupport_emoticons($this->config->_support_emoticons);
			$form->setEmoticons_path($_emoticons_path);
			$form->setEmoticonWCount($this->config->_emoticon_wcount);
			return $form->emoticons(false);
		}
		return "";

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
}
?>
