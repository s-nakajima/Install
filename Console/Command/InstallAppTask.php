<?php
/**
 * Install App Task
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppShell', 'Console/Command');
App::uses('InstallUtil', 'Install.Utility');

/**
 * Install App Task
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Console\Command
 */
class InstallAppTask extends AppShell {

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		$this->InstallUtil = new InstallUtil();
	}
}
