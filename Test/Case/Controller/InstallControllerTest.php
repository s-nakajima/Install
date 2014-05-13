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
		'plugin.users.user',
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
			'components' => array(
				'Install' => array('user'),
				'Session',
			),
		));
		$this->controller->plugin = 'Install';
		$this->controller->Install
			->staticExpects($this->any())
			->method('user')
			->will($this->returnCallback(array($this, 'installUserCallback')));
	}

/**
 * testIndex action
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return   void
 */
	public function testIndex() {
		$this->testAction('/install/index');
		$this->assertEqual($this->headers['Location'], Router::url('/install/index', true));
	}
}
