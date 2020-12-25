<?php

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/*
 * Copyright (C) 2009 Daniel Dimitrov (http://compojoom.com)
 * Copyright Copyright (C) 2007 Alain Georgette. All rights reserved.
 * Copyright Copyright (C) 2006 Frantisek Hliva. All rights reserved.
 * License http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * !JoomlaComment is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * !JoomlaComment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 */

require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'includes' . DS . 'framework.php');

/*
 * UTILS CLASS
 */

class JOSC_utils {
	/*
	 * require once the component plugin file and create component class
	 * class component file name must be : josc_[component].class.php
	 * and it must be in the administrator/components/com_comment/plugin/[component]  directory
	 */

	public function ComPluginObject($component, &$row, $set_id=0, $sectionid=0) {
		$file = JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_comment' . DS . 'plugin' . DS . $component . DS . 'josc_' . $component . '.class.php';
		if (!file_exists($file)) {
			echo "joomlacomment: unexpected error. No plugin found for component '$component' !";
			return null;
		}
		require_once($file);
		$class = "JOSC_$component";
		$list = array();
		$list['sectionid'] = $sectionid;
		$comObject = new $class($component, $row, $list);
		return $comObject;
	}

	/*
	 * This is the exec function to call joomlacomment from any component
	 *
	 * $exclude :	active section/category exclusion OR not
	 *
	 * $row and $params will be passed to the plugin functions
	 *
	 */

	function execJoomlaCommentPlugin(&$comObject, &$row, &$params, $exclude=true) {

		if ($comObject == null)
			return;

		$board = JOSC_utils::boardInitialization($comObject, $exclude, $row, $params);

		/* exclude is set again in board
		 * according to section/categories exclusion
		 */

		$html = "<!-- START of joscomment -->";
		if (!$exclude) {
			$board->execute();
			$html .= $board->visual_htmlCode();
		} else {
			unset($board);
		}
		$html .= "<!-- END of joscomment -->";

		return $html;
	}

	/*
	 *      used in mod_comment module !
	 */

	function boardInitialization(&$comObject, &$exclude, &$row, &$params) {

		$josComment_absolute_path = JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'joscomment';
		$josComment_live_site = JURI::root() . 'components/com_comment/joscomment';

		$board = new JOSC_board($josComment_absolute_path, $josComment_live_site, $comObject, $exclude, $row, $params);

		return $board;
	}

	function showMessage($msg) {
		echo("<script type='text/javascript'>alert('$msg');</script>");
	}

	function insertToHead($html) {
		$mainframe = JFactory::getApplication();

		/*
		 * header problems if cache -> example when voting
		 * header is refreshed but not the bots ! so css, js...are lost.
		 */
		if ($mainframe->getCfg('caching')) {
			return $html;
		} else {

			if (!strpos($mainframe->getHead(), $html))
				$mainframe->addCustomHeadTag($html);
			return "";
		}
	}

	function getJOSCUserTypes($unregistered = true) {
		/* since joomla 1.5 table usertypes does not exist no more */

		$usertypes = array();

		if ($unregistered) {
			$usertypes[] = JHTML::_('select.option', '-1', 'Unregistered', 'id', 'title');
		}
		$usertypes[] = JHTML::_('select.option', '3', '.Registered', 'id', 'title');
		$usertypes[] = JHTML::_('select.option', '4', '..Author', 'id', 'title');
		$usertypes[] = JHTML::_('select.option', '2', '...Editor', 'id', 'title');
		$usertypes[] = JHTML::_('select.option', '5', '....Publisher', 'id', 'title');
		$usertypes[] = JHTML::_('select.option', '6', '.Manager', 'id', 'title');
		$usertypes[] = JHTML::_('select.option', '1', '..Administrator', 'id', 'title');
		$usertypes[] = JHTML::_('select.option', '0', '....SAdministrator', 'id', 'title');

		return $usertypes;
	}

