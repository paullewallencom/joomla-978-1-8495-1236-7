<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Daniel Dimitrov (http://compojoom.com)
 *  All rights reserved
 *
 *  This script is part of the !CompojoomComment Project. The !CompojoomComment project is
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
 * ************************************************************* */

/**
 * 
 * @author Daniel Dimitrov
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class JOSC_search extends JOSC_support {

	public $_search;
	public $_keyword;
	public $_phrase;
	public $_counter;
	public $_resultTemplate;

	public function __construct($value, &$comObject) {
		$this->_search = $value;
		parent::__construct($comObject);
	}

	function setKeyword($value) {
		$this->_keyword = addslashes(trim($value));
	}

	function setPhrase($value) {
		$this->_phrase = $value;
	}

	function anonymous($name) {
		if ($name == '') {
			$name = JOSC_utils::filter(JText::_('JOOMLACOMMENT_ANONYMOUS'));
		}
		return $name;
	}

	function filterAll($item) {
		return JOSC_board::filterAll($item);
	}

	function searchMatch() {
		$result = ($this->_counter == 1) ? JOSC_utils::filter(JText::_('JOOMLACOMMENT_SEARCHMATCH')) : JOSC_utils::filter(JText::_('JOOMLACOMMENT_SEARCHMATCHES'));
		return sprintf($result, $this->_counter);
	}

	function trimResult($html, $word, $size) {
		$html = str_replace("\n", '', $html);
		if ($word == '')
			return '';
		$p = JString::strpos($html, $word);
		if ($p == 0)
			return JString::substr($html, 0, $size);
		$len = JString::strlen($html);
		$sublen = JString::strlen($word);
		$size = ($size - $sublen) / 2;
		if ($size >= $len)
			$result = $html;
		else {
			if ($p < $size)
				$a = $p - 1;
			else
				$a = $size;
			$c = $len - ($p + $sublen);
			if ($c < $size)
				$b = $c;
			else
				$b = $size;
			$b = $a + $b + $sublen;
			$result = JString::substr($html, $p - $a, $b);
		}
		return $result;
	}

	function highlightWord($html, $maxSize = -1) {
		$html = stripslashes($html);
		if (($this->_phrase == 'any') Or ($this->_phrase == 'all')) {
			$words = split(' ', $this->_keyword);
			if ($maxSize != -1)
				$html = $this->trimResult($html, $words[0], $maxSize);
			foreach ($words as $item) {
				if ($item != '')
					$html = str_ireplace($item, "<span>$item</span>", $html);
			}
			return $html;
		} else {
			if ($maxSize != -1)
				$html = $this->trimResult($html, $this->_keyword, $maxSize);
			return str_ireplace($this->_keyword, "<span>$this->_keyword</span>", stripslashes($html));
		}
	}

	function addItem($item, $itemCSS) {
		$comment = $this->censorText($item['comment']);
		$title = $this->censorText($this->highlightWord($item['title']));
		$name = $this->censorText($this->highlightWord($this->anonymous($item['name'])));
		$address = $this->_comObject->linkToContent($item['contentid'], $item['id']);

		$maxsize = min(200, $this->_maxlength_text);
		$comment = JOSC_utils::wrapWord($comment, $this->_maxlength_word, ' ');
		if ($maxsize != 0 && JString::strlen($comment) > $maxsize) {
			$comment = '...' . $this->highlightWord($comment, $maxsize) . '...';
		} else {
			$comment = $this->highlightWord($comment);
		}
		$html = $this->_resultTemplate;
		$html = str_replace('{postclass}', 'sectiontableentry' . $itemCSS, $html);
		$html = str_replace('{title}', "<b>$title</b>", $html);
		$html = str_replace('{_JOOMLACOMMENT_BY}', JOSC_utils::filter(JText::_('JOOMLACOMMENT_BY')), $html);
		$html = str_replace('{name}', $name, $html);
		$html = str_replace('{address}', $address, $html);
		$html = str_replace('{preview}', $comment, $html);
		$html = str_replace('{date}', JOSC_utils::getLocalDate($item['date'], $this->_date_format), $html); //date($this->_date_format, strToTime($item['date'])), $html);
		return $html;
	}

	function find($terms) {
		$database = & JFactory::getDBO();
		/* TODO : search for all only if .... */
		$database->setQuery("SELECT * FROM #__comment WHERE component='$this->_component' AND ( $terms ) ORDER BY date DESC");
		$data = $database->loadAssocList();
		$html = '';
		$itemCSS = 1;
		$this->_counter = 0;
		if ($data == null)
			return '';
		foreach ($data as $item) {
			$item = $this->filterAll($item);
			$html .= $this->addItem($item, $itemCSS);
			$this->_counter++;
			$itemCSS++;
			if ($itemCSS == 3)
				$itemCSS = 1;
		}
		return $html;
	}

	function terms($list, $term) {
		$result = '';
		foreach ($list as $item) {
			if ($result != '')
				$result .= ' OR ';
			$result .= $item . " $term ";
		}
		return $result;
	}

	function anyWords($list) {
		$result = '';
		if (!JString::strpos($this->_keyword, ' '))
			return $this->terms($list, "LIKE '%$this->_keyword%'");
		$words = split(' ', $this->_keyword);
		foreach ($words as $item) {
			if ($item != '') {
				if ($result != '')
					$result .= ' OR ';
				$result .= $this->terms($list, "LIKE '%$item%'");
			}
		}
		return $result;
	}

	function allWords($list) {
		$result = '';
		if (!strpos($this->_keyword, ' '))
			return $this->terms($list, "LIKE '%$this->_keyword%'");
		$words = split(' ', $this->_keyword);
		foreach ($words as $item) {
			if ($item != '') {
				if ($result != '')
					$result .= ' AND ';
				$result .= '(' . $this->terms($list, "LIKE '%$item%'") . ')';
			}
		}
		return $result;
	}

	function exactPhrase($list) {
		return $this->terms($list, "LIKE '%$this->_keyword%'");
	}

	function search_htmlCode() {
		$html = $this->_search;
		if ($this->_keyword) {
			$list[] = 'name';
			$list[] = 'title';
			$list[] = 'comment';
			if ($this->_phrase == 'any')
				$terms = $this->anyWords($list);
			if ($this->_phrase == 'all')
				$terms = $this->allWords($list);
			if ($this->_phrase == 'exact')
				$terms = $this->exactPhrase($list);
			$this->_resultTemplate = JOSC_utils::block($html, 'searchresult');
			$results = $this->find($terms);
		} else
			$results = '';
		$html = str_replace('{resulttitle}', ($results) ? $this->searchMatch() : JOSC_utils::filter(Jtext::_('JOOMLACOMMENT_NOSEARCHMATCH')), $html);
		$html = JOSC_utils::ignoreBlock($html, 'searchresult', true, $results);


		return $html;
	}

}
?>
