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
 * Fixtures
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @var      array
 */
	public $fixtures = array(
		/* 'plugin.users.user', */
	);

/**
 * setUp
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function setUp() {
		parent::setUp();
		$this->InstallController = $this->generate('Install.Install', array(
		/* $this->controller = $this->generate('Install.Install', array( */
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
 * tearDown
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	/* public function tearDown() { */
	/* 	parent::tearDown(); */
	/* 	foreach (array('app/Config/database.php', 'app/Config/application.yml') as $conf) { */
	/* 		if (file_exists($conf)) { */
	/* 			unlink($conf); */
	/* 		} */
	/* 	} */
	/* } */

/**
 * testIndex GET
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	/* public function testGettableBeforeInstallation() { */
	/* 	foreach (self::$__actions as $action) { */
	/* 		$this->testAction(sprintf('/install/%s', $action), array('method' => 'get')); */
	/* 		$this->assertTextEquals($action, $this->InstallController->view); */
	/* 	} */
	/* } */

/**
 * test index redirects to init_permission
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	/* public function testIndexGet() { */
	/* 	$ret = $this->testAction('/install/index', array('method' => 'get')); */
	/* 	/\* var_dump($ret); *\/ */
	/* 	/\* var_dump($this->view); *\/ */
	/* 	/\* var_dump($this->headers); *\/ */
	/* 	$this->assertEqual($this->view, 'index'); */
	/* } */

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
 * test index redirects to init_permission
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
 * test index redirects to init_permission
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
 * test index redirects to init_permission
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
					'port' => '3305',
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
 * test index redirects to init_permission
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitDBRedirectsToInitAdminUserWithValidMysql() {
		$this->testAction('/install/init_db', array(
			'data' => array(
				/* 'DatabaseConfiguration' => array( */
				/* 	'datasource' => 'Database/Mysql', */
				/* 	'persistent' => false, */
				/* 	'port' => '3306', */
				/* 	'host' => 'localhost', */
				/* 	'login' => 'root', */
				/* 	'password' => 'root', */
				/* 	'database' => 'nc3', */
				/* 	'prefix' => '', */
				/* 	'encoding' => 'utf8', */
				/* ), */
				'DatabaseConfiguration' => $this->controller->chooseDBByEnvironment(),
			),
		));
		$this->assertEqual($this->headers['Location'], Router::url('/install/init_admin_user', true));
	}
}
