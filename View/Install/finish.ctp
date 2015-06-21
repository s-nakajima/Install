<h1><?php echo __d('install', 'Installed') ?></h1>

<p><?php echo __d('install', 'By clicking the button, you can jump to the home.')?></p>
<?php echo $this->Form->create('User',
			array(
				'url' => '/')) ?>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __d('install', 'Home') ?></button>
<?php echo $this->Form->end();
