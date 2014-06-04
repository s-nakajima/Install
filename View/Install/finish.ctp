<?php if ($succeed): ?>
<h1><?php echo __('Installed') ?></h1>
<?php else: ?>
<h1><?php echo __('Install Failed') ?></h1>
<?php endif; ?>

<?php if ($succeed): ?>
<p><?php echo __('By clicking the button, you can jump to the home.')?></p>
<?php echo $this->Form->create('User',
			array(
				'url' => '/')) ?>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Home') ?></button>
<?php echo $this->Form->end() ?>
<?php else: ?>
<ul class="list-unstyled">
	<?php foreach($messages as $message): ?>
	<li><?php echo $message ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

