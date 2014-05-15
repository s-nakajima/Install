index

	<?php echo $this->Form->create('User',
					array(
						'url' => array(
							'plugin' => 'install',
							'controller' => 'install',
							'action' => 'finish'))) ?>
		<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Install') ?></button>
	<?php echo $this->Form->end() ?>
