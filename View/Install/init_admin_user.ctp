<?php echo $this->Form->create('User',
			array(
				'url' => array(
					'plugin' => 'install',
					'controller' => 'install',
					'action' => 'init_admin_user'))) ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __('Create an Administrator') ?></div>
		<div class="panel-body">
			<div class="form-group">
				<?php echo $this->Form->input('username',
							array(
								'default' => 'admin',
								'class' => 'form-control',
								'placeholder' => __('Username'))) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('handlename',
							array(
								'label' => __('Handle Name'),
								'default' => 'admin',
								'class' => 'form-control',
								'placeholder' => __('Handle Name'))) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('password',
							array(
								'class' => 'form-control',
								'placeholder' => __('Password')
							)) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('password_again',
							array(
								'type' => 'password',
								'class' => 'form-control',
								'placeholder' => __('Password')
							)) ?>
			</div>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Next') ?></button>
<?php echo $this->Form->end() ?>
