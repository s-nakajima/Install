<?php echo $this->Form->create(false,
			array(
				'url' => array(
					'plugin' => 'install',
					'controller' => 'install',
					'action' => 'init_db'))) ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __('Database Settings') ?></div>
		<div class="panel-body">
			<?php echo $this->Form->input('host',
						array(
							'default' => $defaultDB['host'],
							'class' => 'form-control')) ?>
			<?php echo $this->Form->input('port',
						array(
							'default' => $defaultDB['port'],
							/* 'default' => '3306', */
							'class' => 'form-control')) ?>
			<?php echo $this->Form->input('database',
						array(
							'default' => 'nc3',
							'class' => 'form-control')) ?>
			<?php echo $this->Form->input('login',
						array(
							'class' => 'form-control',
							'default' => $defaultDB['login'],
							'placeholder' => __('Username'))) ?>
			<?php echo $this->Form->input('password',
						array(
							'class' => 'form-control',
							'placeholder' => __('Password')
						)) ?>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Next') ?></button>
<?php echo $this->Form->end() ?>
