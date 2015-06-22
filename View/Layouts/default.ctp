<?php
$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>
		<?php
			if (isset($pageTitle)) {
				echo h($pageTitle);
			}
		?>
	</title>

	<?php
		echo $this->fetch('meta');

		echo $this->Html->css(
			array(
				'/components/bootstrap/dist/css/bootstrap.min.css',
				'/components/jqueryui/themes/overcast/jquery-ui.min.css',
				'/net_commons/css/style.css',
				'style'
			),
			array('plugin' => false)
		);
		echo $this->fetch('css');

		echo $this->Html->script(
			array(
				'/components/jquery/dist/jquery.min.js',
				'/components/jqueryui/jquery-ui.min.js',
				'/components/bootstrap/dist/js/bootstrap.min.js',
				'/net_commons/jquery.cookie.js'
			),
			array('plugin' => false)
		);
		echo $this->fetch('script');
	?>
</head>
	<body>

	<div class="container">
		<?php if ($flashMss = $this->Session->flash()) { ?>
			<!-- flash -->
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $flashMss ?>
			</div>
		<?php } ?>
		<?php echo $this->fetch('content'); ?>
	</div>

	<!-- container-footer  -->
	<footer id="nc-system-footer" role="contentinfo">
		<div class="box-footer box-id-5">
			<div class="copyright">Powered by NetCommons</div>
		</div>
	</footer>

		<!-- /container -->
		<?php echo $this->element('sql_dump'); ?>

	</body>
</html>
