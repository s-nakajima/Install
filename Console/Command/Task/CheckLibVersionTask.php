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
class CheckLibVersionTask extends InstallAppTask {

/**
 * PHPバージョン
 * ※テストで変更できるようにメンバー変数にする
 *
 * @var array
 */
	public $phpVersion = PHP_VERSION;

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		parent::execute();

		$status = 0;
		if (version_compare($this->phpVersion, '5.4.0') >= 0) {
			$message = __d('install', '%s(%s) version success.', 'PHP', $this->phpVersion);
			$this->out($message);
		} else {
			$message = __d(
				'install',
				'%s(%s) version error. Please more than "%s" version.', 'PHP', $this->phpVersion, 'PHP 5.4'
			);
			$this->err('<error>Error:</error> ' . $message);
			$status = parent::CODE_ERROR;
		}

		$version = phpversion('pdo_mysql');
		if (! $version) {
			$message = __d('install', 'Not found the %s. Please install the %s.', 'pdo_mysql', 'pdo_mysql');
			$this->err('<error>Error:</error> ' . $message);
			$status = parent::CODE_ERROR;
		} else {
			$message = __d('install', '%s(%s) version success.', 'pdo_mysql', $version);
			$this->out($message);
		}

		$this->_stop($status);
	}

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(__d('install', 'NetCommons Install Step 2'));

		return $parser;
	}
}
