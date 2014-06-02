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
class InstallControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @var      array
 */
	public $fixtures = array(
		/* 'plugin.users.user', */
	);

	/* private static $__actions = array('index', 'init_permission', 'init_db', 'init_admin_user', 'finish'); */

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
				'Session',
			),
		));
		$this->controller->plugin = 'Install';
		/* $this->controller->Install */
		/* 	->staticExpects($this->any()) */
		/* 	->method('user') */
		/* 	->will($this->returnCallback(array($this, 'installUserCallback'))); */
	}

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
	public function testInitPermissionRedirectsToInitDB2() {
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
	public function testInitDBRedirectsToInitAdminUserWithValidMysql() {
		$this->testAction('/install/init_db', array(
			'data' => array(
				'DatabaseConfiguration' => array(
					'datasource' => 'Database/Mysql',
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
		$this->assertEqual($this->headers['Location'], Router::url('/install/init_admin_user', true));
	}

/**
 * test index redirects to init_permission
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	/* public function testInitDBRedirectsToInitAdminUserWithValidPostgresql() { */
	/* 	$this->testAction('/install/init_db', array( */
	/* 		'data' => array( */
	/* 			'DatabaseConfiguration' => array( */
	/* 				'datasource' => 'Database/Postgres', */
	/* 				'persistent' => false, */
	/* 				'port' => '5432', */
	/* 				'host' => 'localhost', */
	/* 				'login' => 'postgres', */
	/* 				'password' => '', */
	/* 				'database' => 'nc3', */
	/* 				'prefix' => '', */
	/* 				'encoding' => 'utf8', */
	/* 			), */
	/* 		), */
	/* 	)); */
	/* 	$this->assertEqual($this->headers['Location'], Router::url('/install/init_admin_user', true)); */
	/* } */

/**
 * test index redirects to init_permission
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitAdminUserRedirectsToFinish() {
		$this->testAction('/install/init_admin_user', array(
			'data' => array(
				'User' => array(
					'username' => 'admin',
					'handlename' => 'admin',
					'password' => 'admin',
					'password_again' => 'admin',
				),
			),
		));
		$this->assertEqual($this->headers['Location'], Router::url('/install/finish', true));
	}

/**
 * test index redirects to init_permission
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	/* public function testInitDBRedirectsBackToSelfOnFailure() { */
	/* 	$this->testAction('/install/init_permission', array( */
	/* 		'data' => array( */
	/* 		), */
	/* 	)); */
	/* 	$this->assertEqual($this->headers['Location'], Router::url('/install/init_db', true)); */
	/* } */
}
