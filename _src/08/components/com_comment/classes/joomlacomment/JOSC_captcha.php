<?php
/***************************************************************
*  $Revision$
*
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
defined('_JEXEC') or die('Restricted access');

class JOSC_captcha {

	function createImage() {
		if (isset($HTTP_GET_VARS["refid"]) && $HTTP_GET_VARS["refid"] != "") {
			$referenceid = stripslashes($HTTP_GET_VARS["refid"]);
		} elseif (isset($_REQUEST['refid']) && $_REQUEST['refid'] != "") {
			$referenceid = stripslashes($_REQUEST['refid']);
		} else {
			$referenceid = md5(mktime() * rand());
		}

		$font = JPATH_COMPONENT . "/joscomment/captcha/century.ttf";
		$background = JPATH_COMPONENT . "/joscomment/captcha/bg" . rand(1, 3) . '.png';

		$im = ImageCreateFromPNG($background);
		$chars = array("a", "A", "b", "B", "c", "C", "d", "D", "e", "E", "f", "F", "g",
			"G", "h", "H", "i", "I", "j", "J", "k",
			"K", "L", "m", "M", "n", "N", "o", "p", "P", "q", "Q",
			"r", "R", "s", "S", "t", "T", "u", "U", "v",
			"V", "w", "W", "x", "X", "y", "Y", "z", "Z", "2", "3", "4",
			"5", "6", "7", "8", "9");

		$length = 5;
		$textstr = "";

		for ($i = 0; $i < $length; $i++) {
			$textstr .= $chars[rand(0, count($chars)-1)];
		}

		$size = rand(12, 14);
		$angle = rand(-4, 4);

		$color = ImageColorAllocate($im, rand(0,64), rand(0,64), rand(0,64));
		$rk_color = ImageColorAllocate($im, 128, 128, 128);

		$textsize = imagettfbbox($size, $angle, $font, $textstr);
		$twidth = abs($textsize[2] - $textsize[0]);
		$theight = abs($textsize[5] - $textsize[3]);

		$x = (imagesx($im) / 2) - ($twidth / 2) + (rand(-25, 25));
		$y = (imagesy($im)) - ($theight / 2) + 3;

		ImageTTFText($im, $size, $angle, $x + 2, $y + 2, $rk_color, $font, $textstr);
		ImageTTFText($im, $size, $angle, $x, $y, $color, $font, $textstr);

		header("Content-Type: image/png");
		ImagePNG($im);
		imagedestroy($im);

		$this->updateDatabase($referenceid, $textstr);
	}

	function updateDatabase($referenceid, $textstr) {
		$db = JFactory::getDBO();
		$insert = 'INSERT INTO ' . $db->nameQuote('#__comment_captcha') . '(insertdate, referenceid, hiddentext)'
				. ' VALUES (now(),' . $db->Quote($referenceid) . ',' . $db->Quote($textstr) . ')';
		$db->setQuery($insert);
		$db->query();

		$delete = 'DELETE FROM ' . $db->nameQuote('#__comment_captcha') . ' WHERE '
				. ' insertdate < date_sub(now(),interval 1 day)';


		$db->setQuery($delete);
		$db->query();
	}
}
?>