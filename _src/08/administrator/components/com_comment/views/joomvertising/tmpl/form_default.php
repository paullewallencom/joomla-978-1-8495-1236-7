<?php defined('_JEXEC') or die('Restricted access');
    JHTML::stylesheet('joomvertising.css', 'administrator/components/com_comment/assets/css/');
    JToolBarHelper::title( JText::_( 'JoomVertising AD network support' ), 'user.png' );
?>
<div id="cpanel">
    <?php
    echo $this->quickiconButton( 'index.php?option=com_comment&controller=joomvertising&layout=form', 'icon-48-article-add.png', JText::_('Join as publisher') );
    echo $this->quickiconButton( 'index.php?option=com_comment&controller=joomvertising&layout=join_advertiser', 'icon-48-article-add.png', JText::_('Join as advertiser') );
    ?>
</div>
<div class="clear"></div>
<p>
    Help:
</p>
<ul class="help">
    <li>
        <strong>Publishers present joomvertising! on their websites</strong>
    </li>
    <li>
        <strong>Advertisers place ads on Joomvertising enabled sites</strong>
    </li>
</ul>

<br />