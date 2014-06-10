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
 * test init_db validation w/ invalid datasource
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitDBValidationWithInvalidRequest() {
		$this->testAction('/install/init_db', array(
			'data' => array(
				'DatabaseConfiguration' => array(
					'datasource' => 'Database/Sqlite',
					'persistent' => false,
					'port' => '3306',
					'host' => 'localhost',
					'login' => 'root',
					'password' => 'root',
					'database' => 'nc3',
					'prefix' => '',
					'encoding' => 'utf8',
				),
			),
		));
		$this->assertTrue(isset($this->controller->DatabaseConfiguration->validationErrors['datasource']));
	}

/**
 * test __createDB() fail w/ invalid port number
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testCreateDBFailWithInvalidRequest() {
		$this->testAction('/install/init_db', array(
			'data' => array(
				'DatabaseConfiguration' => array(
					'datasource' => 'Database/Mysql',
					'persistent' => false,
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
 * test init_db redirects to init_admin_user w/ valid request
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitDBRedirectsToInitAdminUserWithValidMysql() {
		$this->testAction('/install/init_db', array(
			'data' => array(
				'DatabaseConfiguration' => $this->controller->chooseDBByEnvironment(),
			),
		));
		$this->assertEqual($this->headers['Location'], Router::url('/install/init_admin_user', true));
	}
}
