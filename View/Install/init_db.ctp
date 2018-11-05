<?php
/**
 * データベース設定
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->NetCommonsHtml->script('/install/js/install.js');
echo $this->NetCommonsHtml->css('/install/css/install.css');
?>

<div class="loader hidden text-center">
	<?php echo $this->Html->image('/net_commons/img/loader.gif', array('plugin' => false)); ?>
</div>

<?php foreach ($errors as $error): ?>
	<div class="alert alert-danger alert-dismissable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<?php echo $error; ?>
	</div>
<?php endforeach; ?>
<?php
	echo $this->NetCommonsForm->create(false,
		array(
			'url' => array(
				//'plugin' => 'install',
				//'controller' => 'install',
				'action' => 'init_db',
				'?' => ['language' => Configure::read('Config.language')]
			),
			'id' => 'InitDbForm'
		)
	);
?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __d('install', 'Database Settings'); ?></div>
		<div class="panel-body">
			<div class="form-group form-inline">
				<?php echo $this->NetCommonsForm->input('datasource',
						array(
							'type' => 'select',
							'options' => array(
								'Database/Mysql' => 'Mysql',
								//'Database/Postgres' => 'Postgresql'
							),
							'empty' => false,
							'label' => __d('install', 'Datasource'),
							'div' => false,
							'error' => false,
							'help' => __d('install', 'Select the type of database server to use.'),
						)
					); ?>
			</div>
			<div class="form-group">
				<?php echo $this->NetCommonsForm->input('host',
						array(
							'label' => __d('install', 'Host'),
							'default' => $masterDB['host'],
							'div' => false,
							'error' => false,
							'help' => __d('install', "Enter the host name of the database server to use. If you do not understand well, there is almost no problem as 'localhost'."),
						)
					); ?>
				<?php if (Hash::get($validationErrors, 'host')) : ?>
					<div class="has-error">
						<?php foreach ($validationErrors['host'] as $message) : ?>
							<div class="help-block">
								<?php echo h($message); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<?php echo $this->NetCommonsForm->input('port',
						array(
							'label' => __d('install', 'Port'),
							'default' => $masterDB['port'],
							'div' => false,
							'error' => false,
							'help' => __d('install', "Enter the port number of the database server to use. If you do not understand well, there is almost no problem as '3306'."),
						)
					); ?>
				<?php if (Hash::get($validationErrors, 'port')) : ?>
					<div class="has-error">
						<?php foreach ($validationErrors['port'] as $message) : ?>
							<div class="help-block">
								<?php echo h($message); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<?php echo $this->NetCommonsForm->input('database',
						array(
							'label' => __d('install', 'Database'),
							'default' => 'nc3',
							'div' => false,
							'error' => false,
							'help' => __d('install', 'Enter the database name to use.'),
						)
					); ?>
				<?php if (Hash::get($validationErrors, 'database')) : ?>
					<div class="has-error">
						<?php foreach ($validationErrors['database'] as $message) : ?>
							<div class="help-block">
								<?php echo h($message); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="form-group">

			<?php echo $this->NetCommonsForm->hidden('schema',
					array(
						'value' => 'public',
					)
				); ?>

			<div class="form-group">
				<?php echo $this->NetCommonsForm->input('prefix',
						array(
							'label' => __d('install', 'Prefix'),
							'placeholder' => 'nc3_',
							'div' => false,
							'error' => false,
							'help' => __d('install', "Table prefix of the database. This prefix is added to each table name to prevent duplication of names with existing tables. If you do not understand well, it is 'blank' and there is almost no problem."),
						)
					); ?>
				<?php if (Hash::get($validationErrors, 'prefix')) : ?>
					<div class="has-error">
						<?php foreach ($validationErrors['prefix'] as $message) : ?>
							<div class="help-block">
								<?php echo h($message); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<?php echo $this->NetCommonsForm->input('login',
						array(
							'label' => __d('install', 'ID'),
							'default' => $masterDB['login'],
							'placeholder' => __d('install', 'Username'),
							'div' => false,
							'error' => false,
							'help' => __d('install', 'User name of the database. Please enter the user account name in the above database.'),
						)
					); ?>
				<?php if (Hash::get($validationErrors, 'login')) : ?>
					<div class="has-error">
						<?php foreach ($validationErrors['login'] as $message) : ?>
							<div class="help-block">
								<?php echo h($message); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<?php echo $this->NetCommonsForm->input('password',
						array(
							'label' => __d('install', 'Password'),
							'placeholder' => __d('install', 'Password'),
							'div' => false,
							'error' => false,
							'help' => __d('install', 'Enter the password with the above ID.'),
						)
					); ?>
				<?php if (Hash::get($validationErrors, 'password')) : ?>
					<div class="has-error">
						<?php foreach ($validationErrors['password'] as $message) : ?>
							<div class="help-block">
								<?php echo h($message); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit">
		<?php echo __d('install', 'Next'); ?>
	</button>
<?php echo $this->NetCommonsForm->end();
