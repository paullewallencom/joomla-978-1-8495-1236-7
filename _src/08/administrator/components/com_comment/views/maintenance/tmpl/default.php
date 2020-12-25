<?php
defined('_JEXEC') or die('Restricted access');
JToolbarHelper::title('Compojoom comment maintenance');
?>

<?php if($this->sysInfo['warnings']) : ?>
<dl id="system-message">
<dt class="message notice">Message</dt>
<dd class="message message notice fade">
	<ul>
		<li><?php echo $this->sysInfo['warnings'] ?></li>
	</ul>
</dd>
</dl>
<?php else : ?>
<dl id="system-message">
<dt class="message notice">Message</dt>
<dd class="message message fade">
	<ul>
		<li>Your system appears to be up to date</li>
	</ul>
</dd>
</dl>

<?php endif; ?>

<table>
	<thead>
		<tr><th><h2>System Information:</h2></th></tr>
	</thead>
	<tbody>
		<tr>
			<td></td>
			<td>Current</td>
			<td>Recommended</td>
		</tr>
		<tr>
			<td><strong>Joomla Version:</strong></td>
			<td><?php echo $this->sysInfo['jversion'] ?></td>
			<td><?php echo $this->sysInfo['jversionRecommended']; ?> or later</td>
		</tr>
		<tr>
			<td><strong>PHP Version:</strong></td>
			<td><?php echo $this->sysInfo['php'] ?></td>
			<td><?php echo $this->sysInfo['phpRecommended']; ?> or later</td>
		</tr>
		<tr>
			<td><strong>MySQL Version:</strong></td>
			<td><?php echo $this->sysInfo['mysql'] ?></td>
			<td><?php echo $this->sysInfo['mysqlRecommended']; ?> or later</td>
		</tr>
	</tbody>
</table>

<?php


?>
