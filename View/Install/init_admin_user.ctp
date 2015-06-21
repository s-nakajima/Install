<?php echo $this->element('scripts'); ?>
<?php echo $this->Form->create('User',
			array(
				'url' => array(
					'plugin' => 'install',
					'controller' => 'install',
					'action' => 'init_admin_user'))) ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __d('install', 'Create an Administrator') ?></div>
		<div class="panel-body">
			<div class="form-group">
				<?php echo $this->Form->input('username',
							array(
								'default' => 'system_administrator',
								'class' => 'form-control',
								'placeholder' => __d('install', 'Username'))) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('handlename',
							array(
								'label' => __d('install', 'Handle Name'),
								'default' => 'system_administrator',
								'class' => 'form-control',
								'placeholder' => __d('install', 'Handle Name'))) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('password',
							array(
								'class' => 'form-control',
								'placeholder' => __d('install', 'Password')
							)) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('password_again',
							array(
								'type' => 'password',
								'class' => 'form-control',
								'placeholder' => __d('install', 'Password')
							)) ?>
			</div>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __d('install', 'Next') ?></button>
<?php echo $this->Form->end();
