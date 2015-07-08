<?php echo $this->element('scripts') ?>
<div class="loader hidden text-center">
	<div class="progress">
		<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
			<span class="sr-only"></span>
		</div>
	</div>

</div>
<?php foreach ($errors as $error): ?>
	<div class="alert alert-danger alert-dismissable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<?php echo $error ?>
	</div>
<?php endforeach; ?>
<?php echo $this->Form->create('DatabaseConfiguration',
			array(
				'url' => array(
					'plugin' => 'install',
					'controller' => 'install',
					'action' => 'init_db'))) ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __d('install', 'Database Settings') ?></div>
		<div class="panel-body">
			<label class="datasource"><?php echo __d('install', 'Datasource') ?></label>
			<div class="form-group">
				<?php echo $this->Form->select('datasource',
							array(
								'Database/Mysql' => 'Mysql',
								'Database/Postgres' => 'Postgresql'),
							array(
								'empty' => false,
								'class' => '')) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('host',
							array(
								'default' => $masterDB['host'],
								'class' => 'form-control')) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('port',
							array(
								'default' => $masterDB['port'],
								'class' => 'form-control')) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('database',
							array(
								'default' => 'nc3',
								'class' => 'form-control')) ?>
			</div>
			<div class="form-group <?php echo isset($this->request->data['datasource']) && $this->request->data['datasource'] === 'Database/Postgres' ? '' : 'none' ?>">
				<?php echo $this->Form->input('schema',
							array(
								'default' => 'public',
								'class' => 'form-control')) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('prefix',
							array(
								'placeholder' => 'nc3_',
								'class' => 'form-control')) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('login',
							array(
								'label' => __d('install', 'ID'),
								'class' => 'form-control',
								'default' => $masterDB['login'],
								'placeholder' => __d('install', 'Username'))) ?>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('password',
							array(
								'class' => 'form-control',
								'placeholder' => __d('install', 'Password')
							)) ?>
			</div>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __d('install', 'Next') ?></button>
<?php echo $this->Form->end();
