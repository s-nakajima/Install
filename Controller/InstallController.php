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

	public $helpers = array('M17n.M17n');

/**
 * Default configuration
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $defaultDB = array(
		'datasource' => 'Database/Mysql',
		'persistent' => true,
		'host' => 'localhost',
		'port' => 3306,
		'login' => 'root',
		'password' => '',
		'database' => '',
		'prefix' => '',
		'encoding' => 'utf8',
	);

/**
 * beforeFilter
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 **/
	public function beforeFilter() {
		$this->Auth->allow();
		$this->layout = 'Install.default';
		parent::beforeFilter();
	}

/**
 * Step 1
 * Index
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 **/
	public function index() {
		// Initialize default database connection
		if (!$this->__saveDBConf()) {
			$this->Session->setFlash(
				__('Failed to write %s. Please check permission.',
				array(APP . 'Config' . DS . 'database.php'))
			);
		}
		if ($this->request->is('post')) {
			$this->redirect(array('action' => 'init_permission'));
		}
	}

/**
 * Step 2
 * Initialize permission
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @throws Exception Permission Error
 * @return void
 **/
	public function init_permission() {
		// Check permissions
		$permissions = array();
		$ret = true;
		if (is_writable(APP . 'Config')) {
			$permissions[] = __('OK: %s', array(APP . 'Config'));
		} else {
			$ret = false;
			$permissions[] = __('Failed to write %s. Please check permission.', array(APP . 'Config'));
		}
		if (is_writable(APP . 'tmp')) {
			$permissions[] = __('OK: %s', array(APP . 'tmp'));
		} else {
			$ret = false;
			$permissions[] = __('Failed to write %s. Please check permission.', array(APP . 'tmp'));
		}

		// Show current page on failure
		if (!$ret) {
			$this->redirect(array('action' => 'init_permission'));
		}

		if ($this->request->is('post')) {
			$this->redirect(array('action' => 'init_db'));
		}
		$this->set('permissions', $permissions);
	}

/**
 * Step 3
 * Initialize db
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 **/
	public function init_db() {
		$this->set('defaultDB', $this->defaultDB);
		if ($this->request->is('post')) {
			// Initialize database connection w/o database name
			$this->__saveDBConf(array(
				'host' => $this->request->data['host'],
				'port' => $this->request->data['port'],
				'login' => $this->request->data['login'],
				'password' => $this->request->data['password'],
			));

			App::uses('ConnectionManager', 'Model');
			try {
				$db = ConnectionManager::getDataSource('default');
				CakeLog::info(sprintf('DB Connected'), true);

				// Remove malicious chars
				$database = preg_replace('/[^a-zA-Z0-9_\-]/', '', $this->request->data['database']);
				/* $encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', $this->request->data['encoding']); */
				$encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', 'utf8');
				$db->rawQuery(
					sprintf('CREATE DATABASE IF NOT EXISTS `%s` /*!40100 DEFAULT CHARACTER SET %s */', $database, $encoding)
				);
				CakeLog::info(sprintf('Database %s created successfully', $database), true);
			} catch (Exception $e) {
				$this->Session->setFlash($e->getMessage());
				$this->redirect(array('action' => 'init_db'));
			}

			// Update database connection w/ database name
			$this->__saveDBConf();

			// Invoke all available migrations
			$plugins = App::objects('plugins');
			foreach ($plugins as $plugin) {
				exec(sprintf('cd /var/www/app && app/Console/cake Migrations.migration run all -p %s', $plugin));
				CakeLog::info(sprintf('Migrated %s', $plugin), true);
			}
			$this->redirect(array('action' => 'init_admin_user'));
		}
	}

/**
 * Step 4
 * Initialize administrator account
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 **/
	public function init_admin_user() {
		if ($this->request->is('post')) {
			$this->loadModel('Users.User');
			if ($this->request->is('post')) {
				$this->User->create();
				if ($this->User->save($this->request->data)) {
					return $this->redirect(array('action' => 'finish'));
				} else {
					$this->Session->setFlash(__('The user could not be saved. Please try again.'));
				}
			}
			$this->redirect(array('action' => __FUNCTION__));
		}
	}

/**
 * Step 5
 * Last page of installation
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 **/
	public function finish() {
		$packages = array(
			'netcommons/auth:dev-master',
			'netcommons/auth-general:dev-master',
			'netcommons/pages:dev-master',
			'netcommons/announcements:dev-master',
			'netcommons/boxes:dev-master',
			'netcommons/containers:dev-master',
			'netcommons/frames:dev-master',
			'netcommons/public-space:dev-master',
			'netcommons/theme-settings:dev-master',
			'netcommons/sandbox:dev-master',
		);
		echo system('export COMPOSER_HOME=/tmp && cd /var/www/app && composer require ' . implode(' ', $packages) . ' --dev 2>&1');

		$this->__saveAppConf();
	}

/**
 * Save application configurations
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return boolean File written or not
 **/
	private function __saveAppConf() {
		Configure::write('Security.salt', Security::generateAuthKey());
		Configure::write('Security.cipherSeed', mt_rand(32, 32));
		Configure::write('NetCommons.installed', true);
		/* Configure::write('NetCommons.installed', false); */

		App::uses('File', 'Utility');
		$file = new File(APP . 'Config' . DS . 'application.yml', true);
		return $file->write(Spyc::YAMLDump(Configure::read()));
	}

/**
 * Save database configurations
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return boolean File written or not
 **/
	private function __saveDBConf($configs = array()) {
		$configs = $configs ? : $this->request->data;
		$conf = file_get_contents(APP . 'Config' . DS . 'database.php.install');
		$params = array_merge($this->defaultDB, $configs);

		foreach ($params as $key => $value) {
			$value = ($value === null) ? 'null' : $value;
			$value = ($value === true) ? 'true' : $value;
			$value = ($value === false) ? 'false' : $value;
			$conf = str_replace(sprintf('{%s}', $key), $value, $conf);
		}

		App::uses('File', 'Utility');
		$file = new File(APP . 'Config' . DS . 'database.php', true);
		return $file->write($conf);
	}
}
