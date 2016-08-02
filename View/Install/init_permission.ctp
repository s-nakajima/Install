<?php
/**
 * パーミッションチェック
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->NetCommonsHtml->script('/install/js/install.js');
echo $this->NetCommonsHtml->css('/install/css/install.css');
?>

<?php echo $this->Form->create(false, array('url' => array('plugin' => 'install', 'controller' => 'install', 'action' => 'init_permission'))); ?>

	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo __d('install', 'Permissions'); ?>
		</div>
		<div>
			<?php foreach ($permissions as $permission): ?>
				<?php if ($permission['error']): ?>
					<p class="bg-danger message text-danger">
						<span class="glyphicon glyphicon-remove"></span>
						<?php echo h($permission['message']); ?>
					</p>
				<?php else: ?>
					<p class="bg-success message text-success">
						<span class="glyphicon glyphicon-ok"></span>
						<?php echo h($permission['message']); ?>
					</p>
				<?php endif; ?>
			<?php endforeach ?>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit">
		<?php echo __d('install', 'Next'); ?>
	</button>

<?php echo $this->Form->end();
