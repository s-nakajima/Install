<?php
App::uses('InstallAppController', 'Install.Controller');
/**
 * Install Controller
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */
class InstallController extends InstallAppController {

/**
 * beforeFilter
 *
 * @return void
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 **/
	public function beforeFilter() {
		$this->Auth->allow();
		parent::beforeFilter();
	}

/**
 * index
 *
 * @return void
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 **/
	public function index() {
	}

/**
 * finish
 *
 * @return void
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 **/
	public function finish() {
	}
}
