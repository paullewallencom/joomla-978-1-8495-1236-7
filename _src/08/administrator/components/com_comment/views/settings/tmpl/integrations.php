<?php
defined('_JEXEC') or die('Restricted access');
$rows = new JOSC_tabRows();

	$row 			= new JOSC_tabRow();
    $row->caption 	= JTEXT::_('GRAVATAR_CAPTION');
    $row->component = JHTML::_('select.booleanlist',  'params[_gravatar]', 'class="inputbox"', $this->config->_gravatar);
    $row->help 		= JTEXT::_('GRAVATAR_HELP');
    $rows->addRow($row);


	$row            = new JOSC_tabRow();
    $row->caption   = JTEXT::_('AKISMET_USE');
    $row->component = JHTML::_('select.booleanlist', 'params[_akismet_use]', 'class="inputbox"', $this->config->_akismet_use);
    $row->help 		= JTEXT::_('AKISMET_HELP');
    $rows->addRow($row);
    $row            = new JOSC_tabRow();
    $row->caption   = JTEXT::_('AKISMET_KEY');
    $row->component = JOSC_library::input('params[_akismet_key]', 'class="inputbox"', $this->config->_akismet_key);
    $row->help 		= JTEXT::_('AKISMET_KEY_HELP');
	$rows->addRow($row);

	$row 			= new JOSC_tabRow();
    $row->caption 	= JText::_('SUPPORT_PROFILES_CAPTION');
    $sorting 		= array();
	$sorting[] 		= JHTML::_('select.option','0', JTEXT::_('NONE'));
	if($this->componentsExist['CB']) {
		$sorting[] 	= JHTML::_('select.option','CB', JTEXT::_('COMPOJOOMCOMMENT_PROFILES_CB'));
	}
	if($this->componentsExist['jomSocial']) {
		$sorting[] 	= JHTML::_('select.option','JOMSOCIAL', JTEXT::_('COMPOJOOMCOMMENT_PROFILES_JOMSOCIAL'));
	}
	if($this->componentsExist['k2']) {
		$sorting[] 	= JHTML::_('select.option','K2', JTEXT::_('COMPOJOOMCOMMENT_PROFILES_K2'));
	}
    $row->component = JHTML::_('select.genericlist',$sorting, 'params[_support_profiles]', 'class="inputbox"', 'value', 'text', $this->config->_support_profiles);

	$row->help 		= JText::_('SUPPORT_PROFILES_HELP');
    $rows->addRow($row);
    $row 			= new JOSC_tabRow();
    $row->caption 	= JText::_('SUPPORT_AVATARS_CAPTION');
	$sorting 		= array();
	$sorting[] 		= JHTML::_('select.option','0', JTEXT::_('NONE'));
	if($this->componentsExist['CB']) {
		$sorting[] 	= JHTML::_('select.option','CB', JTEXT::_('COMPOJOOMCOMMENT_AVATAR_CB'));
	}
	if($this->componentsExist['jomSocial']) {
		$sorting[] 	= JHTML::_('select.option','JOMSOCIAL', JTEXT::_('COMPOJOOMCOMMENT_AVATAR_JOMSOCIAL'));
	}

	$row->component = JHTML::_('select.genericlist',$sorting, 'params[_support_avatars]', 'class="inputbox"', 'value', 'text', $this->config->_support_avatars);
    $row->help 		= JText::_('SUPPORT_AVATARS_HELP');
    $rows->addRow($row);

	echo $rows->tabRows_htmlCode();

?>
