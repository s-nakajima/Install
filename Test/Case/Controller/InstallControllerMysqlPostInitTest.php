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
class InstallControllerMysqlPostInitTest extends ControllerTestCase {

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
				'Auth' => array('user'),
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
 * testFinishRedirectsToHome
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testFinishRedirectsToHome() {
		var_dump('fin');
		$this->testAction('/install/finish', array(
			'data' => array(
			),
		));
		$this->assertEqual($this->headers['Location'], Router::url('/', true));
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

/* /\** */
/*  * testInitPermissionInvisibleAfterInstallation */
/*  * */
/*  * @author   Jun Nishikawa <topaz2@m0n0m0n0.com> */
/*  * @return   void */
/*  *\/ */
/* 	public function testInitPermissionInvisibleAfterInstallation() { */
/* 		$this->setExpectedException('NotFoundException'); */
/* 		$this->testAction('/install/init_permission', array('method' => 'get')); */
/* 	} */

/* /\** */
/*  * testIndexInvisibleAfterInstallation */
/*  * */
/*  * @author   Jun Nishikawa <topaz2@m0n0m0n0.com> */
/*  * @return   void */
/*  *\/ */
/* 	public function testInitDBInvisibleAfterInstallation() { */
/* 		$this->setExpectedException('NotFoundException'); */
/* 		$this->testAction('/install/init_db', array('method' => 'get')); */
/* 	} */

/* /\** */
/*  * testInitAdminUserInvisibleAfterInstallation */
/*  * */
/*  * @author   Jun Nishikawa <topaz2@m0n0m0n0.com> */
/*  * @return   void */
/*  *\/ */
/* 	public function testInitAdminUserInvisibleAfterInstallation() { */
/* 		$this->setExpectedException('NotFoundException'); */
/* 		$this->testAction('/install/init_admin_user', array('method' => 'get')); */
/* 	} */

/* /\** */
/*  * testFinishInvisibleAfterInstallation */
/*  * */
/*  * @author   Jun Nishikawa <topaz2@m0n0m0n0.com> */
/*  * @return   void */
/*  *\/ */
/* 	public function testFinishInvisibleAfterInstallation() { */
/* 		$this->setExpectedException('NotFoundException'); */
/* 		$this->testAction('/install/finish', array('method' => 'get')); */
/* 	} */

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
