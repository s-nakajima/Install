<?php
App::uses('InstallAppController', 'Install.Controller');
/**
 * Install Controller
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Apply array_filter() recursively
 *
 * @param mixed $input input value
 * @param callback $callback callback
 * @return void
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @codeCoverageIgnore
 **/
function __arrayFilterRecursive($input, $callback = null) {
	foreach ($input as &$value) {
		if (is_array($value)) {
			$value = __arrayFilterRecursive($value, $callback);
		}
	}
	return array_filter($input, $callback);
}

class InstallController extends InstallAppController {

	public $helpers = array('M17n.M17n');

	/* public $uses = array('Users.User'); */

/**
 * Default configuration
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $defaultDBMysql = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'port' => 3306,
		'login' => 'root',
		'password' => 'root',
		'database' => 'nc3',
		'prefix' => '',
		'encoding' => 'utf8',
	);

/**
 * Default configuration
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $defaultDBPostgresql = array(
		'datasource' => 'Database/Postgres',
		'persistent' => false,
		'host' => 'localhost',
		'port' => 5432,
		'login' => 'postgres',
		'password' => '',
		'database' => 'nc3',
		'prefix' => '',
		'schema' => 'public',
		'encoding' => 'utf8',
	);

/**
 * DB configuration for travis
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $travisDBMysql = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => '0.0.0.0',
		'port' => 3306,
		'login' => 'travis',
		'password' => '',
		'database' => 'cakephp_test',
		'prefix' => '',
		'encoding' => 'utf8',
	);

/**
 * DB configuration for travis
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $travisDBPostgresql = array(
		'datasource' => 'Database/Postgres',
		'persistent' => false,
		'host' => 'localhost',
		'port' => 5432,
		'login' => 'postgres',
		'password' => 'postgres',
		'database' => 'cakephp_test',
		'prefix' => '',
		'schema' => 'public',
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
		if (!$this->__saveDBConf($this->chooseDBByEnvironment())) {
			$this->Session->setFlash(
				__('Failed to write %s. Please check permission.',
				array(APP . 'Config' . DS . 'database.php'))
			);
			return;
		}

		// Initialize application.yml
		Configure::write('Security.salt', Security::generateAuthKey());
		Configure::write('Security.cipherSeed', mt_rand() . mt_rand());
		Configure::write('NetCommons.installed', false);
		if (!$this->__saveAppConf()) {
			$this->Session->setFlash(
				__('Failed to write %s. Please check permission.',
				array(APP . 'Config' . DS . 'application.yml'))
			);
			return;
		}

		if ($this->request->is('post')) {
			return $this->redirect(array('action' => 'init_permission'));
		}
	}

/**
 * Step 2
 * Initialize permission
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 * @codeCoverageIgnore
 **/
	public function init_permission() {
		// Check permissions
		$permissions = array();
		$ret = true;
		// Actually we don't have to check app/Config and app/tmp here,
		// since cakephp itself cannot handle requests w/o these directories with proper permission.
		// Just a stub action for future release.
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
				CakeLog::error($permission['message']);
			}
			return $this->redirect(array('action' => 'init_permission'));
		}

		if ($this->request->is('post')) {
			return $this->redirect(array('action' => 'init_db'));
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
		$this->set('defaultDB', $this->chooseDBByEnvironment());
		if ($this->request->is('post')) {
			$this->loadModel('DatabaseConfiguration');
			$this->DatabaseConfiguration->set($this->request->data);
			if ($this->DatabaseConfiguration->validates()) {
				// Update database connection
				$this->__saveDBConf($this->request->data['DatabaseConfiguration']);
			} else {
				CakeLog::info('Validation error');
				CakeLog::info(var_export($this->DatabaseConfiguration->validationErrors, true));
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
				exec(sprintf('cd %s && app/Console/cake Migrations.migration run all -p %s', ROOT, $plugin));
				CakeLog::info(sprintf('[Migrations.migration] Migrated %s', $plugin), true);
			}
			CakeLog::info('[Migrations.migration] Successfully migrated all plugins', true);
			return $this->redirect(array('action' => 'init_admin_user'));
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
		/* $db =& ConnectionManager::getDataSource('default'); */
		/* $db->setConfig(array('password' => 'root', 'database' => 'nc3', 'persistent' => false)); */
		/* $db =& ConnectionManager::getDataSource('test'); */
		/* $db->setConfig(array('password' => 'root', 'database' => 'nc3', 'persistent' => false)); */
		/* CakeLog::info(var_export($db, true)); */
		/* $db->setConfig(array('password' => 'root', 'database' => $config['database'], 'persistent' => false)); */
		/* ClassRegistry::init('ConnectionManager'); */
		/* App::uses('ConnectionManager', 'Model'); */
		/* $db = ConnectionManager::drop('default'); */
		/* $db->_init = false; */
		/* ConnectionManager::_init(); */
		/* $db = ConnectionManager::create('default'); */
		/* unset($db); */
		if ($this->request->is('post')) {
			$this->loadModel('Users.User');
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				return $this->redirect(array('action' => 'finish'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please try again.'));
			}
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
		// Install packages
		$cmd = sprintf('export COMPOSER_HOME=/tmp && cd %s && cp tools/build/app/cakephp/composer.json . && composer update 2>&1', ROOT);
		exec($cmd, $messages, $ret);

		// Write logs
		foreach ($messages as $message) {
			CakeLog::info(sprintf('[composer] %s', $message));
		}

		if ($ret === 0) {
			// Update application.yml on success
			Configure::write('NetCommons.installed', true);
			$this->__saveAppConf();
		} else {
			CakeLog::error('Failed to install dependencies');
		}
		$this->set('succeed', $ret === 0);
		$this->set('messages', $messages);
	}

/**
 * Choose database configuration by environment
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return array Database configuration
 * @codeCoverageIgnore
 **/
	public function chooseDBByEnvironment() {
		$db = isset($_SERVER['TRAVIS']) ? 'travisDB' : 'defaultDB';

		if (isset($_SERVER['DB'])) {
			if ($_SERVER['DB'] === 'pgsql') {
				$db .= 'Postgresql';
			} else {
				$db .= 'Mysql';
			}
		} else {
			$db .= 'Mysql';
		}

		return $this->$db;
	}

/**
 * Save application configurations
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return boolean File written or not
 **/
	private function __saveAppConf() {
		App::uses('File', 'Utility');
		$file = new File(APP . 'Config' . DS . 'application.yml', true);
		$conf = __arrayFilterRecursive(Configure::read(), function($val){
			return !is_object($val);
		});
		return $file->write(Spyc::YAMLDump($conf));
	}

/**
 * Save database configurations
 *
 * @param array $configs configs
 * @return boolean File written or not
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 **/
	private function __saveDBConf($configs = array()) {
		$conf = file_get_contents(APP . 'Config' . DS . 'database.php.install');
		$params = array_merge($this->defaultDBMysql, $configs);

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
			CakeLog::info(sprintf('DB Connected'));

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
