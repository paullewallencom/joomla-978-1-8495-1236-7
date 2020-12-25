<?php defined('_JEXEC') or die('Restricted access');
    JHTML::stylesheet('joomvertising.css', 'administrator/components/com_comment/assets/css/');
    JToolBarHelper::title( JText::_( 'JoomVertising! Ad network' ), 'user.png' );
?>
<h2>Joomlacomment is part of the beta testing program of an exciting new ad network designed to support Open Source.</h2>

<p class="ads_info">
    <b>This program is scheduled to launch in mid-late July 2009 - Please Join now to be one of the first publishers or advertisers</b>
    <br />
    <br />
    <img src="<?php echo JURI::base(); ?>components/com_comment/assets/images/joomvertising-logo.png" alt="joomvertising logo"/>
</p>

<h2>Welcome to the Joomvertising! program</h2>

<b>What is Joomvertising! ?</b>
<ul class="ads_info">
    <li>
        <b>Joomvertising! is an advertising network.</b>
    </li>
    <li>
        <b>Joomvertising! supports open source
            and Joomla! with a portion of every dollar made through the network.</b>
    </li>
    <li>
        <b>Joomvertising pays the publishers first and foremost – that is you!</b>
    </li>
    <li>
        <b>70% of all advertising revenue goes directly to the publisher!</b>
    </li>
    <li>
        <b>Less than 10% of the advertising revenue goes to the Joomvertising! Ad network.</b>
    </li>
    <li>
        <b>The remainder? Is paid to developers of open source extensions like this
            one and to Joomla! itself.</b>
    </li>
</ul>
<p class="ads_info">
    Use this component with Joomvertising! and get rewarded while helping the Open Source community with part of the revenue you generate by offering Joomvertising! on your website
    through components like this.
</p>
<p class="ads_info">
    The Joomvertising! ad network is expanding to help developers create new and exciting
    extensions while offering publishers an easy and attractive method to monetise.
</p>
<ul class="ads_info">
    <li>
        <b>Joomvertising! – Finds the advertisers</b>
    </li>
    <li>
        <b>Joomvertising! – Places the ads automatically</b>
    </li>
    <li>
        <b>Joomvertising! – Allows the publisher to reject any ad type</b>
    </li>
    <li>
        <b>Joomvertising! – Pays the publisher first!</b>
    </li>
    <li>
        <b>Joomvertising! - is set and forget, install the code, watch the PayPal inbox</b>
    </li>
    <li>
        <b>Joomvertising! – is the only ad network that fully supports Open Source!</b>
    </li>
</ul>

<p class="ads_info">
    Joomvertising! is available for use through this component or by logging onto 
    the <a href="http://joomvertising.com"> JoomVertising! website</a> and registering as a publisher.
</p>
<p class="ads_info">
    The Joomvertising! ad codes can be placed in any banner or user position through
    the standard Joomla! banner component. (See the support section on <a href="http://joomvertising.com">joomvertising.com</a> 
    for easy to understand videos showing exactly how to insert the code.
</p>
<div id="cpanel">
    <b>Start now by joining Joomvertising! Return here to change your advertisement code.</b>
    <?php
    echo $this->quickiconButton( 'index.php?option=com_comment&controller=joomvertising&layout=form_default', 'icon-48-inbox.png', JText::_('Join') );
    echo $this->quickiconButton( 'index.php?option=com_comment&controller=joomvertising&layout=standard_banner', 'icon-48-article.png', JText::_('Change Code') );
    ?>
</div>