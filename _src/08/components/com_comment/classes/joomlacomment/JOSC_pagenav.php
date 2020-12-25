<?php

defined('_JEXEC') or die('Direct Access to this location is not allowed.');
/***************************************************************
*  $Revision$
*
*  Copyright notice
*
*  Copyright 2009 Daniel Dimitrov. (http://compojoom.com)
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
class JOSC_PageNav {
	var $_ajax		= false;
	var $limitstart = null;
	var $limit 		= null;
	var $total 		= null;

	function JOSC_PageNav( $ajax, $total, $limitstart, $limit )
	{
		$this->_ajax		= $ajax;
		$this->total 		= (int) $total;
		$this->limitstart 	= (int) max( $limitstart, 0 );
		$this->limit 		= (int) max( $limit, 1 );
		if ($this->limit > $this->total) {
			/*  0....[total]...[limit] */
			$this->limitstart = 0;
		}
//		if (($this->limit-1)*$this->limitstart > $this->total) {
		/* rounded limitstart to multiple value of limit */
		$this->limitstart -= $this->limitstart % $this->limit; /* % = modulo */
//		}
	}

	function writePagesLinks( $link, $endlink='' )
	{
		$txt = '';
		$js = $this->_ajax;

		$displayed_pages = 10;
		$total_pages = $this->limit ? ceil( $this->total / $this->limit ) : 0;
		$this_page = $this->limit ? ceil( ($this->limitstart+1) / $this->limit ) : 1;
		$start_loop = (floor(($this_page-1)/$displayed_pages))*$displayed_pages+1;
		if ($start_loop + $displayed_pages - 1 < $total_pages) {
			$stop_loop = $start_loop + $displayed_pages - 1;
		} else {
			$stop_loop = $total_pages;
		}

		if (!$js && $link) {
            $link = "&amp;josclimit=". $this->limit;
        }


		$_PN_LT 		=  JText::_('&lt');
		$_PN_RT 		= JText::_('&gt;');
		$_PN_START 		= JText::_( 'Start' );
		$_PN_PREVIOUS 	= JText::_( 'Prev' );
		$_PN_NEXT		= JText::_( 'Next' );
		$_PN_END		= JText::_( 'End' );

		$pnSpace = "";
		if ($_PN_LT || $_PN_RT) $pnSpace = " ";

		if ($this_page > 1 && $link) {
			$page = ($this_page - 2) * $this->limit;
			if ($js)
				$href = "javascript:JOSC_getComments(-1, 0)";
			else
				$href = JRoute::_( "$link&amp;josclimitstart=0$endlink" );
			$txt .= "<a href='$href' class='pagenav' title='". $_PN_START ."'>". $_PN_LT . $_PN_LT . $pnSpace . $_PN_START ."</a> ";
			if ($js)
				$href = "javascript:JOSC_getComments(-1, $page)";
			else
				$href = JRoute::_( "$link&amp;josclimitstart=$page$endlink" );
			$txt .= "<a href='$href' class='pagenav' title='". $_PN_PREVIOUS ."'>". $_PN_LT . $pnSpace . $_PN_PREVIOUS ."</a> ";
		} else {
			$txt .= "<span class='pagenav'>". $_PN_LT . $_PN_LT . $pnSpace . $_PN_START ."</span> ";
			$txt .= "<span class='pagenav'>". $_PN_LT . $pnSpace . $_PN_PREVIOUS ."</span> ";
		}

		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $this->limit;
			if ($i == $this_page || !$link) {
				$txt .= "<span class='pagenav'>". $i ."</span> ";
			} else {
				if ($js)
					$href = "javascript:JOSC_getComments(-1, $page)";
				else
					$href = JRoute::_( $link .'&amp;josclimitstart='. $page . $endlink );
				$txt .= "<a href='$href' class='pagenav'><strong>". $i ."</strong></a> ";
			}
		}

		if ($this_page < $total_pages && $link) {
			$page = $this_page * $this->limit;
			$end_page = ($total_pages-1) * $this->limit;
			if ($js)
				$href = "javascript:JOSC_getComments(-1, $page)";
			else
				$href = JRoute::_( $link ."&amp;josclimitstart=". $page . $endlink );
			$txt .= "<a href='". $href ." ' class='pagenav' title='". $_PN_NEXT ."'>". $_PN_NEXT . $pnSpace . $_PN_RT ."</a> ";
			if ($js)
				$href = "javascript:JOSC_getComments(-1, $end_page)";
			else
				$href = JRoute::_( $link ."&amp;josclimitstart=". $end_page . $endlink );
			$txt .= "<a href='". $href ." ' class='pagenav' title='". $_PN_END ."'>". $_PN_END . $pnSpace . $_PN_RT . $_PN_RT ."</a> ";
		} else {
			$txt .= "<span class='pagenav'>". $_PN_NEXT . $pnSpace . $_PN_RT ."</span> ";
			$txt .= "<span class='pagenav'>". $_PN_END . $pnSpace . $_PN_RT . $_PN_RT ."</span>";
		}
		return $txt;
	}

}
?>
