<?php
/**
 * InstallUtil::installApplicationYaml()のテスト
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
 * InstallUtil::installApplicationYaml()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Test\Case\Utility\InstallUtil
 */
class InstallUtilityInstallUtilInstallApplicationYamlTest extends NetCommonsCakeTestCase {

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
		$this->__prepare();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		$this->__prepare();
		parent::tearDown();
	}

/**
 * 事前準備
 *
 * @return void
 */
	private function __prepare() {
		$instance = new InstallUtil(true);
		if (file_exists(APP . 'Config' . DS . $instance->appYmlPrefix . 'application.yml')) {
			unlink(APP . 'Config' . DS . $instance->appYmlPrefix . 'application.yml');
		}
	}

/**
 * installApplicationYaml()の引数省略テスト
 *
 * @return void
 */
	public function testEmptyData() {
		//事前準備
		$instance = new InstallUtil(true);
		$this->assertFalse(file_exists(APP . 'Config' . DS . $instance->appYmlPrefix . 'application.yml'));

		//テストデータ
		$data = array();

		//テスト実施
		$result = $instance->installApplicationYaml($data);

		//チェック
		$this->assertEquals(Configure::read('Config.language'), 'ja');
		$this->assertEquals(Configure::read('Config.languageEnabled'), ['en', 'ja']);
		$this->assertEquals(Configure::read('NetCommons.installed'), false);

		$this->assertTrue($result);
		$this->assertTrue(file_exists(APP . 'Config' . DS . $instance->appYmlPrefix . 'application.yml'));
	}

/**
 * installApplicationYaml()の引数ありテスト
 *
 * @return void
 */
	public function testArgumentsData() {
		//事前準備
		$instance = new InstallUtil(true);
		$this->assertFalse(file_exists(APP . 'Config' . DS . $instance->appYmlPrefix . 'application.yml'));

		//テストデータ
		$data = array(
			'language' => 'en',
			'languageEnabled' => ['ja'],
		);

		//テスト実施
		$result = $instance->installApplicationYaml($data);

		//チェック
		$this->assertEquals(Configure::read('Config.language'), 'en');
		$this->assertEquals(Configure::read('Config.languageEnabled'), ['ja']);
		$this->assertEquals(Configure::read('NetCommons.installed'), false);

		$this->assertTrue($result);
		$this->assertTrue(file_exists(APP . 'Config' . DS . $instance->appYmlPrefix . 'application.yml'));
	}

}
