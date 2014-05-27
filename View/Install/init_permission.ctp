<?php echo $this->Form->create(false,
			array(
				'url' => array(
					'plugin' => 'install',
					'controller' => 'install',
					'action' => 'init_permission'))) ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __('Permissions') ?></div>
		<?php foreach ($permissions as $permission): ?>
			<div><?php echo h($permission) ?></div>
		<?php endforeach ?>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Next') ?></button>
<?php echo $this->Form->end() ?>
