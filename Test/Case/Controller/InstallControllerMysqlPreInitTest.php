<?php
/**
 * InstallController Test Case
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('InstallController', 'Controller');

/**
 * Summary for InstallController Test Case
 */
class InstallControllerMysqlPreInitTest extends ControllerTestCase {

/**
 * setUp
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function setUp() {
		parent::setUp();
		$this->InstallController = $this->generate('Install.Install', array(
			'components' => array(
				'Auth' => array('user'),
				'Session',
			),
		));
		$this->controller->plugin = 'Install';

		foreach (array('app/Config/database.php', 'app/Config/application.yml') as $conf) {
			if (file_exists($conf)) {
				unlink($conf);
			}
		}
	}

/**
 * test index GET
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testIndexGet() {
		$this->testAction('/install/index', array('method' => 'get'));
		$this->assertEqual($this->InstallController->view, 'index');
	}

/**
 * test index redirects to init_permission
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testIndexRedirectsToInitPermission() {
		$this->testAction('/install/index', array(
			'data' => array(
			),
		));
		$this->assertEqual($this->headers['Location'], Router::url('/install/init_permission', true));
	}

/**
 * test init_permission GET
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitPermissionGet() {
		$this->testAction('/install/init_permission', array('method' => 'get'));
		$this->assertEqual($this->InstallController->view, 'init_permission');
	}

/**
 * test init_permission redirects to init_db
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitPermissionRedirectsToInitDB() {
		$this->testAction('/install/init_permission', array(
			'data' => array(
			),
		));
		$this->assertEqual($this->headers['Location'], Router::url('/install/init_db', true));
	}

/**
 * test init_db GET
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitDBGet() {
		$this->testAction('/install/init_db', array('method' => 'get'));
		$this->assertEqual($this->InstallController->view, 'init_db');
	}

/**
 * test init_db validation w/ invalid request
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitDBValidationWithInvalidRequest() {
		$invalid = array(
			'datasource' => 'Database/Sqlite',
			'persistent' => -1,
			'port' => -1,
			'host' => false,
			'login' => false,
			'password' => '',
			'database' => false,
			'prefix' => false,
			'encoding' => false,
		);
		$this->testAction('/install/init_db', array(
			'data' => array(
				'DatabaseConfiguration' => $invalid,
			),
		));
		$this->assertTrue(isset($this->controller->DatabaseConfiguration->validationErrors['datasource']));
		$this->assertTrue(isset($this->controller->DatabaseConfiguration->validationErrors['persistent']));
		$this->assertTrue(isset($this->controller->DatabaseConfiguration->validationErrors['database']));
		$this->assertTrue(isset($this->controller->DatabaseConfiguration->validationErrors['host']));
		$this->assertTrue(isset($this->controller->DatabaseConfiguration->validationErrors['login']));
	}

/**
 * test InstallController::__createDB() fails w/ invalid port number
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testCreateDBFailsWithInvalidRequest() {
		$this->testAction('/install/init_db', array(
			'data' => array(
				'DatabaseConfiguration' => array(
					'datasource' => 'Database/Mysql',
					'persistent' => '0',
					'port' => '0',
					'host' => 'localhost',
					'login' => 'root',
					'password' => 'root',
					'database' => 'nc3',
					'prefix' => '',
					'encoding' => 'utf8',
				),
			),
		));
		$this->assertTextEquals('init_db', $this->InstallController->view);
	}

/**
 * testComposerFailure
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testComposerFailure() {
		exec('chmod ug-w composer.json');
		$this->testAction('/install/init_db', array(
			'data' => array(
				'DatabaseConfiguration' => array_merge(
					$this->controller->chooseDBByEnvironment(),
					array('persistent' => '0')
				),
			),
		));
		$this->assertEqual($this->InstallController->view, 'init_db');
		exec('chmod ug+w composer.json');
	}

/**
 * test init_db redirects to init_admin_user w/ valid request
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitDBRedirectsToInitAdminUserWithValidMysql() {
		$this->testAction('/install/init_db', array(
			'data' => array(
				'DatabaseConfiguration' => array_merge(
					$this->controller->chooseDBByEnvironment(),
					array('persistent' => '0')
				),
			),
		));

		debug(Router::url('/install/init_admin_user', true));
		debug($this->headers['Location']);

		$this->assertEqual($this->headers['Location'], Router::url('/install/init_admin_user', true));
	}
}
