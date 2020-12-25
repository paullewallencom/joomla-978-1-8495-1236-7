<?php defined('_JEXEC') or die('Restricted access');

	JHTML::stylesheet('joomvertising.css', 'administrator/components/com_comment/assets/css/');

	JToolBarHelper::title( '<small>' . JText::_( 'JoomVertising AD Network' ) . ':</small> ' .JText::_('Change standard banner code!'), 'user.png' );
?>
<script language="javascript" type="text/javascript">
<!--
function setgood() {
	// TODO: Put setGood back
	return true;
}

function submitbutton(pressbutton) {
	var form = document.adminForm;

	submitform(pressbutton);
}
-->
</script>
<form action="" method="post" id="adminForm" name="adminForm" onsubmit="setgood();">
    <fieldset>
        <legend><?php echo JText::_('Change the code that will be added between the article and the comments'); ?></legend>
        <label for="standard_banner">Standard Banner:</label>
        <textarea id="standard_banner" name="standard_banner" rows="4" cols="4"><?php echo $this->contents; ?></textarea>
        <p>
            <button type="button" onclick="submitbutton('saveStandardBannerCode')">
                <?php echo JText::_('save') ?>
            </button>
        </p>
    </fieldset>
    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="option" value="com_comment" />
    <input type="hidden" name="controller" value="joomvertising" />
    <input type="hidden" name="task" value="saveStandardBannerCode" />

</form>

