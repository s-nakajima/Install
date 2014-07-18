<?php
App::uses('InstallAppController', 'Install.Controller');
/**
 * Apply array_filter() recursively
 *
 * @param mixed $input input value
 * @param callback $callback callback
 * @return void
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 **/

function __arrayFilterRecursive($input, $callback = null) {
	foreach ($input as &$value) {
		if (is_array($value)) {
			$value = __arrayFilterRecursive($value, $callback);
		}
	}
	return array_filter($input, $callback);
}

/**
 * Install Controller
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class InstallController extends InstallAppController {

	public $helpers = array('M17n.M17n');

/**
 * Master configuration
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $masterDBMysql = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'port' => 3306,
		'login' => 'root',
		'password' => 'root',
		'database' => 'nc3',
		'prefix' => '',
		'schema' => '',
		'encoding' => 'utf8',
	);

/**
 * Master configuration
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $masterDBPostgresql = array(
		'datasource' => 'Database/Postgres',
		'persistent' => false,
		'host' => 'localhost',
		'port' => 5432,
		'login' => 'postgres',
		'password' => 'postgres',
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
		'schema' => '',
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
 * Master configuration
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $masterTestDBMysql = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => 'localhost',
		'port' => 3306,
		'login' => 'test',
		'password' => 'test',
		'database' => 'test_nc3',
		'prefix' => '',
		'schema' => '',
		'encoding' => 'utf8',
	);

/**
 * Master configuration
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $masterTestDBPostgresql = array(
		'datasource' => 'Database/Postgres',
		'persistent' => false,
		'host' => 'localhost',
		'port' => 5432,
		'login' => 'postgres',
		'password' => 'postgres',
		'database' => 'test_nc3',
		'prefix' => '',
		'schema' => 'public',
		'encoding' => 'utf8',
	);

/**
 * DB configuration for travis
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $travisTestDBMysql = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => '0.0.0.0',
		'port' => 3306,
		'login' => 'travis',
		'password' => '',
		'database' => 'cakephp_test',
		'prefix' => '',
		'schema' => '',
		'encoding' => 'utf8',
	);

/**
 * DB configuration for travis
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public $travisTestDBPostgresql = array(
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
 * @codeCoverageIgnore
 **/
	public function index() {
		// Initialize master database connection
		if (!$this->__saveDBConf($this->chooseDBByEnvironment())) {
			$this->Session->setFlash(
				__('Failed to write %s. Please check permission.',
				array(APP . 'Config' . DS . 'database.php'))
			);
			return;
		}

		// Initialize application.yml
		Configure::write('Security.salt', Security::generateAuthKey());
		Configure::write('Security.cipherSeed', mt_rand() . mt_rand() . mt_rand() . mt_rand());
		Configure::write('Config.languageEnabled', array('en', 'ja', 'zh'));
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
		// Destroy session in order to handle ping request
		$this->Session->destroy();

		$this->set('masterDB', $this->chooseDBByEnvironment());
		$this->set('errors', array());
		if ($this->request->is('post')) {
			$this->loadModel('DatabaseConfiguration');
			$this->DatabaseConfiguration->set($this->request->data);
			if ($this->DatabaseConfiguration->validates()) {
				// Update database connection
				$this->__saveDBConf($this->request->data['DatabaseConfiguration']);
			} else {
				$this->response->statusCode(400);
				CakeLog::info(sprintf('Validation error: %s',
				implode(', ', array_keys($this->DatabaseConfiguration->validationErrors))));
				return;
			}

			if (!$this->__createDB()) {
				$this->response->statusCode(400);
				CakeLog::info('Failed to create database');
				return;
			}

			// Install packages
			if (!$this->__installPackages()) {
				CakeLog::error('Failed to install dependencies');
				return;
			}

			// Invoke all available migrations
			CakeLog::info('[Migrations.migration] Start migrating all plugins');
			$connections = array('master');
			$plugins = array_merge(
				App::objects('plugins'),
				array_map('basename', glob(ROOT . DS . 'app' . DS . 'Plugin' . DS . '*', GLOB_ONLYDIR))
			);
			foreach ($connections as $connection) {
				foreach ($plugins as $plugin) {
					exec(sprintf(
						'cd %s && app/Console/cake Migrations.migration run all -p %s -c %s -i %s',
						ROOT, $plugin, $connection, $connection
					));
					CakeLog::info(sprintf('[Migrations.migration] Migrated %s for %s connection', $plugin, $connection));
				}
			}
			CakeLog::info('[Migrations.migration] Successfully migrated all plugins');
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
		if ($this->request->is('post')) {
			$this->loadModel('Users.User');
			$this->User->create($this->request->data);
			if ($this->User->validates() && $this->User->saveAdmin($this->request->data)) {
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
		Configure::write('NetCommons.installed', true);
		/* Configure::write('NetCommons.installed', false); */
		$this->__saveAppConf();
	}

/**
 * Choose database configuration by environment
 *
 * @param string $env environment
 * @return array Database configuration
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @codeCoverageIgnore
 **/
	public function chooseDBByEnvironment($env = '') {
		$db = isset($_SERVER['TRAVIS']) ? 'travis' . ucfirst($env) . 'DB' : 'master' . ucfirst($env) . 'DB';

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
 * @SuppressWarnings(PHPMD.NPathComplexity)
 **/
	private function __saveDBConf($configs = array()) {
		$conf = file_get_contents(APP . 'Config' . DS . 'database.php.install');

		$params = array_merge($this->chooseDBByEnvironment(), $configs);
		foreach ($params as $key => $value) {
			$value = ($value === null) ? 'null' : $value;
			$value = ($value === true) ? 'true' : $value;
			$value = ($value === false) ? 'false' : $value;
			$conf = str_replace(sprintf('{master_%s}', $key), $value, $conf);
		}

		$params = $this->chooseDBByEnvironment('test');
		foreach ($params as $key => $value) {
			$value = ($value === null) ? 'null' : $value;
			$value = ($value === true) ? 'true' : $value;
			$value = ($value === false) ? 'false' : $value;
			$conf = str_replace(sprintf('{test_%s}', $key), $value, $conf);
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
							// Validation blocks following lines to be executed
							// @codeCoverageIgnoreStart
					default:
							CakeLog::error(sprintf('Unknown datasource %s', $configuration['datasource']));
							return false;
							// @codeCoverageIgnoreEnd
			}
			$db = new PDO(
				"{$driver}:host={$configuration['host']};port={$configuration['port']}",
				$configuration['login'],
				$configuration['password']
			);
			CakeLog::info(sprintf('DB Connected'));

			foreach (array('master', 'test') as $env) {
				if ($env === 'test') {
					$params = $this->chooseDBByEnvironment('test');
					$database = $params['database'];
					$encoding = $params['encoding'];
				} else {
					// Remove malicious chars
					$database = preg_replace('/[^a-zA-Z0-9_\-]/', '', $configuration['database']);
					/* $encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', $configuration['encoding']); */
					$encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', 'utf8');
				}
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
								// Validation blocks following lines to be executed
								// @codeCoverageIgnoreStart
						default:
								CakeLog::error(sprintf('Unknown datasource %s', $configuration['datasource']));
								return false;
								// @codeCoverageIgnoreEnd
				}
				CakeLog::info(sprintf('Database %s for %s created successfully', $database, $configuration['datasource']));
			}
		} catch (Exception $e) {
			CakeLog::error($e->getMessage());
			$this->set('errors', array($e->getMessage()));
			return false;
		}

		return true;
	}

/**
 * Install packages
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return boolean Install succeed or not
 **/
	private function __installPackages() {
		// Use hhvm only if php version greater than 5.5.0 and hhvm installed
		// @see https://github.com/facebook/hhvm/wiki/OSS-PHP-Frameworks-Unit-Testing
		$gt55 = version_compare(phpversion(), '5.5.0', '>=');
		exec('which hhvm', $messages, $ret);
		$hhvm = ($gt55 && $ret === 0) ? 'hhvm -vRepo.Central.Path=/var/run/hhvm/hhvm.hhbc' : '';

		$cmd = sprintf('export COMPOSER_HOME=/tmp && cd %s && cp tools/build/app/cakephp/composer.json . && %s `which composer` update 2>&1', ROOT, $hhvm);
		exec($cmd, $messages, $ret);

		// Write logs
		foreach ($messages as $message) {
			CakeLog::info(sprintf('[composer] %s', $message));
		}
		if ($ret !== 0) {
			$this->response->statusCode(500);
			$this->set('errors', array_merge($this->viewVars['errors'], $messages));
			return false;
		}

		return true;
	}
}
