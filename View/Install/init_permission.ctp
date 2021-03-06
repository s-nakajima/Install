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

<?php echo $this->NetCommonsForm->create(false, array('url' => array(
		//'plugin' => 'install',
		//'controller' => 'install',
		'action' => 'init_permission',
		'?' => ['language' => Configure::read('Config.language')]
	))); ?>

	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo __d('install', 'Versions'); ?>
		</div>
		<div>
			<?php foreach ($versions as $version): ?>
				<?php if ($version['error']): ?>
					<p class="bg-danger message text-danger">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
						<?php echo h($version['message']); ?>
					</p>
				<?php elseif ($version['warning']): ?>
					<p class="bg-warning message text-warning">
						<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
						<?php echo h($version['message']); ?>
					</p>
				<?php else: ?>
					<p class="bg-success message text-success">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
						<?php echo h($version['message']); ?>
					</p>
				<?php endif; ?>
			<?php endforeach ?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo __d('install', 'Versions(CLI)'); ?>
		</div>
		<div>
			<?php foreach ($cliVersions as $version): ?>
				<?php if ($version['error']): ?>
					<p class="bg-danger message text-danger">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
						<?php echo h($version['message']); ?>
					</p>
				<?php elseif ($version['warning']): ?>
					<p class="bg-warning message text-warning">
						<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
						<?php echo h($version['message']); ?>
					</p>
				<?php else: ?>
					<p class="bg-success message text-success">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
						<?php echo h($version['message']); ?>
					</p>
				<?php endif; ?>
			<?php endforeach ?>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo __d('install', 'Permissions'); ?>
		</div>
		<div>
			<?php foreach ($permissions as $permission): ?>
				<?php if ($permission['error']): ?>
					<p class="bg-danger message text-danger">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
						<?php echo h($permission['message']); ?>
					</p>
				<?php else: ?>
					<p class="bg-success message text-success">
						<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
						<?php echo h($permission['message']); ?>
					</p>
				<?php endif; ?>
			<?php endforeach ?>
		</div>
	</div>

	<button class="btn btn-lg btn-primary btn-block" type="submit"<?php echo ($canInstall ? '' : ' disabled="disabled"'); ?>>
		<?php echo __d('install', 'Next'); ?>
	</button>

<?php echo $this->NetCommonsForm->end();
