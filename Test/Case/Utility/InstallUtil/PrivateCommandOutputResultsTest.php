<?php
/**
 * InstallUtil::__commandOutputResults()のテスト
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
 * InstallUtil::__commandOutputResults()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Test\Case\Utility\InstallUtil
 */
class InstallUtilityInstallUtilPrivateCommandOutputResultsTest extends NetCommonsCakeTestCase {

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
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//ログ出力のMock
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Install', 'TestInstall');
		CakeLog::config('TestMockLog', array('engine' => 'TestInstall.TestMock'));
	}

/**
 * __commandOutputResults()のdebug=1テスト
 *
 * @return void
 */
	public function testCommandOutputResults() {
		$instance = new InstallUtil(true);

		//データ生成
		$type = 'unit test';
		$messages = ['Unit test InstallUtil::__commandOutputResults().'];

		//テスト実施
		$this->_testReflectionMethod(
			$instance, '__commandOutputResults', array($type, $messages)
		);

		//チェック
		$logger = CakeLog::stream('TestMockLog');
		$expected = array(
			'Info: [unit test]   Unit test InstallUtil::__commandOutputResults().',
		);
		$this->assertEquals($expected, $logger->output);
	}

}
