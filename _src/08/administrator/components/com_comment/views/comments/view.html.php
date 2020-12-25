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
***************************************************************/#

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CommentViewComments extends JView {
	public $conf;

	function display($tpl = null) {
		$mainframe = JFactory::getApplication();

		$comments =& $this->get('Data');
		$pagination =& $this->get('Pagination');

		$filterState = $mainframe->getUserStateFromRequest('com_comment.filterState', 'filter_state','', 'word');
		$lists['order'] = $mainframe->getUserStateFromRequest('com_comment.filterOrder', 'filter_order', 'date', 'word');
		$lists['orderDirection'] = $mainframe->getUserStateFromRequest('com_comment.filterOrderDirection', 'filter_order_Dir', 'DESC', 'cmd');
		$lists['state'] = JHTML::_('grid.state', $filterState);
		
		if(strtoupper($lists['orderDirection']) == 'ASC') {
			$lists['orderDirection'] = 'ASC';
		} else {
			$lists['orderDirection'] = 'DESC';
		}

		$search = $mainframe->getUserStateFromRequest('com_comment.search', 'search', '', 'string');
		$search = JString::strtolower($search);

		$lists['search'] = $search;

		$componentlist = JOSC_library::getComponentList();
		$selectedcomponent = $mainframe->getUserStateFromRequest('com_comment.component', 'component','com_content', '');
		$lists['componentlist'] = JHTML::_('select.genericlist',$componentlist, 'component', 'class="inputbox" onchange="submitform();"', 'value', 'text', $selectedcomponent);

		$null=null;
		$this->comObject = JOSC_utils::ComPluginObject($selectedcomponent,$null);
		$this->max_length_word = "100"; /* max admin width in characters */

		$comments = $this->renderComments($comments);
		$this->assignRef('lists', $lists);
		$this->assignRef('comments', $comments);
		$this->assignRef('pagination', $pagination);
		parent::display($tpl);
	}

	private function renderComments($comments) {
		$i = 0;
		foreach($comments as $comment) {
			if($comment->notify) {
				$notifyimg = "mailgreen.jpg";
				$notifytxt = "notify if new post " . $comment->email;
				$notifyalt = "yes";
			} else {
				$notifyimg = "mailred.jpg";
				$notifytxt = "not notify if new post " . $comment->email;
				$notifyalt = "no" ;
			}
			if($comment->website) {
				$comment->website = '<a href="' . $comment->website . '"><img src="images/go_f2.png" width="16" height="16" border="0" alt="" /></a>';
			}
			$img = '<img border="0" src="'.JURI::root().'components/com_comment/joscomment/images/'.$notifyimg .'" title="'.$notifytxt.'" alt="'.$notifyalt.'" />';
			if($comment->email) {
				$comment->notify = '<a href="mailto:'.$comment->email.'">' . $img . '</a>';
			} else {
				$comment->notify = $img;
			}
			$this->comObject->setRowDatas($comment); /* used for link... */
			$comment->published = JHTML::_('grid.published', $comment, $i, 'publish_g.png', 'publish_x.png', 'notify');;
			$comment->delete = '<a href="javascript:return void(0);" onclick="return listItemTask(\'cb'.$i . '\',\'notifyremove\'); "><img src="images/delete_f2.png" width="12" height="12" border="0" alt="" /></a>';

			$comment->checked = JHTML::_('grid.id', $i, $comment->id);
			$comment->link = $this->comObject->linkToContent($comment->contentid, $comment->id, true, true);
			if (JString::strlen($comment->comment) > $this->max_length_word) {
				$comment->comment = JString::substr($comment->comment, 0, $this->max_length_word);
				$comment->comment .= "...";
			}
			$comment->link_edit = JRoute::_('index.php?option=com_comment&view=comments&controller=comments&task=edit&cid[]='.$comment->id);
			$comment->name = ($comment->name) ? $comment->name : 'Anonymous';
			$renderedcomments[] = $comment;
			$i++;
		}

		return $renderedcomments;
	}
}
?>