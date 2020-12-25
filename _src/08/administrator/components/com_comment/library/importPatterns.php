<?php
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

function getImport_mXcomment($checktable=false) {
		$database =& JFactory::getDBO();

		if (JOSC_TableUtils::existsTable( '#__mxc_comments' )) {
			if (!JOSC_TableUtils::TableColumnsGet( '#__mxc_comments' )) {
				return;
			}
		} else {
			return;
		}


		$result['fromtable'] = $database->getPrefix().'mxc_comments';
	 /* ['sel_columns'][joscolumn] = component_column */
		$result['sel_columns']['componentfield']	= 'component';
		$result['sel_columns']['id'] 		= 'id';
		$result['sel_columns']['contentid'] = 'contentid';
		$result['sel_columns']['date'] 		= 'date';
		$result['sel_columns']['name'] 		= 'name';
		$result['sel_columns']['userid'] 	= 'iduser';
		$result['sel_columns']['ip'] 		= 'ip';
		$result['sel_columns']['email']		= 'email';
		$result['sel_columns']['notify']	= 'subscribe';
		$result['sel_columns']['website']	= 'web';
		$result['sel_columns']['title']		= 'title';
		$result['sel_columns']['comment']	= 'comment';
		$result['sel_columns']['published']	= 'published';
		$result['sel_columns']['voting_yes'] = 'rating'; /* better than nothing */
		$result['sel_columns']['voting_no']  = '';
		$result['sel_columns']['parentid']	= 'parentid';

		return( $result );
	}

	function getImport_AkoComment($checktable=false) {
		$database =& JFactory::getDBO();


		if (JOSC_TableUtils::existsTable('#__akocomment') == true) {
			if (!JOSC_TableUtils::TableColumnsGet( '#__akocomment' )) {
				return;
			}
		} else {
			return;
		}
		$result['fromtable'] = $database->getPrefix().'akocomment';
	 /* ['sel_columns'][joscolumn] = component_column */
		$result['sel_columns']['componentfield']	= '';
		$result['sel_columns']['id'] 		= 'id';
		$result['sel_columns']['contentid'] = 'contentid';
		$result['sel_columns']['date'] 		= 'date';
		$result['sel_columns']['name'] 		= 'name';
		$result['sel_columns']['userid'] 	= 'iduser';
		$result['sel_columns']['ip'] 		= 'ip';
		$result['sel_columns']['email']		= 'email';
		$result['sel_columns']['notify']	= 'subscribe';
		$result['sel_columns']['website']	= 'web';
		$result['sel_columns']['title']		= 'title';
		$result['sel_columns']['comment']	= 'comment';
		$result['sel_columns']['published']	= 'published';
		$result['sel_columns']['voting_yes'] = '';
		$result['sel_columns']['voting_no']  = '';
		$result['sel_columns']['parentid']	= 'parentid';

		return( $result );
	}

	function getImport_JReaction($checktable=false) {
		$database =& JFactory::getDBO();


		if (JOSC_TableUtils::existsTable( '#__jreactions' )) {
			if (!JOSC_TableUtils::TableColumnsGet( '#__jreactions' )) {
				return;
			}
		} else {
			return;
		}


		$result['fromtable'] = $database->getPrefix().'jreactions';
	 /* ['sel_columns'][joscolumn] = component_column */
		$result['sel_columns']['componentfield']	= '';
		$result['sel_columns']['id'] 		= 'id';
		$result['sel_columns']['contentid'] = 'contentid';
		$result['sel_columns']['date'] 		= 'date';
		$result['sel_columns']['name'] 		= 'name';
		$result['sel_columns']['userid'] 	= 'userid';
		$result['sel_columns']['ip'] 		= 'ip';
		$result['sel_columns']['email']		= 'email';
		$result['sel_columns']['notify']	= ''; //'subscribe';
		$result['sel_columns']['website']	= 'website';
		$result['sel_columns']['title']		= 'title';
		$result['sel_columns']['comment']	= 'comments';
		$result['sel_columns']['published']	= 'published';
		$result['sel_columns']['voting_yes'] = 'rank'; /* better than nothing */
		$result['sel_columns']['voting_no']  = '';
		$result['sel_columns']['parentid']	= '';

		return( $result );
	}

	function getImport_JomComment($checktable=false) {
		$database =& JFactory::getDBO();


		if (JOSC_TableUtils::existsTable( '#__jomcomment' )) {
			if (!JOSC_TableUtils::TableColumnsGet( '#__jomcomment' )) {
				return;
			}
		} else {
			return;
		}

		$result['fromtable'] = $database->getPrefix().'jomcomment';
	 /* ['sel_columns'][joscolumn] = component_column */
		$result['sel_columns']['componentfield']	= 'option';
		$result['sel_columns']['id'] 		= 'id';
		$result['sel_columns']['contentid'] = 'contentid';
		$result['sel_columns']['date'] 		= 'date';
		$result['sel_columns']['name'] 		= 'name';
		$result['sel_columns']['userid'] 	= 'user_id';
		$result['sel_columns']['ip'] 		= 'ip';
		$result['sel_columns']['email']		= 'email';
		$result['sel_columns']['notify']	= ''; //'subscribe';
		$result['sel_columns']['website']	= 'website';
		$result['sel_columns']['title']		= 'title';
		$result['sel_columns']['comment']	= 'comment';
		$result['sel_columns']['published']	= 'published';
		$result['sel_columns']['voting_yes'] = 'star'; /* better than nothing */
		$result['sel_columns']['voting_no']  = '';
		$result['sel_columns']['parentid']	= 'parentid';

		return( $result );
	}

	function getImport_YvComment($checktable=false) {
		$database =& JFactory::getDBO();


		if (JOSC_TableUtils::existsTable( '#__yvcomment' )) {
			if (!JOSC_TableUtils::TableColumnsGet( '#__yvcomment' )) {
				return;
			}
		} else {
			return;
		}

		$result['fromtable'] = $database->getPrefix().'yvcomment';
	 /* ['sel_columns'][joscolumn] = component_column */
		$result['sel_columns']['componentfield']	= '';
		$result['sel_columns']['id'] 		= 'id';
		$result['sel_columns']['contentid'] = 'parentid';
		$result['sel_columns']['date'] 		= 'created';
		$result['sel_columns']['name'] 		= 'created_by_alias';
		$result['sel_columns']['userid'] 	= 'created_by';
		$result['sel_columns']['ip'] 		= '';
		$result['sel_columns']['email']		= '';
		$result['sel_columns']['notify']	= ''; //'subscribe';
		$result['sel_columns']['website']	= '';
		$result['sel_columns']['title']		= 'title_alias';
		$result['sel_columns']['comment']	= 'fulltext';
		$result['sel_columns']['published']	= 'state';
		$result['sel_columns']['voting_yes'] = ''; /* better than nothing */
		$result['sel_columns']['voting_no']  = '';
		$result['sel_columns']['parentid']	= '';

		return( $result );
	}
	function getImport_Wordpress($checktable=false) {
		$database =& JFactory::getDBO();


		if (JOSC_TableUtils::existsTable( '#__wp_comments' )) {
			if (!JOSC_TableUtils::TableColumnsGet( '#__wp_comments' )) {
				return;
			}
		} else {
			return;
		}

		$result['fromtable'] = $database->getPrefix().'wp_comments';
	 /* ['sel_columns'][joscolumn] = component_column */
		$result['sel_columns']['componentfield']	= '';
		$result['sel_columns']['id'] 		= 'comment_ID';
		$result['sel_columns']['contentid'] = 'comment_post_ID';
		$result['sel_columns']['date'] 		= 'comment_date';
		$result['sel_columns']['name'] 		= 'comment_author';
		$result['sel_columns']['userid'] 	= 'user_id';
		$result['sel_columns']['ip'] 		= 'comment_author_IP';
		$result['sel_columns']['email']		= 'comment_author_email';
		$result['sel_columns']['notify']	= ''; //'subscribe';
		$result['sel_columns']['website']	= 'comment_author_url';
		$result['sel_columns']['title']		= 'title_alias';
		$result['sel_columns']['comment']	= 'comment_content';
		$result['sel_columns']['published']	= 'comment_approved';
		$result['sel_columns']['voting_yes'] = 'comment_karma'; /* better than nothing */
		$result['sel_columns']['voting_no']  = '';
		$result['sel_columns']['parentid']	= 'comment_parent';

		return( $result );
	}
?>
