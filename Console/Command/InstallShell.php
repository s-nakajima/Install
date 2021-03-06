<?php
/**
 * Installシェル
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Shell', 'Console');
App::uses('AppShell', 'Console/Command');

/**
 * Installシェル
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Console\Command
 */
class InstallShell extends AppShell {

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 */
	public $tasks = array(
		'Install.InstallStart',
		'Install.CheckLibVersion',
		'Install.InstallPermission',
		'Install.CreateDatabase',
		'Install.InstallMigrations',
		'Install.InstallBower',
		'Install.SaveAdministrator',
		'Install.InstallSiteSetting',
		'Install.SaveInitData',
		'Install.InstallFinish'
	);

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 */
	public $executeWeight = array(
		'InstallStart',
		'CheckLibVersion',
		'InstallPermission',
		'CreateDatabase',
		'InstallMigrations',
		'InstallBower',
		'SaveAdministrator',
		'InstallSiteSetting',
		'SaveInitData',
		'InstallFinish'
	);

/**
 * Override startup
 *
 * @return void
 */
	public function startup() {
		$this->hr();
		$this->out(__d('install', 'NetCommons Install'));
		$this->hr();
	}

/**
 * Override main
 *
 * @return void
 */
	public function main() {
		if (Configure::read('NetCommons.installed')) {
			return $this->error(__d('install', 'Already installed.'));
		}

		$this->out(__d('install', '[S]tart'));
		$this->out(__d('install', '[H]elp'));
		$this->out(__d('install', '[Q]uit'));

		$choice = strtolower(
			$this->in(__d('net_commons', 'What would you like to do?'), ['S', 'H', 'Q'], 'Q')
		);
		switch ($choice) {
			case 's':
				foreach ($this->executeWeight as $task) {
					$this->$task->execute();
				}

				$this->out('<success>' . __d('install', 'Install success.') . '</success>');
				return $this->_stop();
			case 'h':
				$this->out($this->getOptionParser()->help());
				break;
			case 'q':
				return $this->_stop();
			default:
				$this->out(
					__d('net_commons', 'You have made an invalid selection. Please choose a command to execute by entering %s.', '[S, H, Q]')
				);
		}
		$this->hr();
	}

/**
 * Get the option parser.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('install', 'NetCommons Install'))
			->addSubcommand('install_start', array(
				'help' => __d('install', 'Install Step 1'),
				'parser' => $this->InstallStart->getOptionParser(),
			))
			->addSubcommand('check_lib_version', array(
				'help' => __d('install', 'Install Step 2(1)'),
				'parser' => $this->CheckLibVersion->getOptionParser(),
			))
			->addSubcommand('install_permission', array(
				'help' => __d('install', 'Install Step 2(2)'),
				'parser' => $this->InstallPermission->getOptionParser(),
			))
			->addSubcommand('create_database', array(
				'help' => __d('install', 'Install Step 3'),
				'parser' => $this->CreateDatabase->getOptionParser(),
			))
			->addSubcommand('install_migrations', array(
				'help' => __d('install', 'Install Step 4'),
				'parser' => $this->InstallMigrations->getOptionParser(),
			))
			->addSubcommand('install_bower', array(
				'help' => __d('install', 'Install Step 5'),
				'parser' => $this->InstallBower->getOptionParser(),
			))
			->addSubcommand('save_administrator', array(
				'help' => __d('install', 'Install Step 6'),
				'parser' => $this->SaveAdministrator->getOptionParser(),
			))
			->addSubcommand('install_site_setting', array(
				'help' => __d('install', 'Install Step 7'),
				'parser' => $this->InstallSiteSetting->getOptionParser(),
			))
			->addSubcommand('save_init_data', array(
				'help' => __d('install', 'Install Step 8'),
				'parser' => $this->SaveInitData->getOptionParser(),
			))
			->addSubcommand('install_finish', array(
				'help' => __d('install', 'Install End'),
				'parser' => $this->InstallFinish->getOptionParser(),
			));
	}

}
