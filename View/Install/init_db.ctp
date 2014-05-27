<?php echo $this->element('scripts'); ?>
<?php echo $this->Form->create(false,
			array(
				'url' => array(
					'plugin' => 'install',
					'controller' => 'install',
					'action' => 'init_db'))) ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __('Database Settings') ?></div>
		<div class="panel-body">
			<label class="datasource"><?php echo __('Datasource') ?></label>
			<div>
			<?php echo $this->Form->select('datasource',
						array(
							'Database/Mysql' => 'Mysql',
							'Database/Postgres' => 'Postgresql'),
						array(
							'default' => $defaultDB['datasource'],
							'empty' => false,
							'class' => '')) ?>
			</div>
			<?php echo $this->Form->input('host',
						array(
							'default' => $defaultDB['host'],
							'class' => 'form-control')) ?>
			<?php echo $this->Form->input('port',
						array(
							'default' => $defaultDB['port'],
							'class' => 'form-control')) ?>
			<?php echo $this->Form->input('database',
						array(
							'default' => 'nc3',
							'class' => 'form-control')) ?>
			<?php echo $this->Form->input('prefix',
						array(
							'placeholder' => 'nc3_',
							'class' => 'form-control')) ?>
			<?php echo $this->Form->input('login',
						array(
							'label' => __('ID'),
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
