<?php
/*
 * Copyright Copyright (C) 2009 Compojoom.com . All rights reserved!
 * License http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * Compojoom Comment is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Compojoom Comment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 */

defined('_JEXEC') or die('Restricted access');



function output($row, $params) {

    global $option;
    require_once(JPATH_SITE.DS.'components'.DS.'com_comment'.DS.'joscomment'.DS.'utils.php');

    $comObject = JOSC_utils::ComPluginObject($option,$row);
    $comments =  JOSC_utils::execJoomlaCommentPlugin($comObject, $row, $params, true);
    unset($comObject);

    return $comments;
}

?>