<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class commentModelJoomvertising extends JModel
{
    function __construct() {
        parent::__construct();
    }
    function sendEMail() {
       JRequest::checkToken() or die( 'Invalid Token' );
       $data = JRequest::get( 'post' );

       $subject = 'JoomVertising programm: add Publisher';

       $mailer =& JFactory::getMailer();
  
       $message =   '<b>Username: </b>' . $data['name']. '<br />'
	   . '<b>User E-Mail: </b>' . $data['email'] . '<br />'
	   . '<b>User Address: </b>' . $data['address'] . '<br />'
	   . '<b>User Country: </b>' . $data['country'] . '<br />'
	   . '<b>User PayPal: </b>' . $data['paypal'] . '<br />'
	   . '<b>User Website: </b>' . $data['website']. '<br />'
	   . '<b>Site category: </b>' . $data['category']. '<br />'
	   . '<b>Monthly impressions: </b>' . $data['impressions']. '<br />'
	   . '<b>Site visitors: </b>' . $data['visitors']. '<br />'
	   . '<b>Site description: </b>' . $data['site_description']. '<br />'
           . '<b>User additional info: </b>' . $data['additional_info'] . '<br />'
	   . '<b>Registered trough extension: </b>' . $data['extension_name']. '<br />';


        $mailer->setSender($data['email']);
        $mailer->addRecipient('advertisement@joomvertising.com');

        $mailer->setSubject($subject);
        $mailer->setBody($message);

        $mailer->IsHTML(true);

        if($mailer->Send() !== true)
        {
            return false;
        } else {
            return true;
        }
    }


    function getStandardBannerCode() {
        $db =& JFactory::getDBO();
        $query = 'SELECT code FROM ' . $db->nameQuote('#__comment_joomvertising')
                .' WHERE type="standard_banner"';
        $db->setQuery($query);
        $data = $db->loadObject();
	if (is_object($data)) {
	    return $data->code;
	} else {
	    return '';
	}
        
    }

    function saveStandardBannerCode() {
        JRequest::checkToken() or die('Invalid Token');
        $db =& JFactory::getDBO();
        $data = JRequest::get('post',2);

        //check if we already have code in the database
        $select_query = 'SELECT * FROM ' . $db->nameQuote('#__comment_joomvertising')
                        . ' WHERE type = "standard_banner";';
        $db->setQuery($select_query);
        $exists = $db->loadObject();

        if($exists) {
            $query = 'UPDATE ' . $db->nameQuote('#__comment_joomvertising')
                . ' SET '. $db->nameQuote('code') . ' = ' . $db->Quote($data['standard_banner'])
                . ' WHERE ' .$db->nameQuote('type'). '="standard_banner";';
            $db->setQuery($query);
        } else {
            // possible if feature was not used
            $query = 'INSERT INTO ' . $db->nameQuote('#__comment_joomvertising') . ' (id,type,code)'
                . ' VALUES ( "1" , "standard_banner",'. $db->Quote($data['standard_banner']).');';
            $db->setQuery($query);
        }
        
        if($db->query() == true ) {
            return true;
        } else {
            return false;
        }
    }
    
}
?>
