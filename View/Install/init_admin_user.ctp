<?php echo $this->Form->create('User',
			array(
				'url' => array(
					'plugin' => 'install',
					'controller' => 'install',
					'action' => 'init_admin_user'))) ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo _('Create an Administrator') ?></div>
		<div class="panel-body">
			<?php echo $this->Form->input('username',
						array(
							'default' => 'admin',
							'class' => 'form-control',
							'placeholder' => _('Username'))) ?>
			<?php echo $this->Form->input('password',
						array(
							'class' => 'form-control',
							'placeholder' => _('Password')
						)) ?>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Next') ?></button>
<?php echo $this->Form->end() ?>
