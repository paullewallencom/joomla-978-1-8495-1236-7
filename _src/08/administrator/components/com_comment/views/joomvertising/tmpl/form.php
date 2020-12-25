<?php defined('_JEXEC') or die('Restricted access');

	JHTML::stylesheet('joomvertising.css', 'administrator/components/com_comment/assets/css/');

	JToolBarHelper::title(   JText::_( 'Joomvertising!' ) . ': ' .JText::_('join now!'), 'user.png' );
?>
<script language="javascript" type="text/javascript">
<!--
function setgood() {
	// TODO: Put setGood back
	return true;
}

function submitbutton(pressbutton) {
	var form = document.adminForm;

	// do field validation
    if (form.name.value == '') {
        return alert ("<?php echo JText::_( 'Please enter your name'); ?>");
    } else if (form.email.value == '') {
        return alert ("<?php echo JText::_('please enter e-mail address'); ?>");
    } else if (form.address.value == '') {
        return alert ("<?php echo JText::_('please enter your post address'); ?>");
    } else if (form.country.value == '') {
        return alert ("<?php echo JText::_('please enter your country'); ?>");
    } else if (form.paypal.value == '') {
        return alert ("<?php echo JText::_('please enter paypal address'); ?>");
    } else if (form.website.value == '') {
        return alert ("<?php echo JText::_('please enter your website address'); ?>");
    } else if (form.category.value == '') {
        return alert ("<?php echo JText::_('please enter site category'); ?>");
    } else if (form.impressions.value == '') {
        return alert ("<?php echo JText::_('please enter impressions'); ?>");
    } else if (form.visitors.value == '') {
        return alert ("<?php echo JText::_('please enter visitors'); ?>");
    } else if (form.site_description.value == '') {
        return alert ("<?php echo JText::_('please enter a site description'); ?>");
    }
	submitform(pressbutton);
}
-->
</script>

<form action="" method="post" id="adminForm" name="adminForm" onsubmit="setgood();">
<fieldset>
<legend><?php echo JText::_('Take part in the Joomvertising! program as a Publisher'); ?></legend>
<fieldset>
    <legend><?php echo JText::_('Your contact details'); ?></legend>
<p>
<label for="name">Your name:</label><input type="text" id="name" name="name" value="<?php echo JFactory::getUser()->name; ?>" />
</p>
<p>
<label for="email">Your e-mail:</label><input type="text" id="email" name="email" value="<?php echo JFactory::getUser()->email; ?>" />
</p>
<p>
<label for="address">Your address:</label><textarea id="address" name="address" rows="2" cols="2">
</textarea>
</p>
<p>
<label for="country">Your country:</label><input type="text" id="country" name="country" value="" />
</p>
<p>
<label for="paypal"><span class="info">Paypal account*:</span></label><input type="text" id="paypal" name="paypal" value="<?php echo JFactory::getUser()->email; ?>" />
</p>
</fieldset>
<fieldset>
    <legend><?php echo JText::_('Website to register'); ?></legend>
<p>
<label for="website">Website:</label><input type="text" id="website" name="website" value="<?php echo JURI::root(); ?>" />
</p>
<p>
<label for="category">Site Category:</label><input type="text" id="category" name="category" value="" />
</p>
<p>
<label for="impressions">Monthly impressions:</label><input type="text" id="impressions" name="impressions" value="" />
</p>
<p>
<label for="visitors">Monthly visitors:</label><input type="text" id="visitors" name="visitors" value="" />
</p>
<p>
<label for="site_description">Site Description:</label><textarea id="site_description" name="site_description" rows="4" cols="2">

</textarea>
</p>

<p>
<label for="additional_info">Additional info:</label><textarea id="additional_info" name="additional_info" rows="4" cols="2">
Please add my website to your advertisement program.
</textarea>
</p>
</fieldset>
<p class="info">
    *<a href="http://joomvertising.com">Joomvertising.com</a> currently use only <a href="http://paypal.com" target="_blank">Paypal</a> for processing payments.
</p>

<p>
			<button type="button" onclick="submitbutton('send')">
				<?php echo JText::_('send') ?>
			</button>
</p>

</fieldset>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_comment" />
<input type="hidden" name="extension_name" value="joomlacomment compojoom" />
<input type="hidden" name="controller" value="joomvertising" />
<input type="hidden" name="task" value="send" />

</form>
<p>
    Please fill out the form above or send the information requested in an email to: <a href='mailto:advertisement@joomvertising.com?subject=Join as publisher&amp;body=Write information about your website here'>advertisement@joomvertising.com</a>
</p>

