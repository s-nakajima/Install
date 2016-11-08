<?php
/**
 * Install Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('InstallAppController', 'Install.Controller');
App::uses('InstallUtil', 'Install.Utility');
App::uses('InstallValidatorUtil', 'Install.Utility');

/**
 * Install Controller
 *
 * @package NetCommons\Install\Controlelr
 */
class InstallController extends InstallAppController {

/**
 * Helpers
 */
	public $helpers = array(
		'M17n.M17n',
	);

/**
 * beforeFilter
 *
 * @return void
 * @throws NotFoundException
 */
	public function beforeFilter() {
		if (Configure::read('NetCommons.installed')) {
			throw new NotFoundException();
		}
		$this->Auth->allow();
		$this->layout = 'Install.default';

		//テストのために必要
		if (substr(get_class($this->InstallUtil), 0, strlen('Mock_')) !== 'Mock_') {
			$this->InstallUtil = new InstallUtil();
		}

		if (isset($this->request->query['language'])) {
			Configure::write('Config.language', $this->request->query['language']);
			$this->Session->write('Config.language', $this->request->query['language']);
		} else {
			Configure::write('Config.language', 'ja');
			$this->Session->write('Config.language', 'ja');
		}

		$this->Components->unload('NetCommons.Permission');
	}

/**
 * ステップ 1
 * application.ymlの初期値セット
 *
 * @return void
 */
	public function index() {
		$this->set('pageTitle', __d('install', 'Term'));

		//// Initialize master database connection
		//$configs = $this->InstallUtil->chooseDBByEnvironment();
		//if (! $this->InstallUtil->saveDBConf($configs)) {
		//	$message = __d(
		//		'install',
		//		'Failed to write %s. Please check permission.',
		//		array(APP . 'Config' . DS . 'database.php')
		//	);
		//	$this->Session->setFlash($message);
		//	return;
		//}

		if (! $this->InstallUtil->installApplicationYaml($this->request->query)) {
			$message = __d(
				'install',
				'Failed to write %s. Please check permission.',
				array(APP . 'Config' . DS . 'application.yml')
			);
			$this->Session->setFlash($message);
			return;
		}

		if ($this->request->is('post')) {
			$this->redirect(array(
				'action' => 'init_permission',
				'?' => ['language' => Configure::read('Config.language')]
			));
		}
	}

/**
 * ステップ 2
 * パーミッションのチェック
 *
 * @return void
 */
	public function init_permission() {
		$this->set('pageTitle', __d('install', 'Permissions'));

		$validator = new InstallValidatorUtil();
		$ret = true;

		$versions = $validator->versions();
		$permissions = $validator->permissions();
		$messages = array_merge($versions, $permissions);
		foreach ($messages as $message) {
			if ($message['error']) {
				$ret = false;
				break;
			}
		}

		// Show current page on failure
		$this->set('versions', $versions);
		$this->set('permissions', $permissions);
		$this->set('canInstall', $ret);

		if (! $ret) {
			foreach ($messages as $output) {
				CakeLog::error($output['message']);
			}
			return;
		}

		if ($this->request->is('post')) {
			$this->redirect(array(
				'action' => 'init_db',
				'?' => ['language' => Configure::read('Config.language')]
			));
			return;
		}
	}

/**
 * ステップ 3
 * データベース設定
 *
 * @return void
 */
	public function init_db() {
		$this->set('pageTitle', __d('install', 'Database Settings'));

		// Destroy session in order to handle ping request
		$this->Session->destroy();

		$this->set('masterDB', $this->InstallUtil->chooseDBByEnvironment());
		$this->set('errors', array());
		$this->set('validationErrors', array());
		if ($this->request->is('post')) {
			set_time_limit(1800);

			if ($this->request->data['prefix'] &&
					substr($this->request->data['prefix'], -1, 1) !== '_') {
				$this->request->data['prefix'] .= '_';
			}

			if ($this->InstallUtil->validatesDBConf($this->request->data)) {
				$this->InstallUtil->saveDBConf($this->request->data);
			} else {
				$this->set('validationErrors', $this->InstallUtil->validationErrors);
				$this->response->statusCode(400);
				$this->set('errors', [
					__d('net_commons', 'Failed on validation errors. Please check the input data.')
				]);
				CakeLog::info('[ValidationErrors] ' . $this->request->here());
				if (Configure::read('debug')) {
					CakeLog::info(var_export($this->InstallUtil->validationErrors, true));
				}
				return;
			}

			if (! $this->InstallUtil->createDB($this->request->data)) {
				$this->response->statusCode(400);
				CakeLog::info('Failed to create database.');
				$this->set('errors', [__d('install', 'Failed to create database.')]);
				return;
			}

			// Install migrations
			$plugins = array_unique(array_merge(
				App::objects('plugins'),
				array_map('basename', glob(ROOT . DS . 'app' . DS . 'Plugin' . DS . '*', GLOB_ONLYDIR))
			));
			if (!$this->InstallUtil->installMigrations('master', $plugins)) {
				$this->response->statusCode(400);
				CakeLog::error('Failed to install migrations');
				$this->set('errors', [__d('install', 'Failed to install migrations.')]);
				return;
			}

			$this->redirect(array(
				'action' => 'init_admin_user',
				'?' => ['language' => Configure::read('Config.language')]
			));
		}
	}

/**
 * ステップ 4
 * 管理者アカウントの登録
 *
 * @return void
 */
	public function init_admin_user() {
		$this->set('pageTitle', __d('install', 'Create an Administrator'));

		if ($this->request->is('post')) {
			if (! $this->InstallUtil->saveAdminUser($this->request->data)) {
				$this->Session->setFlash(
					__d('install', 'The user could not be saved. Please try again.')
				);
				return;
			}

			$this->redirect(array(
				'action' => 'finish',
				'?' => ['language' => Configure::read('Config.language')]
			));
		}
	}

/**
 * ステップ 5
 * インストール終了
 *
 * @return void
 */
	public function finish() {
		$this->set('pageTitle', __d('install', 'Installed'));

		Configure::write('NetCommons.installed', true);
		/* Configure::write('NetCommons.installed', false); */
		$this->InstallUtil->saveAppConf();
	}

/**
 * Keep connection alive
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 */
	public function ping() {
		$this->set('result', array('message' => 'OK'));
		$this->set('_serialize', array('result'));
	}

}
