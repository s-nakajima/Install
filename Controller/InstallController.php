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
		'password' => 'root',
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
		/* $this->layout = false; */
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
		$this->__saveDBConf();
		if ($this->request->is('post')) {
			$this->redirect(array('action' => 'init_db'));
		}
	}

/**
 * Step 2
 * Initialize db
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 **/
	public function init_db() {
		$this->set('defaultDB', $this->defaultDB);
		if ($this->request->is('post')) {
			$this->__saveDBConf();

			App::uses('ConnectionManager', 'Model');
			try {
				$db = ConnectionManager::getDataSource('default');

				// Remove malicious chars
				$database = preg_replace('/[^a-zA-Z0-9_\-]/', '', 'nc3');
				$encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', 'utf8');
				$db->rawQuery(
					sprintf('CREATE DATABASE IF NOT EXISTS `%s` /*!40100 DEFAULT CHARACTER SET %s */', $database, $encoding)
				);
			} catch (Exception $e) {
				$this->Session->setFlash($e->getMessage());
//				$this->Session->setFlash(__('Failed to connect database. Please, try again.'));
				$this->redirect(array('action' => 'init_db'));
			}

			// Invoke all available migrations
			$plugins = App::objects('plugins');
			foreach ($plugins as $plugin) {
				exec(sprintf('cd /var/www/app && app/Console/cake Migrations.migration run all -p %s', $plugin));
			}
			$this->redirect(array('action' => 'init_admin_user'));
		}
	}

/**
 * Step 3
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
					$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
				}
			}
			$this->redirect(array('action' => __FUNCTION__));
		}
	}

/**
 * Step 4
 * Last page of installation
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 **/
	public function finish() {
		$packages = array(
			'netcommons/auth:dev-master',
			'netcommons/auth-general:dev-master',
			'netcommons/users:dev-master',
			'netcommons/pages:dev-master',
			'netcommons/revision:dev-master',
			'netcommons/announcements:dev-master',
			'netcommons/boxes:dev-master',
			'netcommons/containers:dev-master',
			'netcommons/frames:dev-master',
			'netcommons/public-space:dev-master',
			'netcommons/theme-settings:dev-master',
			'netcommons/sandbox:dev-master',
		);
		echo str_replace(array("\r\n","\r","\n"), '<br />', system('export COMPOSER_HOME=/tmp && cd /var/www/app && composer require ' . implode(' ', $packages) . ' --dev 2>&1'));

		$this->__saveAppConf();
	}

/**
 * Save application configurations
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 **/
	private function __saveAppConf() {
		Configure::write('Security.salt', Security::generateAuthKey());
		Configure::write('Security.cipherSeed', mt_rand(32, 32));
		Configure::write('NetCommons.installed', true);
		/* Configure::write('NetCommons.installed', false); */

		App::uses('File', 'Utility');
		$file = new File(APP . 'Config' . DS . 'application.yml', true);
		$file->write(Spyc::YAMLDump(Configure::read()));
	}

/**
 * Save database configurations
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return void
 **/
	private function __saveDBConf() {
		$conf = file_get_contents(APP . 'Config' . DS . 'database.php.install');
		$params = array_merge($this->defaultDB, $this->request->data);
		foreach ($params as $key => $value) {
			$value = ($value === null) ? 'null' : $value;
			$value = ($value === true) ? 'true' : $value;
			$value = ($value === false) ? 'false' : $value;
			$conf = str_replace(sprintf('{%s}', $key), $value, $conf);
		}

		App::uses('File', 'Utility');
		$file = new File(APP . 'Config' . DS . 'database.php', true);
		$file->write($conf);
	}
}
