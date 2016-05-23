<?php
/**
 * Install Utility
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('CakePlugin', 'Core');
App::uses('File', 'Utility');

/**
 * Install Utility
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Utility
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Install {

/**
 * Master configuration
 *
 * @return array
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
 * @return array
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
 * @return array
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
 * @return array
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
 * @return array
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
 * @return array
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
 * @return array
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
 * @return array
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
 * コンストラクタ
 *
 * @return void
 */
	public function __construct() {
		Security::setHash('sha512');
	}

/**
 * Save application configurations
 *
 * @return bool File written or not
 */
	public function saveAppConf() {
		$file = new File(APP . 'Config' . DS . 'application.yml', true);
		$conf = Configure::read();
		return $file->write(Spyc::YAMLDump($conf));
	}

/**
 * サーバ環境によりDBコンフィグを選択する
 *
 * @param string $env environment
 * @return array Database configuration
 */
	public function chooseDBByEnvironment($env = '') {
		if (isset($_SERVER['TRAVIS'])) {
			$db = 'travis' . ucfirst($env) . 'DB';
		} else {
			$db = 'master' . ucfirst($env) . 'DB';
		}

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
 * database.phpの登録
 *
 * @param array $configs configs
 * @return bool File written or not
 */
	public function saveDBConf($configs = array()) {
		$conf = file_get_contents(APP . 'Config' . DS . 'database.php.install');

		$params = array_merge($this->chooseDBByEnvironment(), $configs);
		$conf = $this->__parseDBConf($conf, $params, 'master');

		$params = $this->chooseDBByEnvironment('test');
		$conf = $this->__parseDBConf($conf, $params, 'test');

		$file = new File(APP . 'Config' . DS . 'database.php', true);
		return $file->write($conf);
	}

/**
 * database.phpの登録
 *
 * @param array $conf コンフィグ値
 * @param array $params パラメータ
 * @param string $dbPrefix データベース
 * @return array コンフィグ値
 */
	private function __parseDBConf($conf, $params, $dbPrefix) {
		foreach ($params as $key => $value) {
			$value = ($value === null) ? 'null' : $value;
			$value = ($value === true) ? 'true' : $value;
			$value = ($value === false) ? 'false' : $value;
			$conf = str_replace(sprintf('{' . $dbPrefix . '_%s}', $key), $value, $conf);
		}

		return $conf;
	}

/**
 * application.ymlの初期値セット
 *
 * @param mixed $data 登録データ
 * @return bool
 */
	public function installApplicationYaml($data) {
		// phpDocumentor Settings
		// Put author name to netcommons.php or netcommons.yaml
		/* $author = 'Noriko Arai, Ryuji Masukawa'; */
		$author = 'Your Name <yourname@domain.com>';
		$header = <<<EOF
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author $author
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
EOF;

		Configure::write('PhpDocumentor.classHeader', $header);

		Configure::write('Security.salt', Security::generateAuthKey());
		Configure::write('Security.cipherSeed', mt_rand() . mt_rand() . mt_rand() . mt_rand());

		Configure::write('Config.languageEnabled', Hash::get($data, 'languageEnabled', ['en', 'ja']));
		Configure::write('Config.language', Hash::get($data, 'language', 'ja'));
		Configure::write('NetCommons.installed', false);

		return $this->saveAppConf();
	}

/**
 * データベースの作成
 *
 * @param array $configuration DBの設定値
 * @return bool DB created or not
 * @throws Exception
 */
	public function createDB($configuration) {
		try {
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

			foreach (array('master', 'test') as $env) {
				if ($env === 'test') {
					$params = $this->chooseDBByEnvironment('test');
					$database = $params['database'];
					$encoding = $params['encoding'];
				} else {
					// Remove malicious chars
					$database = preg_replace('/[^a-zA-Z0-9_\-]/', '', $configuration['database']);
					if ($configuration['datasource'] === 'Database/Mysql') {
						$encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', 'utf8mb4');
					} else {
						/* $encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', $configuration['encoding']); */
						$encoding = preg_replace('/[^a-zA-Z0-9_\-]/', '', 'utf8');
					}
				}
				switch ($configuration['datasource']) {
					case 'Database/Mysql':
						$db->query(
							sprintf(
								'CREATE DATABASE IF NOT EXISTS `%s` /*!40100 DEFAULT CHARACTER SET %s */',
								$database,
								$encoding
							)
						);
						break;
					case 'Database/Postgres':
						$db->query(
							sprintf(
								'CREATE DATABASE %s WITH ENCODING=\'%s\'',
								$database,
								strtoupper($encoding)
							)
						);
						break;
					default:
						CakeLog::error(sprintf('Unknown datasource %s', $configuration['datasource']));
						return false;
				}
				CakeLog::info(
					sprintf('Database %s for %s created successfully', $database, $configuration['datasource'])
				);
			}
		} catch (Exception $e) {
			CakeLog::error($e->getMessage());
			throw $e;
		}

		return true;
	}

/**
 * 管理者の登録処理
 *
 * @param mixed $data 登録データ
 * @return void
 */
	public function saveAdminUser($data) {
		$this->User = ClassRegistry::init('Users.User');
		$this->User->setDataSource('master');

		$this->Language = ClassRegistry::init('M17n.Language');
		$this->Language->setDataSource('master');

		$data = Hash::merge($data, array(
			'User' => array(
				'role_key' => 'system_administrator',
				'status' => '1',
				'timezone' => 'Asia/Tokyo', //後で変更予定
			)
		));

		$languages = $this->Language->find('list', array(
			'fields' => array('Language.id', 'Language.code'),
		));

		$index = 0;
		//foreach ($languages as $languageId => $languageCode) {
		foreach (array_keys($languages) as $languageId) {
			$data['UsersLanguage'][$index] = array(
				'id' => null,
				'language_id' => $languageId,
			);
			//国際化の対応時に再検討の予定
			//if ($languageCode === Configure::read('Config.language')) {
				$data['UsersLanguage'][$index]['name'] = $data['User']['handlename'];
			//}
			$index++;
		}

		return $this->User->saveUser($data);
	}

/**
 * composer packagesのインストール
 *
 * @param boot $update 更新かどうか。Trueの場合、処理しない
 * @return bool Install succeed or not
 **/
	public function installPackages($update) {
		if ($update) {
			return true;
		}

		// Use hhvm only if php version greater than 5.5.0 and hhvm installed
		// @see https://github.com/facebook/hhvm/wiki/OSS-PHP-Frameworks-Unit-Testing
		$gt55 = version_compare(phpversion(), '5.5.0', '>=');
		exec('which hhvm', $messages, $ret);
		$hhvm = ($gt55 && $ret === 0) ? 'hhvm -vRepo.Central.Path=/var/run/hhvm/hhvm.hhbc' : '';

		$file = new File(CakePlugin::path(Inflector::camelize('install')) . 'vendors.txt');
		$plugins = explode(chr(10), trim($file->read()));
		$file->close();

		foreach ($plugins as $plugin) {
			CakeLog::info(sprintf('[composer] Start composer install %s', $plugin));

			$messages = array();
			$ret = null;
			$cmd = sprintf(
				'export COMPOSER_HOME=%s && cd %s && %s `which composer` require %s 2>&1',
				ROOT, ROOT, $hhvm, $plugin
			);
			exec($cmd, $messages, $ret);

			// Write logs
			if (Configure::read('debug') || $ret !== 0) {
				foreach ($messages as $message) {
					CakeLog::info(sprintf('[composer]   %s', $message));
				}
			}
			if ($ret !== 0) {
				$this->response->statusCode(500);
				$this->set('errors', array_merge($this->viewVars['errors'], $messages));
				return false;
			}

			CakeLog::info(sprintf('[composer] Successfully composer install %s', $plugin));
		}

		return true;
	}

/**
 * マイグレーション実行
 *
 * @param string $connection 接続先
 * @return bool Install succeed or not
 */
	public function installMigrations($connection = 'master') {
		$plugins = array_unique(array_merge(
			array(
				'Files', 'Users', 'NetCommons', 'M17n', 'DataTypes', 'PluginManager',
				'Roles', 'Mails', 'SiteManager'
			),
			App::objects('plugins'),
			array_map('basename', glob(ROOT . DS . 'app' . DS . 'Plugin' . DS . '*', GLOB_ONLYDIR))
		));

		// Invoke all available migrations
		CakeLog::info('[Migrations.migration] Start migrating all plugins');

		foreach ($plugins as $plugin) {
			CakeLog::info(
				sprintf('[migration] Start migrating %s for %s connection', $plugin, $connection)
			);

			$messages = array();
			$ret = null;
			exec(sprintf(
				'cd %s && Console%scake Migrations.migration run all -p %s -c %s -i %s 2>&1',
				ROOT . DS . APP_DIR, DS, $plugin, $connection, $connection
			), $messages, $ret);

			// Write logs
			if (Configure::read('debug')) {
				// for windows
				//mb_convert_variables('UTF-8', 'SJIS-win', $messages);

				foreach ($messages as $message) {
					CakeLog::info(sprintf('[migration]   %s', $message));
				}
			}

			CakeLog::info(
				sprintf('[migration] Successfully migrated %s for %s connection', $plugin, $connection)
			);
		}

		CakeLog::info('[migration] Successfully migrated all plugins');

		return true;
	}

/**
 * bower packagesのインストール
 *
 * @param boot $update 更新かどうか。Trueの場合、処理しない
 * @return bool Install succeed or not
 */
	public function installBowerPackages($update) {
		if ($update) {
			return true;
		}

		$plugins = array_unique(array_merge(
			array(
				'Files', 'Users', 'NetCommons', 'M17n', 'DataTypes', 'PluginManager',
				'Roles', 'Mails', 'SiteManager'
			),
			App::objects('plugins'),
			array_map('basename', glob(ROOT . DS . 'app' . DS . 'Plugin' . DS . '*', GLOB_ONLYDIR))
		));

		foreach ($plugins as $plugin) {
			$pluginPath = ROOT . DS . 'app' . DS . 'Plugin' . DS . Inflector::camelize($plugin) . DS;
			if (! file_exists($pluginPath . 'bower.json')) {
				continue;
			}

			$file = new File($pluginPath . 'bower.json');
			$bower = json_decode($file->read(), true);
			$file->close();

			foreach ($bower['dependencies'] as $package => $version) {
				CakeLog::info(
					sprintf('[bower] Start bower install %s#%s for %s', $package, $version, $plugin)
				);

				$messages = array();
				$ret = null;
				exec(sprintf(
					'cd %s && `which bower` --allow-root install %s#%s --save',
					ROOT, $package, $version
				), $messages, $ret);

				// Write logs
				if (Configure::read('debug')) {
					foreach ($messages as $message) {
						CakeLog::info(sprintf('[bower]   %s', $message));
					}
				}

				CakeLog::info(
					sprintf('[bower] Successfully bower install %s#%s for %s', $package, $version, $plugin)
				);
			}
		}

		return true;
	}

}
