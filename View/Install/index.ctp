<?php echo $this->Form->create(false,
				array(
					'url' => array(
						'plugin' => 'install',
						'controller' => 'install',
						'action' => 'index'))) ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo _('Term') ?></div>
		<div class="panel-body">
			<?php echo $this->Form->textarea('term',
						array(
							'default' => _('term term term'),
							'class' => 'form-control')) ?>
			<label class="checkbox">
				<?php echo $this->Form->checkbox('agree') ?>
				<?php echo _('Agree')?>
			</label>
		</div>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Next') ?></button>
<?php echo $this->Form->end() ?>
