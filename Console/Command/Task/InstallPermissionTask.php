<?php
/**
 * Installのパーミッション設定
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('InstallAppTask', 'Install.Console/Command');

/**
 * Installのパーミッション設定
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Console\Command
 */
class InstallPermissionTask extends InstallAppTask {

/**
 * ApacheのオプションKey
 *
 * @var string
 */
	const KEY_APACHE_OWNER = 'owner';

/**
 * Override startup
 *
 * @return void
 */
	public function startup() {
		$this->hr();
		$this->out(__d('install', 'NetCommons Install Step 2'));
		$this->hr();
	}

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		parent::execute();

		//引数のセット
		if (isset($this->params[self::KEY_APACHE_OWNER])) {
			$owner = Hash::get($this->params, self::KEY_APACHE_OWNER);

			$writables = array(
				APP . 'Config',
				APP . 'tmp',
				ROOT . DS . 'composer.json',
				ROOT . DS . 'bower.json'
			);
			foreach ($writables as $file) {
				$messages = array();
				$ret = null;
				$cmd = sprintf('`which chown` %s -R %s 2>&1', $owner, $file);
				exec($cmd, $messages, $ret);
			}
		}
	}

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(__d('install', 'NetCommons Install Step 2'))
			->addOption(self::KEY_APACHE_OWNER, array(
				'help' => __d('install', 'Apache owner'),
				'required' => false
			));

		return $parser;
	}
}
