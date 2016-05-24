<?php
/**
 * Installの開始
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('InstallAppTask', 'Install.Console/Command');

/**
 * Migrationの実行
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Console\Command
 */
class InstallBowerTask extends InstallAppTask {

/**
 * Override startup
 *
 * @return void
 */
	public function startup() {
		$this->hr();
		$this->out(__d('install', 'NetCommons Install Step 5'));
		$this->hr();
	}

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		parent::execute();

		if (! $this->InstallUtil->installBowerPackages(false)) {
			return $this->error(__d('install', 'Failed to install bower packages.'));
		}
	}

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser;
	}
}
