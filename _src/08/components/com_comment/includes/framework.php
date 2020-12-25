<?php
defined('_JEXEC') or die('Restricted access');

/***************************************************************
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
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
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'includes' . DS . 'defines.php');
require_once(JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_comment' . DS . 'library' . DS . 'JOSC_config.php');

require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_component.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_tableutils.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_notification.php');

require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_template.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_properties.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_support.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_security.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_pagenav.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_jscript.php');

require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_search.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_visual.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_board.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_strutils.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_ubbcode.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_menu.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_post.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_form.php');

require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_captcha.php');
?>