	function getJOSCUserType($userType) {
		switch ($userType) {
			case 'Super Administrator':
			case 'SAdministrator':
				$result = 0;
				break;

			case 'Administrator':
				$result = 1;
				break;

			case 'Editor':
				$result = 2;
				break;

			case 'Registered':
				$result = 3;
				break;

			case 'Author':
				$result = 4;
				break;

			case 'Publisher':
				$result = 5;
				break;

			case 'Manager':
				$result = 6;
				break;

			default:
				$result = -1;
				break;
		}
		return $result;
	}

	/*
	 * convert joomlacomment usertype int to standard Joomla value
	 */

	function getJoomlaUserType($JOSCUserType) {
		switch ($JOSCUserType) {
			case 0:
				$result = 'Super Administrator';
				break;

			case 1:
				$result = 'Administrator';
				break;

			case 2:
				$result = 'Editor';
				break;

			case 3:
				$result = 'Registered';
				break;

			case 4:
				$result = 'Author';
				break;

			case 5:
				$result = 'Publisher';
				break;

			case 6:
				$result = 'Manager';
				break;
			default:
				$result = '';
				break;
		}
		return $result;
	}

	/**
	 * Check if the current user is a moderator or if the comment belongs to him
	 * OR if the comment is one of its comment
	 * @param array $moderatorlist
	 * @param array $userid
	 * @return boolean
	 */
	function isCommentModerator($moderatorlist, $userid=0) {
		$loggedUser = JFactory::getUser();

		if (!$userid || !isset($loggedUser->id)) {
			return false;
		}

		if ($loggedUser->id == $userid) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * check if current ($my) user is moderator
	 * OR if usertype param is moderator
	 * @return boolean
	 */

	function isModerator($moderator, $usertype='') {
		$loggedUser = JFactory::getUser();

		if (!$usertype) {
			return (in_array(JOSC_utils::getJOSCUserType($loggedUser->usertype), $moderator));
		} else {
			return (in_array(JOSC_utils::getJOSCUserType($usertype), $moderator));
		}
	}

	function partialIP($ip) {
		$quads = split('\.', $ip);
		$quads[3] = 'xxx';
		return join(".", $quads);
	}

	function ignoreBlock($source, $name, $ignore, $newStr = '') {
		if ($ignore) {
			$after_replace = $newStr;
		} else {
			$after_replace = '\\1';
		}
		return eregi_replace("\{" . $name . "\}([^\[]+)\{/" . $name . "\}", $after_replace, $source);
	}

	/*
	 * $display = true 	: get the block deleting tags
	 * $display = false : replace the block by $newStr
	 *
	 */

	function checkBlock($name, $display, $source, $newStr = '') {
		if ($display) {
			$after_replace = '\\1';
		} else {
			$after_replace = $newStr;
		}
		$source = str_replace('$', '&#36;', $source);
		return preg_replace("/{" . $name . "}(.*?){\/" . $name . "}/si", $after_replace, $source);
	}


	function cdata($data) {
		if ($data == '')
			return '';
		else
			return "<![CDATA[$data]]>";
	}

	function block($source, $name) {
		$begin = '{' . $name . '}';
		$end = '{/' . $name . '}';
		$len = JString::strlen($begin);
		$pos_begin = JString::strpos($source, $begin);
		$pos_end = JString::strpos($source, $end);
		if ($pos_begin === false || $pos_end == false)
			return '';
		else
			return JString::substr($source, $pos_begin + $len, $pos_end - ($pos_begin + $len));
	}

	function filter($html, $downward = false) {
		/*
		 * remind :
		 * 	ISO 	= &#code;
		 *  HTML 	= &name;
		 */
		if ($downward) {
			$html = str_replace('&#64;', '@', $html);
			$html = str_replace('&#92;', '\\', $html);
			$html = str_replace('&#34;', '"', $html);
		} else {
			$html = str_replace('@', '&#64;', stripslashes($html));
			$html = str_replace('\\', '&#92;', $html);
			$html = str_replace('"', '&#34;', $html);
		}
		return $html;
	}

	function buildTree($data) {
		require_once(JPATH_SITE . DS . 'components' . DS . 'com_comment' . DS . 'classes' . DS . 'joomlacomment' . DS . 'JOSC_tree.php');

		$tree = new JOSC_tree();
		return $tree->build($data);
	}

	function setMaxLength($text, $_maxlength_text) {
		if (($_maxlength_text != -1) && (JString::strlen($text) > $_maxlength_text))
			$text = JString::substr($text, 0, $_maxlength_text - 3) . '...';
		return $text;
	}

	/*	 * *************************************************************
	 * The wordwrap function is taken from kunena where it was
	 * originally named htmlwrap()
	 *
	 * htmlwrap() function - v1.7
	 * Copyright (c) 2004-2008 Brian Huisman AKA GreyWyvern
	 *
	 * This program may be distributed under the terms of the GPL
	 *   - http://www.gnu.org/licenses/gpl.txt
	 *
	 *
	 * htmlwrap -- Safely wraps a string containing HTML formatted text (not
	 * a full HTML document) to a specified width
	 *
	 *
	 * Requirements
	 *   htmlwrap() requires a version of PHP later than 4.1.0 on *nix or
	 * 4.2.3 on win32.
	 *
	 *
	 * Changelog
	 * 1.7  - Fix for buggy handling of \S with PCRE_UTF8 modifier
	 *         - Reported by marj
	 *
	 * 1.6  - Fix for endless loop bug on certain special characters
	 *         - Reported by Jamie Jones & Steve
	 *
	 * 1.5  - Tags no longer bulk converted to lowercase
	 *         - Fixes a bug reported by Dave
	 *
	 * 1.4  - Made nobreak algorithm more robust
	 *         - Fixes a bug reported by Jonathan Wage
	 *
	 * 1.3  - Added automatic UTF-8 encoding detection
	 *      - Fixed case where HTML entities were not counted correctly
	 *      - Some regexp speed tweaks
	 *
	 * 1.2  - Removed nl2br feature; script now *just* wraps HTML
	 *
	 * 1.1  - Now optionally works with UTF-8 multi-byte characters
	 *
	 *
	 * Description
	 *
	 * string htmlwrap ( string str [, int width [, string break [, string nobreak]]])
	 *
	 * htmlwrap() is a function which wraps HTML by breaking long words and
	 * preventing them from damaging your layout.  This function will NOT
	 * insert <br /> tags every "width" characters as in the PHP wordwrap()
	 * function.  HTML wraps automatically, so this function only ensures
	 * wrapping at "width" characters is possible.  Use in places where a
	 * page will accept user input in order to create HTML output like in
	 * forums or blog comments.
	 *
	 * htmlwrap() won't break text within HTML tags and also preserves any
	 * existing HTML entities within the string, like &nbsp; and &lt;  It
	 * will only count these entities as one character.
	 *
	 * The function also allows you to specify "protected" elements, where
	 * line-breaks are not inserted.  This is useful for elements like <pre>
	 * if you don't want the code to be damaged by insertion of newlines.
	 * Add the names of the elements you wish to protect from line-breaks as
	 * as a space separate list to the nobreak argument.  Only names of
	 * valid HTML tags are accepted.  (eg. "code pre blockquote")
	 *
	 * htmlwrap() will *always* break long strings of characters at the
	 * specified width.  In this way, the function behaves as if the
	 * wordwrap() "cut" flag is always set.  However, the function will try
	 * to find "safe" characters within strings it breaks, where inserting a
	 * line-break would make more sense.  You may edit these characters by
	 * adding or removing them from the $lbrks variable.
	 *
	 * htmlwrap() is safe to use on strings containing UTF-8 multi-byte
	 * characters.
	 *
	 * See the inline comments and http://www.greywyvern.com/php.php
	 * for more info
	 * ******************************************************************* */

	function wrapWord($str, $width = 75, $break = "\n", $nobreak = "") {

		// Split HTML content into an array delimited by < and >
		// The flags save the delimeters and remove empty variables
		$content = preg_split("/([<>])/", $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		// Transform protected element lists into arrays
		$nobreak = explode(" ", strtolower($nobreak));

		// Variable setup
		$intag = false;
		$innbk = array();
		$drain = "";

		// List of characters it is "safe" to insert line-breaks at
		// It is not necessary to add < and > as they are automatically implied
		$lbrks = "/?!%)-}]\\\"':;&";

		// Is $str a UTF8 string?
//	  $utf8 = (preg_match("/^([\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})*$/", $str)) ? "u" : "";
		// original utf8 problems seems to cause problems with very long text (forumposts)
		// replaced by a little simpler function call by fxstein 8-13-08
		$utf8 = "u";

		while (list(, $value) = each($content)) {
			switch ($value) {

				// If a < is encountered, set the "in-tag" flag
				case "<": $intag = true;
					break;

				// If a > is encountered, remove the flag
				case ">": $intag = false;
					break;

				default:

					// If we are currently within a tag...
					if ($intag) {

						// Create a lowercase copy of this tag's contents
						$lvalue = strtolower($value);

						// If the first character is not a / then this is an opening tag
						if ($lvalue{0} != "/") {

							// Collect the tag name
							preg_match("/^(\w*?)(\s|$)/", $lvalue, $t);

							// If this is a protected element, activate the associated protection flag
							if (in_array($t[1], $nobreak))
								array_unshift($innbk, $t[1]);

							// Otherwise this is a closing tag
						} else {

							// If this is a closing tag for a protected element, unset the flag
							if (in_array(substr($lvalue, 1), $nobreak)) {
								reset($innbk);
								while (list($key, $tag) = each($innbk)) {
									if (substr($lvalue, 1) == $tag) {
										unset($innbk[$key]);
										break;
									}
								}
								$innbk = array_values($innbk);
							}
						}

						// Else if we're outside any tags...
					} else if ($value) {

						// If unprotected...
						if (!count($innbk)) {

							// Use the ACK (006) ASCII symbol to replace all HTML entities temporarily
							$value = str_replace("\x06", "", $value);
							preg_match_all("/&([a-z\d]{2,7}|#\d{2,5});/i", $value, $ents);
							$value = preg_replace("/&([a-z\d]{2,7}|#\d{2,5});/i", "\x06", $value);

							// Enter the line-break loop
							do {
								$store = $value;

								// Find the first stretch of characters over the $width limit
								if (preg_match("/^(.*?\s)?([^\s]{" . $width . "})(?!(" . preg_quote($break, "/") . "|\s))(.*)$/s{$utf8}", $value, $match)) {

									if (strlen($match[2])) {
										// Determine the last "safe line-break" character within this match
										for ($x = 0, $ledge = 0; $x < strlen($lbrks); $x++)
											$ledge = max($ledge, strrpos($match[2], $lbrks{$x}));
										if (!$ledge)
											$ledge = strlen($match[2]) - 1;

										// Insert the modified string
										$value = $match[1] . substr($match[2], 0, $ledge + 1) . $break . substr($match[2], $ledge + 1) . $match[4];
									}
								}

								// Loop while overlimit strings are still being found
							} while ($store != $value);

							// Put captured HTML entities back into the string
							foreach ($ents[0] as $ent)
								$value = preg_replace("/\x06/", $ent, $value, 1);
						}
					}
			}

			// Send the modified segment down the drain
			$drain .= $value;
		}

		// Return contents of the drain
		return $drain;
	}

	function censorText($text, $_censorship_enable, $_censorship_words, $_censorship_case_sensitive) {
		if ($_censorship_enable && is_array($_censorship_words)) {
			if ($_censorship_case_sensitive)
				$replace = 'str_replace';
			else
				$replace = 'str_ireplace';
			foreach ($_censorship_words as $from => $to) {
				$text = call_user_func($replace, $from, $to, $text);
			}
		}
		return $text;
	}

	function inputHidden($tag_name, $value = '') {
		return "<input type='hidden' name='$tag_name' value='$value' />";
	}

	function debug_array($array=array()) {
		if (!is_array($array))
			return "$array is not an array";
		elseif (count($array) <= 0)
			return "$array is empty";

		$index = 0;
		$html = "";
		foreach ($array as $line) {
			$html .= "<b>array[" . $index . "]</b> " . print_r($line, true) . "\n<br />";
			$index++;
		}
	}

	/*
	 * Function to display the Date in the right format with Offset
	 */

	function getLocalDate($strdate, $format='%Y-%m-%d %H:%M:%S') {

		jimport('joomla.utilities.date');
		$user = & JFactory::getUser();

		//	    if we have an anonymous user, then use global config, instead of the user params
		if ($user->get('id')) {
			$tz = $user->getParam('timezone');
		} else {
			$conf = & JFactory::getConfig();
			$tz = $conf->getValue('config.offset');
		}

		$jdate = new JDate($strdate);
		$jdate->setOffset($tz);

		if ($format == 'age') {
			$current = new JDate();
			$current->setOffset($tz);

			$unixtst = (int) $jdate->toUnix();
			$diff = (int) ($current->toUnix() - $unixtst);
			$years = 0;
			$months = 0;
			$days = 0;
			$hours = 0;
			$minutes = 0;
			$seconds = 0;


			if ($diff > (60 * 60 * 24 * 365)) {
				// more than a yeas
				$years = floor($diff / (60 * 60 * 24 * 365));
				$diff = (int) ($diff % (60 * 60 * 24 * 365));
			}
			if ($diff > (60 * 60 * 24 * 30)) {
				// more than a month
				$months = floor($diff / (60 * 60 * 24 * 30));
				$diff = (int) ($diff % (60 * 60 * 24 * 30));
			}

			if ($diff > (60 * 60 * 24)) {
				// more than a day
				$days = floor($diff / (60 * 60 * 24));
				$diff = (int) ($diff % (60 * 60 * 24));
			}

			if ($diff > (60 * 60)) {
				// more than an hour
				$hours = floor($diff / (60 * 60));
				$diff = (int) ($diff % (60 * 60));
			}

			if ($diff > 60) {
				// more than a minute
				$minutes = floor($diff / 60);
				$diff = (int) ($diff % 60);
			}

			$seconds = $diff;
			if ((int) $years) {
				$formatDate = JText::sprintf('%d YEAR' . ((int) $years == 1 ? '' : 'S') . ' AGO', $years);
			} elseif ((int) $months) {
				$formatDate = JText::sprintf('%d MONTH' . ((int) $months == 1 ? '' : 'S') . ' AGO', $months);
			} elseif ((int) $days) {
				$formatDate = JText::sprintf('%d DAY' . ((int) $days == 1 ? '' : 'S') . ' AGO', $days);
			} elseif ((int) $hours) {
				$formatDate = JText::sprintf('%d HOUR' . ((int) $hours == 1 ? '' : 'S') . ' AGO', $hours);
			} elseif ((int) $minutes) {
				$formatDate = JText::sprintf('%d MINUTE' . ((int) $minutes == 1 ? '' : 'S') . ' AGO', $minutes);
			} elseif ((int) $seconds) {
				$formatDate = JText::sprintf('%d SECOND' . ((int) $seconds == 1 ? '' : 'S') . ' AGO', $seconds);
			} else {
				$formatDate = JText::sprintf('%d SECOND' . ((int) $seconds == 1 ? '' : 'S') . ' AGO', $seconds);
			}
		} else {
			$formatDate = $jdate->toFormat($format);
		}
		return $formatDate;
	}

	/**
	 *
	 * @staticvar <int> $ids
	 * @param <string> $component
	 * @return <int>
	 */
	public function getItemid($component='') {
		static $ids;
		if (!isset($ids)) {
			$ids = array();
		}
		if (!isset($ids[$component])) {
			$database = & JFactory::getDBO();
			$query = "SELECT id FROM #__menu"
					. "\n WHERE link LIKE '%option=$component%'"
					. "\n AND type = 'component'"
					. "\n AND published = 1 LIMIT 1";
			$database->setQuery($query);
			$ids[$component] = $database->loadResult();
		}
		return $ids[$component];
	}

}
?>