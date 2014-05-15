<?php

Router::connect('/install/:action', array(
	'plugin' => 'install', 'controller' => 'install'
));
