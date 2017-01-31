<?php
/**
 * サイト設定
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->NetCommonsHtml->script('/install/js/install.js');
echo $this->NetCommonsHtml->css('/install/css/install.css');
?>

<?php foreach ($errors as $error): ?>
	<div class="alert alert-danger alert-dismissable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<?php echo $error; ?>
	</div>
<?php endforeach; ?>

<?php
	echo $this->Form->create(false,
		array(
			'url' => array(
				//'plugin' => 'install',
				//'controller' => 'install',
				'action' => 'init_site_setting',
				'?' => ['language' => Configure::read('Config.language')]
			),
			'novalidate' => true
		)
	);
?>

	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __d('site_manager', 'Please select the language to use.'); ?></div>
		<div class="panel-body">
			<?php echo $this->NetCommonsForm->input('Language.code', array(
				'type' => 'checkbox',
				'multiple' => true,
				'options' => $defaultLangs,
				'default' => $activeLangs,
				'hiddenField' => false,
				'error' => false,
			)); ?>

			<?php if (isset($validationErrors['Language.code'])) : ?>
				<div class="has-error">
					<?php foreach ($validationErrors['Language.code'] as $error): ?>
						<div class="help-block">
							<?php echo $error; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __d('install', 'Next'); ?></button>
<?php echo $this->Form->end();
