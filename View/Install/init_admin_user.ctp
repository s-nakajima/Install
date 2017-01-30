<?php
/**
 * システム管理者アカウントの登録
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->NetCommonsHtml->script('/install/js/install.js');
echo $this->NetCommonsHtml->css('/install/css/install.css');
?>

<?php
	echo $this->Form->create(false,
		array(
			'url' => array(
				//'plugin' => 'install',
				//'controller' => 'install',
				'action' => 'init_admin_user',
				'?' => ['language' => Configure::read('Config.language')]
			),
			'novalidate' => true
		)
	);
?>

	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __d('install', 'Create an Administrator'); ?></div>
		<div class="panel-body">
			<div class="form-group">
				<?php echo $this->Form->input('User.username',
							array(
								'default' => 'system_administrator',
								'class' => 'form-control',
								'placeholder' => __d('install', 'Username'),
								'error' => false,
							)); ?>
				<div class="has-error">
					<?php echo $this->Form->error('User.username', null, array(
							'class' => 'help-block'
						)); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('User.password',
							array(
								'class' => 'form-control',
								'placeholder' => __d('install', 'Password'),
								'error' => false,
							)); ?>
				<div class="has-error">
					<?php echo $this->Form->error('User.password', null, array(
							'class' => 'help-block'
						)); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('User.password_again',
							array(
								'type' => 'password',
								'class' => 'form-control',
								'placeholder' => __d('install', 'Password'),
								'error' => false,
							)); ?>
				<div class="has-error">
					<?php echo $this->Form->error('User.password_again', null, array(
							'class' => 'help-block'
						)); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->Form->input('User.handlename',
					array(
						'label' => __d('install', 'Handle Name'),
						'default' => __d('install', 'System administrator'),
						'class' => 'form-control',
						'placeholder' => __d('install', 'Handle Name'),
						'error' => false,
					)); ?>
				<div class="has-error">
					<?php echo $this->Form->error('User.handlename', null, array(
							'class' => 'help-block'
						)); ?>
				</div>
			</div>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __d('install', 'Next'); ?></button>
<?php echo $this->Form->end();
