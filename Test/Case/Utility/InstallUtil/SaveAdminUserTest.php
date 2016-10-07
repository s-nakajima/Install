<?php
/**
 * InstallUtil::saveAdminUser()のテスト
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
 * InstallUtil::saveAdminUser()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Test\Case\Utility\InstallUtil
 */
class InstallUtilityInstallUtilSaveAdminUserTest extends NetCommonsCakeTestCase {

/**
 * Fixture merge
 *
 * @var array
 */
	protected $_isFixtureMerged = false;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.m17n.language',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'install';

/**
 * saveAdminUser()のテスト
 *
 * @return void
 */
	public function testSaveAdminUser() {
		//事前準備
		$instance = new InstallUtil();
		$instance->User = $this->getMock(
			'User', array('saveUser'), array(), '', false
		);
		$instance->User->expects($this->once())->method('saveUser')->with(array(
			'User' => array(
				'username' => 'test_username',
				'password' => 'password_username',
				'password_again' => 'password_username',
				'handlename' => 'Test User',
				'role_key' => 'system_administrator',
				'status' => '1',
				'timezone' => 'Asia/Tokyo',
			),
			'UsersLanguage' => array(
				array('id' => null, 'language_id' => '1', 'name' => 'Test User'),
				array('id' => null, 'language_id' => '2', 'name' => 'Test User'),
			)
		));
		$instance->Language = ClassRegistry::init('M17n.Language');

		//データ生成
		$data = array(
			'User' => array(
				'username' => 'test_username',
				'password' => 'password_username',
				'password_again' => 'password_username',
				'handlename' => 'Test User',
			)
		);

		//テスト実施
		$instance->saveAdminUser($data);
	}

}
