<h1><?php echo __('Installed') ?></h1>

<p><?php echo __('By clicking the button, you can jump to the home.')?></p>
<?php echo $this->Form->create('User',
			array(
				'url' => '/')) ?>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Home') ?></button>
<?php echo $this->Form->end();
