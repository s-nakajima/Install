<?php
/**
 * インストール完了
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<h1><?php echo __d('install', 'Installed'); ?></h1>

<p><?php echo __d('install', 'By clicking the button, you can jump to the home.'); ?></p>
<a class="btn btn-lg btn-primary btn-block" href="<?php echo $this->Html->url('/'); ?>">
	<?php echo __d('install', 'Home'); ?>
</a>
