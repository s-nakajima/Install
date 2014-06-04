<?php echo $this->element('scripts'); ?>
<?php echo $this->Form->create(false,
				array(
					'url' => array(
						'plugin' => 'install',
						'controller' => 'install',
						'action' => 'index'))) ?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo __('Term') ?></div>
		<div class="panel-body">
			<div class="form-group">
				<?php echo $this->M17n->languages('language',
							array(
								'label' => '',
								'div' => array('class' => 'text-right'))) ?>
			</div>
			<div class="form-group">
			<?php echo $this->Form->textarea('term',
						array(
							'default' => __('The terms & conditions for using the contents of this site is governed
by this agreement. Please read carefully the following conditions, and
register only if you agree to them.

By using this site, I agree to refrain from the following actions, or
behavior that may lead to the following actions.

actions that are against public order or morals
actions that are against the laws or ordinances
criminal acts or actions connected to criminal acts
actions that violate rights of other users, third party, or this site
actions that slander, defame, or cause the loss of prestige or
credibility of other users, third party, or this site
actions that result in liability to other users, third party, or this site
actions that hinder the operation of this site
actions that disseminate information that are not true
postings of personal information that may lead to invasion of privacy
other actions that are deemed unsuitable by this site

Disclaimer

This site is not responsible for damage (direct or indirect) to user
that is caused by, is resulted from the connection of, the usage of
this site, contents related to this site, services from links stemming
from this site, etc.'),
							'class' => 'form-control')) ?>
			</div>
		</div>
	</div>
	<p><?php echo __('By clicking the button, you agree to the terms above.')?></p>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo __('Next') ?></button>
<?php echo $this->Form->end() ?>
