<?php

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Coment Plugin installer
 *
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.5
 */
class CommentInstallerPlugin extends JObject
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$parent	Parent object [JInstaller instance]
	 * @return	void
	 * @since	1.5
	 */
	function __construct(&$parent)
	{
		$this->parent =& $parent;
	}

	/**
	 * Custom install method
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function install()
	{
		// Get a database connector object
		$db =& $this->parent->getDBO();

		// Get the extension manifest object
		$manifest =& $this->parent->getManifest();
		$this->manifest =& $manifest->document;

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Set the extensions name
		$name =& $this->manifest->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'string');
		$this->set('name', $name);

		// Get the component description
		$description = & $this->manifest->getElementByPath('description');
		if (is_a($description, 'JSimpleXMLElement')) {
			$this->parent->set('message', $description->data());
		} else {
			$this->parent->set('message', '' );
		}


		// Set the installation path
		$element =& $this->manifest->getElementByPath('files');


		$group = $this->manifest->attributes('group');
		if (!empty($group)) {
			$this->parent->setPath('extension_root', JPATH_COMPONENT_ADMINISTRATOR.DS.'plugin'.DS.$group);
		} else {
			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('No plugin file specified'));
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// If the plugin directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		 * If we created the plugin directory and will want to remove it if we
		 * have to roll back the installation, lets add it to the installation
		 * step stack
		 */
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

	
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */

		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->parent->copyManifest(-1)) {
			// Install failed, rollback changes
			$this->parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Could not copy setup file'));
			return false;
		}


		return true;
	}

	/**
	 * Custom uninstall method
	 *
	 * @access	public
	 * @param	int		$cid	The id of the plugin to uninstall
	 * @param	int		$clientId	The id of the client (unused)
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function uninstall($id, $clientId )
	{
		// Initialize variables
		$row	= null;
		$retval = true;
		$db		=& $this->parent->getDBO();

		// First order of business will be to load the module object table from the database.
		// This should give us the necessary information to proceed.
		$row = & JTable::getInstance('plugin');
		if ( !$row->load((int) $id) ) {
			JError::raiseWarning(100, JText::_('ERRORUNKOWNEXTENSION'));
			return false;
		}

		// Is the plugin we are trying to uninstall a core one?
		// Because that is not a good idea...
		if ($row->iscore) {
			JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::sprintf('WARNCOREPLUGIN', $row->name)."<br />".JText::_('WARNCOREPLUGIN2'));
			return false;
		}

		// Get the plugin folder so we can properly build the plugin path
		if (trim($row->folder) == '') {
			JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::_('Folder field empty, cannot remove files'));
			return false;
		}

		// Set the plugin root path
		$this->parent->setPath('extension_root', JPATH_COMPONENT_ADMINISTRATOR.DS.'plugin'.DS.$row->folder);

		// Because plugins don't have their own folders we cannot use the standard method of finding an installation manifest
		$manifestFile = JPATH_ROOT.DS.'plugins'.DS.$row->folder.DS.$row->element.'.xml';
		if (file_exists($manifestFile))
		{
			$xml =& JFactory::getXMLParser('Simple');

			// If we cannot load the xml file return null
			if (!$xml->loadFile($manifestFile)) {
				JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::_('Could not load manifest file'));
				return false;
			}

			/*
			 * Check for a valid XML root tag.
			 * @todo: Remove backwards compatability in a future version
			 * Should be 'install', but for backward compatability we will accept 'mosinstall'.
			 */
			$root =& $xml->document;
			if ($root->name() != 'install' && $root->name() != 'mosinstall') {
				JError::raiseWarning(100, JText::_('Plugin').' '.JText::_('Uninstall').': '.JText::_('Invalid manifest file'));
				return false;
			}

			// Remove the plugin files
			$this->parent->removeFiles($root->getElementByPath('images'), -1);
			$this->parent->removeFiles($root->getElementByPath('files'), -1);
			JFile::delete($manifestFile);

			// Remove all media and languages as well
			$this->parent->removeFiles($root->getElementByPath('media'));
			$this->parent->removeFiles($root->getElementByPath('languages'), 1);
		} else {
			JError::raiseWarning(100, 'Plugin Uninstall: Manifest File invalid or not found');
			return false;
		}

		// Now we will no longer need the plugin object, so lets delete it
		$row->delete($row->id);
		unset ($row);

		// If the folder is empty, let's delete it
		$files = JFolder::files($this->parent->getPath('extension_root'));
		if (!count($files)) {
			JFolder::delete($this->parent->getPath('extension_root'));
		}

		return $retval;
	}

	/**
	 * Custom rollback method
	 * 	- Roll back the plugin item
	 *
	 * @access	public
	 * @param	array	$arg	Installation step to rollback
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _rollback_plugin($arg)
	{
		// Get database connector object
		$db =& $this->parent->getDBO();

		// Remove the entry from the #__plugins table
		$query = 'DELETE' .
				' FROM `#__plugins`' .
				' WHERE id='.(int)$arg['id'];
		$db->setQuery($query);
		return ($db->query() !== false);
	}
}
