<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
/***************************************************************
*  $Revision$
*
*  Copyright notice
*
*  Copyright 2010 Daniel Dimitrov. (http://compojoom.com)
*  All rights reserved
*
*  This script is part of the !JoomlaComment project. The !joomlaComment project is
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
class JOSC_ubbcode extends JOSC_support {
	var $_comment;
	var $_ubbcodeCount=1;
	var $_ubbcodeArray=array();
	var $_splitTag;
	var $_limittextTag;
	var $_TO='<';  /* for debug change */
	var $_TC='>';  /* for debug change */

	function __construct($value) {
		$this->_comment = $value;
	}

	function setMaxlength($word, $text, $line) {
		$this->_maxlength_word = $word;
		$this->_maxlength_line = $line;
		$this->_maxlength_text = $text;
	}

	function setParagraphHandling($value) {
		$this->paragraphHandling = $value;
	}

	function parseEmoticons($html) {
		foreach ($this->_emoticons as $ubb => $icon) {
			$html = str_replace($ubb, "<img src='" . $this->_emoticons_path . '/' . $icon . "' border='0' alt='".$ubb."' title='".$ubb."' class='postemoticon' />", $html);
		}
		return $html;
	}

	function code_unprotect($val) {
		$val = str_replace("{ : }", ":", $val);
		$val = str_replace("{ ; }", ";", $val);
		$val = str_replace("{ [ }", "[", $val);
		$val = str_replace("{ ] }", "]", $val);
		$val = str_replace(array("\n\r", "\r\n"), "\r", $val);
		$val = str_replace("\r", '&#13;', $val);
		return JOSC_utils::filter($val, true);
	}

	/*
	 * checks the validity of an url and prevents XSS attacks
	*/
	public static  function do_bbcode_url ($action, $attributes, $content, $params, $node_object) {

		if (!isset ($attributes['default'])) {
			$url = $content;
			$text = htmlspecialchars ($content);
		} else {
			$url = $attributes['default'];
			$text = htmlspecialchars($content);
		}

		/*
		 * check if a valide url was entered
		 * prevents XSS attacks
		 * 
		*/
		if ($action == 'validate' || $action == 'validate_again') {			
			return (JOSC_ubbcode::validate($url) && JOSC_ubbcode::validate($text)) ;
		}

		/*
		 * the module doesn't need a formated link, that is why we only output
		 * link: $url [ text ] for it
		*/
		if($params['support_link']) {
			return "<a href='". JOSC_ubbcode::preventXSS($url)."' rel='external nofollow' title='". JOSC_ubbcode::preventXSS($text)."'>".($text)."</a>";
		} else {
			return 'link:' . $url . ' [' . $text . ']';
		}

	}

	// Unify line breaks of different operating systems
	public static function convertlinebreaks ($text) {
		return preg_replace ("/\015\012|\015|\012/", "\n", $text);
	}

