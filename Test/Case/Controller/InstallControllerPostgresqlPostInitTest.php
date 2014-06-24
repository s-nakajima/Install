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
class InstallControllerPostgresqlPostInitTest extends ControllerTestCase {

/**
 * setUp
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function setUp() {
		Configure::write('debug', 0);
		parent::setUp();
		$this->InstallController = $this->generate('Install.Install', array(
			'components' => array(
				'Session',
			),
		));
		$this->controller->plugin = 'Install';
	}

/**
 * test init_admin_user redirects to finish
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
 * testIndexInvisibleAfterInstallation
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 * @expectedException NotFoundException
 * @expectedExceptionCode 404
 */
	public function testIndexInvisibleAfterInstallation() {
		Configure::write('NetCommons.installed', true);
		$Install = new InstallController(new CakeRequest('/install/index', false), new CakeResponse());
		$Install->beforeFilter();
	}
}
