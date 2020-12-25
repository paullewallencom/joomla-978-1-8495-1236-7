<?php defined('_JEXEC')  or die('Direct Access to this location is not allowed.');

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
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'includes' . DS . 'defines.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'joscomment' . DS . 'utils.php');
require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_tableutils.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controller.php' );
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'library' . DS .'JOSC_config.php' );
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'library' . DS . 'JOSC_library.php' );

// check access permissions (only superadmins & admins)
$acl =& JFactory::getACL();
$user =& JFactory::getUser();

if ( !( $acl->acl_check('administration', 'config', 'users', $user->usertype) )
	||  $acl->acl_check('administration', 'edit', 'users', $user->usertype, 'components', 'com_comment') ) {
//    	global $mainframe;
//	$mainframe->redirect( 'index2.php', _NOT_AUTH );
}

$controller = JRequest::getWord('controller');


if ($controller) {
	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.$controller.'.php' );
}
$classname = 'CommentController'.$controller;
$controller = new $classname();
$controller->execute( JRequest::getCmd('task') );
$controller->redirect();

?> 