// Remove everything but the newline charachter
	public static function bbcode_stripcontents ($text) {
		return preg_replace ("/[^\n]/", '', $text);
	}

	public static function do_bbcode_img ($action, $attributes, $content, $params, $node_object) {
		if (isset($attributes['default'])) {
			$url = $attributes['default'];
		} else {
			$url = $content;
		}

		if ($action == 'validate' || $action == 'validate_again') {
			return (JOSC_ubbcode::validate($url) && JOSC_ubbcode::validate($content));
		}

		if($params['support_pictures']) {
			if($params['image_width'] > 0) {
				$image =  "<img src='".JOSC_ubbcode::preventXSS($url)."' alt='".JOSC_ubbcode::preventXSS($content)."' width='".$params['image_width']."' />";
			} else {
				$image =  "<img src='".JOSC_ubbcode::preventXSS($url)."' alt='".JOSC_ubbcode::preventXSS($content)."' />";
			}
		} else {
			if($params['support_link']) {
				$image = "<a href='" . JOSC_ubbcode::preventXSS($url) . "'
					rel='external nofollow' alt='Visit ".JOSC_ubbcode::preventXSS($url)."' >View image</a>";
			} else {
				$image = 'image:' . JOSC_ubbcode::preventXSS($url);
			}
		}

		return $image;
	}

	public static function do_bbcode_quote($action, $attributes, $content, $params, $node_object) {
		$name = '';
		if (isset($attributes['default'])) {
			$name = $attributes['default'];
			$wrote = JText::_('JOOMLACOMMENT_UBB_WROTE');
		} else {
			$wrote = JText::_('JOOMLACOMMENT_UBB_QUOTE');
		}

		$quote = "<div class='quote'><div class='genmed'><b>" . $name . ' ' . $wrote . "</b></div><div class='quotebody'>".$content.'</div></div>';
		return $quote;
	}

	public static function do_bbcode_code($action, $attributes, $content, $params, $node_object) {
		$quote = "<div class='code'><div class='genmed'><b>" . JText::_('JOOMLACOMMENT_UBB_CODE') . "</b></div><div class='quotebody'>".$content.'</div></div>';
		return $quote;
	}

	public static function do_bbcode_color ($action, $attributes, $content, $params, $node_object) {
		//the default attribute is one after the bbtag itself [color=blah]text[/color]
		if (isset ($attributes['default'])) {
			if ($action == 'validate' || $action == 'validate_again') {
				return (JOSC_ubbcode::validate($attributes['default']) && JOSC_ubbcode::validate($content));
			}
			return '<span style=\'color: '.JOSC_ubbcode::preventXSS($attributes['default']).'\'>'.$content.'</span>';
		}


		return $content;

	}

	public static function do_bbcode_size ($action, $attributes, $content, $params, $node_object) {
		if (isset ($attributes['default'])) {
			if ($action == 'validate' || $action == 'validate_again') {
				return (JOSC_ubbcode::validate($attributes['default']) && JOSC_ubbcode::validate($content));
			}
			return '<span style=\'font-size: '.JOSC_ubbcode::preventXSS($attributes['default']).'\'>'.$content.'</span>';
		}
		return $content;
	}

	/*
	 * Checks for potential XSS code and returns false to the ubb engine, so that
	 * it can exit the stransformation.
	 * @param	string		Input string
	 * @return	boolean		True if everything is OK with the string.
	 */

	public static function validate($data) {

		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
		$data = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x19])/', '', $data);

		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
		$search = '/&#[xX]0{0,8}(21|22|23|24|25|26|27|28|29|2a|2b|2d|2f|30|31|32|33|34|35|36|37|38|39|3a|3b|3d|3f|40|41|42|43|44|45|46|47|48|49|4a|4b|4c|4d|4e|4f|50|51|52|53|54|55|56|57|58|59|5a|5b|5c|5d|5e|5f|60|61|62|63|64|65|66|67|68|69|6a|6b|6c|6d|6e|6f|70|71|72|73|74|75|76|77|78|79|7a|7b|7c|7d|7e);?/ie';
		$data = preg_replace($search, "chr(hexdec('\\1'))", $data);
		$search = '/&#0{0,8}(33|34|35|36|37|38|39|40|41|42|43|45|47|48|49|50|51|52|53|54|55|56|57|58|59|61|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80|81|82|83|84|85|86|87|88|89|90|91|92|93|94|95|96|97|98|99|100|101|102|103|104|105|106|107|108|109|110|111|112|113|114|115|116|117|118|119|120|121|122|123|124|125|126);?/ie';
		$data = preg_replace($search, "chr('\\1')", $data);

		// now the only remaining whitespace attacks are \t, \n, and \r
		$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base', 'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

		foreach ($ra1 as $ra1word) {
			if (stripos($data, $ra1word ) !== false ) {
				return false;
			}
		}
		return true;
	}

	public static function preventXSS($data) {
		return htmlentities($data, ENT_QUOTES, 'UTF-8');
	}

	/*
	 * defines what ubbcode is supported in the post
	*/

	function initUbbParsing($text) {
		if (!class_exists('StringParser_BBCode')) {
			require_once (JPATH_SITE.DS.'components'.DS.'com_comment'.DS.'classes'.DS.'ubbcode'.DS.'stringparser_bbcode.class.php');
		}
		$bbcode = new StringParser_BBCode();
		$bbcode->addFilter (STRINGPARSER_FILTER_PRE, 'JOSC_ubbcode::convertlinebreaks');

		$bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'nl2br');
		$bbcode->addParser ('list', 'JOSC_ubbcode::bbcode_stripcontents');

		$bbcode->addCode ('b', 'simple_replace', null, array ('start_tag' => '<b>', 'end_tag' => '</b>'),
				'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$bbcode->addCode ('i', 'simple_replace', null, array ('start_tag' => '<i>', 'end_tag' => '</i>'),
				'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$bbcode->addCode ('u', 'simple_replace', null, array ('start_tag' => '<u>', 'end_tag' => '</u>'),
				'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$bbcode->addCode ('s', 'simple_replace', null, array ('start_tag' => '<s>', 'end_tag' => '</s>'),
				'inline', array ('listitem', 'block', 'inline', 'link'), array ());


		$bbcode->addCode ('url', 'usecontent?', 'JOSC_ubbcode::do_bbcode_url', array ('usecontent_param' => 'default', 'support_link' => $this->_support_link ),
				'link', array ('listitem', 'block', 'inline'), array ('link'));

		$bbcode->addCode ('img', 'usecontent', 'JOSC_ubbcode::do_bbcode_img', 
				array ('usecontent_param' => 'default',
						'image_width' => (int) $this->_pictures_maxwidth,
						'support_pictures' => $this->_support_pictures,
						'support_link' => $this->_support_link
				),
				'image', array ('listitem', 'block', 'inline', 'link'), array ());
