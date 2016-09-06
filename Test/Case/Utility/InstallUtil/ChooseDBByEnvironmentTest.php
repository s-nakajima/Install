<?php
/**
 * InstallUtil::chooseDBByEnvironment()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');
App::uses('InstallUtil', 'Install.Utility');

/**
 * InstallUtil::chooseDBByEnvironment()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Test\Case\Utility\InstallUtil
 */
class InstallUtilityInstallUtilChooseDBByEnvironmentTest extends NetCommonsCakeTestCase {

/**
 * Fixture merge
 *
 * @var array
 */
	protected $_isFixtureMerged = false;

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'install';

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		if (isset($_SERVER['TRAVIS'])) {
			unset($_SERVER['TRAVIS']);
		}

		parent::tearDown();
	}

/**
 * chooseDBByEnvironment()のテスト
 *
 * @return void
 */
	public function testChooseDBByEnvironment() {
		//データ生成
		$env = '';

		//テスト実施
		$instance = new InstallUtil();
		$result = $instance->chooseDBByEnvironment($env);

		//チェック
		//@codeCoverageIgnoreStart
		if (isset($_SERVER['TRAVIS'])) {
			$expected = array(
				'datasource' => 'Database/Mysql',
				'persistent' => false,
				'host' => '0.0.0.0',
				'port' => 3306,
				'login' => 'travis',
				'password' => '',
				'database' => 'cakephp_test',
				'prefix' => '',
				'schema' => '',
				'encoding' => 'utf8',
			);
		} else {
			$expected = array(
				'datasource' => 'Database/Mysql',
				'persistent' => false,
				'host' => 'localhost',
				'port' => 3306,
				'login' => 'root',
				'password' => 'root',
				'database' => 'nc3',
				'prefix' => '',
				'schema' => '',
				'encoding' => 'utf8',
			);
		}
		//@codeCoverageIgnoreEnd

		$this->assertEquals($expected, $result);
	}

/**
 * chooseDBByEnvironment('test')のテスト
 *
 * @return void
 */
	public function testEnvTest() {
		//データ生成
		$env = 'test';

		//テスト実施
		$instance = new InstallUtil();
		$result = $instance->chooseDBByEnvironment($env);

		//チェック
		if (isset($_SERVER['TRAVIS'])) {
			$expected = array(
				'datasource' => 'Database/Mysql',
				'persistent' => false,
				'host' => '0.0.0.0',
				'port' => 3306,
				'login' => 'travis',
				'password' => '',
				'database' => 'cakephp_test',
				'prefix' => '',
				'schema' => '',
				'encoding' => 'utf8',
			);
		} else {
			$expected = array(
				'datasource' => 'Database/Mysql',
				'persistent' => false,
				'host' => 'localhost',
				'port' => 3306,
				'login' => 'test',
				'password' => 'test',
				'database' => 'test_nc3',
				'prefix' => '',
				'schema' => '',
				'encoding' => 'utf8',
			);
		}
		$this->assertEquals($expected, $result);
	}

}
