<?php
/**
 * Installの終了
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('InstallAppTask', 'Install.Console/Command');
App::uses('Folder', 'Utility');

/**
 * Installの終了
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Console\Command
 */
class InstallFinishTask extends InstallAppTask {

/**
 * ApacheのオプションKey
 *
 * @var string
 */
	const KEY_APACHE_OWNER = 'owner';

/**
 * releaseのオプションKEY
 *
 * @var string
 */
	const KEY_RELEASE = 'release';

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
		if (array_key_exists(self::KEY_RELEASE, $this->params)) {
			$path = ROOT . DS . 'app' . DS . 'Plugin' . DS;
			$plugins = array_unique(array_merge(
				App::objects('plugins'), array_map('basename', glob($path . '*', GLOB_ONLYDIR))
			));

			$folder = new Folder();
			foreach ($plugins as $plugin) {
				$folder->delete($path . $plugin . DS . '.git');
			}
			$folder->delete(ROOT . DS . '.git');
			$folder->delete(ROOT . DS . '.chef');
		}

		//Configure::write('NetCommons.installed', true);
		$this->InstallUtil->saveAppConf();
	}

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(__d('install', 'NetCommons Install End'))
			->addOption(self::KEY_APACHE_OWNER, array(
				'help' => __d('install', 'Apache owner'),
				'required' => false
			))
			->addOption(self::KEY_RELEASE, array(
				'help' => __d('install', 'Release type. Deleting ".git" and ".chef" directory'),
				'required' => false
			));

		return $parser;
	}
}