//		$bbcode->setCodeFlag ('img', 'closetag', BBCODE_CLOSETAG_MUSTEXIST);
		$bbcode->setOccurrenceType ('img', 'image');
		$bbcode->setMaxOccurrences ('image', 4);

		$bbcode->addCode ('size', 'callback_replace', 'JOSC_ubbcode::do_bbcode_size', array ('usecontent_param' => 'default'),
				'inline', array ('listitem', 'block', 'inline', 'link'), array ());

		$bbcode->addCode ('color', 'callback_replace', 'JOSC_ubbcode::do_bbcode_color', array ('usecontent_param' => 'default'),
				'inline', array ('listitem', 'block', 'inline', 'link'), array ());
//		$bbcode->setCodeFlag ('color', 'closetag', BBCODE_CLOSETAG_MUSTEXIST);
		$bbcode->addCode ('list', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
				'list', array ('block', 'listitem'), array ());
		$bbcode->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
				'listitem', array ('list'), array ());
		$bbcode->setCodeFlag ('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
		$bbcode->setCodeFlag ('*', 'paragraphs', true);

		$bbcode->setCodeFlag ('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
		$bbcode->setCodeFlag ('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
		$bbcode->setCodeFlag ('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);

		$bbcode->addCode ('quote', 'callback_replace', 'JOSC_ubbcode::do_bbcode_quote', array ('usecontent_param' => 'default'),
				'block', array ('block'), array('inline'));
		$bbcode->setCodeFlag ('quote', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
		$bbcode->setCodeFlag ('quote', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
		$bbcode->setCodeFlag ('quote', 'closetag.before.newline', BBCODE_NEWLINE_DROP);

		$bbcode->addCode ('code', 'callback_replace', 'JOSC_ubbcode::do_bbcode_code', array ('usecontent_param' => 'default'),
				'block', array ('block'), array('inline'));
		$bbcode->setCodeFlag ('code', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
		$bbcode->setCodeFlag ('code', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
		$bbcode->setCodeFlag ('code', 'closetag.before.newline', BBCODE_NEWLINE_DROP);

		$bbcode->setGlobalCaseSensitive (false);
		$bbcode->setRootParagraphHandling ($this->paragraphHandling);
		$bbcode->setValidateAgain(true);
		
		$maxlength_word = ($this->_maxlength_word!=-1) ? $this->_maxlength_word : 59999;
		$maxlength_text = ($this->_maxlength_text!=-1) ? $this->_maxlength_text : 59999;
		
		$text = JOSC_utils::setMaxLength($text, $maxlength_text);
		$text =  $bbcode->parse ($text);
		$nobreak = 'pre code blockquote';
		$text = JOSC_utils::wrapWord($text,$maxlength_word, ' ', $nobreak);
		
		return $text;
	}
	function ubbcode_parse() {
		$html = $this->_comment;
		
        if ($this->_support_UBBcode) {
            $html = $this->initUbbParsing($html);
        } else {
            // Fix line breaks; respect character and word limits
            $html = nl2br(JOSC_ubbcode::convertlinebreaks($html));
            $maxlength_word = ($this->_maxlength_word!=-1) ? $this->_maxlength_word : 59999;
            $maxlength_text = ($this->_maxlength_text!=-1) ? $this->_maxlength_text : 59999;
            $html = JOSC_utils::setMaxLength($html, $maxlength_text);
            $html = JOSC_utils::wrapWord($html, $maxlength_word);
        }
		if ($this->_support_emoticons) {
			$html = $this->parseEmoticons($html);
		}
		if ($this->_hide) {
			$html = "<span class='hide'>$html</span>";
		}
		return $html;
	}
}
?>