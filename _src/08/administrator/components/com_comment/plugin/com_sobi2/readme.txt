*************************Requirements:*********************
1. joomla 1.5
2. PHP5 and MySQL5
3. joomlacomment 4.0 beta2

*************************Instalation:**********************

1. Unzip the com_sobi2 folder and upload it to your website in the following folder: 
administrator/components/com_comment/plugin .

2. Open components/com_sobi2/templates/default/sobi.details.tmpl.php and place
at the end of the file the following code:
<?php
require_once(JPATH_SITE."/administrator/components/com_comment/plugin/com_sobi2/josc_com_sobi2.php");
?>

3. Now you should be able to create a new sobi2 plugin in the backend of joomlacomment. 
extensions->other component settings ->new * -> 
from the component dropdown menu choose com_sobi2 and click save.

* If you don't see the new button, first click on save. 
After the page reload the new button should be there.

 
*************************Problems???************************

Ask your question in the sobi2 forum:
http://compojoom.com/forum/26-sobi2-plugin.html