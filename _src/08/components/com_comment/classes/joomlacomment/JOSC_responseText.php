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

/**
 * This class creates the response text that should be send back to the
 * browser
 *
 * @author Daniel Dimitrov
 */

class JOSC_responseText  extends JOSC_visual {

    function __construct() {
	$config = JOSC_factory::getConfig('');
    }
    function createSingleCommentText($item) {

	$config = JOSC_factory::getConfig('');
	$dom = new DOMDocument('1.0');

	$post = $dom->appendChild($dom->createElement('post'));

	if ($config->_tree && isset($item['after'])) {
	    $after = $post->appendChild($dom->createElement('after'));
	    $after->appendChild($dom->createTextNode($item['after']));
	}

	$published = $post->appendChild($dom->createElement('published'));
	$published->appendChild($dom->createTextNode($item['view']));

	$noerror = $post->appendChild($dom->createElement('noerror'));
	$noerror->appendChild($dom->createTextNode($item['noerror']));

	$debug = $post->appendChild($dom->createElement('debug'));
	$debug->appendChild($dom->createTextNode($item['debug']));

	if ($item['view']) {
	    $id = $post->appendChild($dom->createElement('id'));
	    $id->appendChild($dom->createTextNode($item['id']));
	    
	    $html = JOSC_utils::cdata(JOSC_utils::filter($this->insertPost($item, '')));

	    $body = $post->appendChild($dom->createElement('body'));
	    $body->appendChild($dom->createTextNode($html));
	}
	if ($config->_captcha) {
	    $captcha = JOSC_utils::cdata(JOSC_security::insertCaptcha('security_refid'));

	    $captcha = $post->appendChild($dom->createElement('captcha'));
	    $captcha->appendChild($dom->createTextNode());
	}
	$dom->formatOutput = true;

	return $dom->saveXML();

    }

    function createAllCommentText() {

    }
}
?>
