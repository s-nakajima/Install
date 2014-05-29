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
 * @throws NotFoundException
 * @return void
 **/
	public function beforeFilter() {
		if (Configure::read('NetCommons.installed')) {
			throw new NotFoundException;
		}
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
 * @return void
 **/
	public function init_permission() {
		// Check permissions
		$permissions = array();
		$ret = true;
		if (is_writable(APP . 'Config')) {
			$permissions[] = array(
				'message' => __('%s is writable', array(APP . 'Config')),
				'error' => false,
			);
		} else {
			$ret = false;
			$permissions[] = array(
				'message' => __('Failed to write %s. Please check permission.', array(APP . 'Config')),
				'error' => true,
			);
		}
		if (is_writable(APP . 'tmp')) {
			$permissions[] = array(
				'message' => __('%s is writable', array(APP . 'tmp')),
				'error' => false,
			);
		} else {
			$ret = false;
			$permissions[] = array(
				'message' => __('Failed to write %s. Please check permission.', array(APP . 'tmp')),
				'error' => true,
			);
		}

		// Show current page on failure
		if (!$ret) {
			foreach ($permissions as $permission) {
				CakeLog::error($permission, true);
			}
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
			$this->loadModel('DatabaseConfiguration');
			$this->DatabaseConfiguration->set($this->request->data);
			if ($this->DatabaseConfiguration->validates()) {
				// Update database connection
				$this->__saveDBConf();
			} else {
				CakeLog::info('Validation error');
				return;
			}

			if (!$this->__createDB()) {
				CakeLog::info('Failed to create database');
				return;
			}

			// Invoke all available migrations
			CakeLog::info('[Migrations.migration] Start migrating all plugins', true);
			$plugins = App::objects('plugins');
			foreach ($plugins as $plugin) {
				exec(sprintf('cd /var/www/app && app/Console/cake Migrations.migration run all -p %s', $plugin));
				CakeLog::info(sprintf('[Migrations.migration] Migrated %s', $plugin), true);
			}
			CakeLog::info('[Migrations.migration] Successfully migrated all plugins', true);
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
		// Dependencies
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

		// Install packages
		$cmd = 'export COMPOSER_HOME=/tmp && cd /var/www/app && composer require ' . implode(' ', $packages) . ' --dev 2>&1';
		exec($cmd, $messages, $ret);

		// Write logs
		foreach ($messages as $line) {
			CakeLog::info(sprintf('[composer] %s', $line), true);
		}

		if ($ret === 0) {
			// Write application.yml on success
			$this->__saveAppConf();
		} else {
			CakeLog::error('Failed to install dependencies', true);
		}
		$this->set('succeed', $ret === 0);
		$this->set('messages', $messages);
	}

/**
 * Save application configurations
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return boolean File written or not
 **/
	private function __saveAppConf() {
		Configure::write('Security.salt', Security::generateAuthKey());
		Configure::write('Security.cipherSeed', mt_rand() . mt_rand());
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
		$configs = $configs ? : $this->request->data['DatabaseConfiguration'];
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

/**
 * Create database
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return boolean DB created or not
 **/
	private function __createDB() {
		try {
			$configuration = $this->request->data['DatabaseConfiguration'];
			switch ($configuration['datasource']) {
					case 'Database/Mysql':
							$driver = 'mysql';
							break;
					case 'Database/Postgres':
							$driver = 'pgsql';
							break;
					default:
							CakeLog::error(sprintf('Unknown datasource %s', $configuration['datasource']));
							return false;
			}
			$db = new PDO(
				"{$driver}:host={$configuration['host']};port={$configuration['port']}",
				$configuration['login'],
				$configuration['password']
			);
			CakeLog::info(sprintf('DB Connected'), true);

			// Remove malicious chars
			$database = preg_replace('/[^a-zA-Z0-9_\-]/', '', $configuration['database']);
			/* $encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', $configuration['encoding']); */
			$encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', 'utf8');
			switch ($configuration['datasource']) {
					case 'Database/Mysql':
							$db->query(
								sprintf('CREATE DATABASE IF NOT EXISTS `%s` /*!40100 DEFAULT CHARACTER SET %s */', $database, $encoding)
							);
							break;
					case 'Database/Postgres':
							$db->query(
								sprintf('CREATE DATABASE %s WITH ENCODING=\'%s\'', $database, strtoupper($encoding))
							);
							break;
					default:
							CakeLog::error(sprintf('Unknown datasource %s', $configuration['datasource']));
							return false;
			}
			CakeLog::info(sprintf('Database %s created successfully', $database));
		} catch (Exception $e) {
			CakeLog::error($e->getMessage());
			$this->Session->setFlash($e->getMessage());
			return false;
		}

		return true;
	}
}
