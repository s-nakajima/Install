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
class InstallControllerPostgresqlPreInitTest extends ControllerTestCase {

/**
 * setUp
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function setUp() {
		parent::setUp();
		$this->controller = $this->generate('Install.Install', array(
			'components' => array(
				'Session',
			),
		));
		$this->controller->plugin = 'Install';

		foreach (array('app/Config/database.php', 'app/Config/application.yml') as $conf) {
			if (file_exists($conf)) {
				unlink($conf);
			}
		}
		$_SERVER['DB'] = 'pgsql';
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
 * test init_db redirects to init_admin_user w/ valid request
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testInitDBRedirectsToInitAdminUserWithValidPostgresql() {
		$this->testAction('/install/init_db', array(
			'data' => array(
				'DatabaseConfiguration' => $this->controller->chooseDBByEnvironment(),
			),
		));
		$this->assertEqual($this->headers['Location'], Router::url('/install/init_admin_user', true));
	}
}
