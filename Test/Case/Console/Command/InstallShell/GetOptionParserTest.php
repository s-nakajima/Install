<?php
/**
 * InstallShell::getOptionParser()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsConsoleTestCase', 'NetCommons.TestSuite');

/**
 * InstallShell::getOptionParser()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Test\Case\Console\Command\InstallShell
 */
class InstallConsoleCommandInstallShellGetOptionParserTest extends NetCommonsConsoleTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'install';

/**
 * Shell name
 *
 * @var string
 */
	protected $_shellName = 'InstallShell';

/**
 * getOptionParser()のテスト
 *
 * @return void
 */
	public function testGetOptionParser() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadShell($shell, 'h');

		//事前準備
		$tasks = array(
			'InstallStart', 'InstallPermission', 'CreateDatabase', 'InstallMigrations',
			'InstallBower', 'SaveAdministrator', 'InstallFinish'
		);
		foreach ($tasks as $task) {
			$this->$shell->$task = $this->getMock($task,
					array('getOptionParser'), array(), '', false);
			$this->$shell->$task->expects($this->once())->method('getOptionParser')
				->will($this->returnValue(true));
		}

		//テスト実施
		$result = $this->$shell->getOptionParser();

		//チェック
		$this->assertEquals('ConsoleOptionParser', get_class($result));

		//サブタスクヘルプのチェック
		$expected = array();
		$actual = array();
		$subCommands = array(
			'install_start' => __d('install', 'Install Step 1'),
			'install_permission' => __d('install', 'Install Step 2'),
			'create_database' => __d('install', 'Install Step 3'),
			'install_migrations' => __d('install', 'Install Step 4'),
			'install_bower' => __d('install', 'Install Step 5'),
			'save_administrator' => __d('install', 'Install Step 6'),
			'install_finish' => __d('install', 'Install End')
		);
		foreach ($subCommands as $subCommand => $helpMessage) {
			$expected[] = $subCommand . ' ' . $helpMessage;
			$actual[] = $result->subcommands()[$subCommand]->help(strlen($subCommand) + 1);
		}
		$this->assertEquals($expected, $actual);
	}

}
