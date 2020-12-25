<?php defined('_JEXEC')  or die('Direct Access to this location is not allowed.');
/***************************************************************
*  $Revision$
*
*  Copyright notice
*
*  Copyright 2010 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the CompojoomComment project. The CompojoomComment project is
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
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
require_once(JPATH_SITE. DS . 'components' . DS . 'com_comment' . DS . 'joscomment' . DS . 'utils.php');

$josctask 	= JRequest::getCmd('josctask');
$component  = JRequest::getCmd('component');

$sectionid  = intval(JRequest::getInt('joscsectionid'));
switch ($josctask) {
    case 'ajax_insert':
    case 'ajax_quote':
    case 'ajax_modify':
    case 'ajax_edit':
    case 'ajax_getcomments':
    case 'ajax_delete':
    case 'ajax_delete_all':
    case 'ajax_voting_yes':
    case 'ajax_voting_no':
    case 'ajax_reload_captcha':
    case 'ajax_search':
    case 'ajax_insert_search':
	case 'ajax_unpublish':
	case 'ajax_publish':
		execPlugin($component,$sectionid);
        break;

    case 'rss':
        createFeed();
        break;

    case 'noajax':
		execPlugin($component,$sectionid);
        break;
	case 'captcha':
		captcha();
		break;
    default:
        break;
}
function captcha() {
	$captcha = new JOSC_captcha();
	$captcha->createImage();
	jexit();
}
function execPlugin($component,$sectionid){
	$null=null;
	$comObject = JOSC_utils::ComPluginObject($component, $null, 0, $sectionid);
	JOSC_utils::execJoomlaCommentPlugin($comObject, $null, $null, false);	
}

function createFeed(){
    $null=null;
    $component = JRequest::getCmd('plugin');
    $comObject = JOSC_utils::ComPluginObject($component, $null, 0, '');
    $comObject->createFeed();
}

?>
