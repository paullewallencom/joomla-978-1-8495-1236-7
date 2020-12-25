<?php
defined('_JEXEC')  or die('Restricted access');
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

require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'joscomment' . DS . 'utils.php');

$vars = $this->vars['vars'];

$category = array (
		'category_id' => $this->vars['category_id']
);

$productArray = array_merge($category, $vars, $this->vars['productArray']);

$product = JArrayHelper::toObject($productArray, 'JObject');

$comObject = JOSC_utils::ComPluginObject('com_virtuemart',$product);
$params=null;
echo JOSC_utils::execJoomlaCommentPlugin($comObject, $product, $params, true);
unset($comObject);
?>