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

App::uses('CakeValidationSet', 'Model/Validator');

/**
 * Install Utility
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Utility
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class InstallValidatorUtil {

/**
 * PHPバージョン
 * ※テストで変更できるようにメンバー変数にする
 *
 * @var array
 */
	public static $phpVersion = PHP_VERSION;

/**
 * Holds the CakeValidationSet objects array
 *
 * @var CakeValidationSet[]
 */
	protected $_fields = array();

/**
 * List of validation errors.
 *
 * @var array
 */
	public $validationErrors = array();

/**
 * Returns true if all fields pass validation. Will validate hasAndBelongsToMany associations
 * that use the 'with' key as well. Since `Model::_saveMulti` is incapable of exiting a save operation.
 *
 * Will validate the currently set data. Use `Model::set()` or `Model::create()` to set the active data.
 *
 * @param array $options An optional array of custom options to be made available in the beforeValidate callback
 * @return bool True if there are no errors
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function validatesDBConf($options = array()) {
		$validates = array(
			'datasource' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				),
				'inList' => array(
					'rule' => array('inList', array('Database/Mysql', 'Database/Postgres')),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'persistent' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'host' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(
						__d('net_commons', 'Please input %s.'), __d('install', 'Host')
					),
					'required' => true,
				),
				'regex' => array(
					'rule' => array('custom', '/[\w' . preg_quote('-./_~', '/') . ']+$/'),
					'message' => __d('net_commons', 'Only alphabets, numbers and symbols are allowed.'),
					'required' => true,
				),
			),
			'port' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(
						__d('net_commons', 'Please input %s.'), __d('install', 'Port')
					),
					'required' => true,
				),
				'numeric' => array(
					'rule' => array('range', -1, 65536),
					'message' => sprintf(
						__d('net_commons', 'The input %s must be a number bigger than %d and less than %d.'),
						__d('install', 'Port'),
						0,
						65535
					),
				),
			),
			'database' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(
						__d('net_commons', 'Please input %s.'), __d('install', 'Database')
					),
					'required' => true,
				),
				'regex' => array(
					'rule' => array('custom', '/^[\w-]+$/'),
					'message' => __d('install', "Only alphabets, numbers, \"-\" and \"_\" are allowed."),
				),
			),
			'prefix' => array(
				'regex' => array(
					'rule' => array('custom', '/^[\w]+$/'),
					'message' => __d('install', 'Only alphabets and numbers are allowed.'),
					'allowEmpty' => true,
				),
			),
			'login' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(
						__d('net_commons', 'Please input %s.'), __d('install', 'Login')
					),
					'required' => true,
				),
				'regex' => array(
					'rule' => array('custom', '/[\w' . preg_quote('!#%&()*+,-./;<=>?@[]^_{|}~', '/') . ']+$/'),
					'message' => __d('net_commons', 'Only alphabets, numbers and symbols are allowed.'),
					'required' => true,
				),
			),
			'password' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(
						__d('net_commons', 'Please input %s.'), __d('install', 'Password')
					),
					'required' => true,
				),
				'regex' => array(
					'rule' => array('custom', '/[\w' . preg_quote('!#%&()*+,-./;<=>?@[]^_{|}~', '/') . ']+$/'),
					'message' => __d('net_commons', 'Only alphabets, numbers and symbols are allowed.'),
					'allowEmpty' => true,
				),
			),
		);

		$this->_fields = array();
		foreach ($validates as $fieldName => $ruleSet) {
			$this->_fields[$fieldName] = new CakeValidationSet($fieldName, $ruleSet);
		}

		$errors = $this->errors($options);
		if (is_array($errors)) {
			return count($errors) === 0;
		}
		return $errors;
	}

/**
 * Returns an array of fields that have failed validation. On the current model. This method will
 * actually run validation rules over data, not just return the messages.
 *
 * @param string $options An optional array of custom options to be made available in the beforeValidate callback
 * @return array Array of invalid fields
 * @triggers Model.afterValidate $model
 * @see ModelValidator::validates()
 */
	public function errors($options = array()) {
		foreach ($this->_fields as $field) {
			$errors = $field->validate($options);
			foreach ($errors as $error) {
				$this->invalidate($field->field, $error);
			}
		}
		return $this->validationErrors;
	}

/**
 * Marks a field as invalid, optionally setting a message explaining
 * why the rule failed
 *
 * @param string $field The name of the field to invalidate
 * @param string $message Validation message explaining why the rule failed, defaults to true.
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function invalidate($field, $message = true) {
		$this->validationErrors[$field][] = $message;
	}

/**
 * パーミッションチェック
 *
 * @return array
 */
	public function permissions() {
		$permissions = array();

		$writables = array(
			APP . 'Config',
			APP . 'tmp',
			APP . 'Uploads'
		);
		foreach ($writables as $path) {
			if (is_writable($path)) {
				$permissions[] = array(
					'message' => __d('install', '%s is writable', array($path)),
					'error' => false,
				);
			} else {
				$permissions[] = array(
					'message' => __d(
						'install', 'Failed to write %s. Please check permission.', array($path)
					),
					'error' => true,
				);
			}
		}

		return $permissions;
	}

/**
 * バージョンチェック
 *
 * @param array $libraries チェックするライブラリ群(テストで使用)
 * @return array
 */
	public function versions($libraries = array()) {
		$versions = array();

		//PHPのバージョンチェック
		$error = false;
		if (version_compare(self::$phpVersion, '5.4.0') >= 0) {
			$message = __d('install', '%s(%s) version success.', 'PHP', self::$phpVersion);
		} else {
			$message = __d(
				'install',
				'%s(%s) version error. Please more than "%s" version.', 'PHP', self::$phpVersion, 'PHP 5.4'
			);
			$error = true;
		}
		$versions[] = array('message' => $message, 'error' => $error, 'warning' => false);

		if (! $libraries) {
			$libraries = array(
				'dom' => array(
					'type' => 'function', 'name' => 'phpversion', 'asError' => true,
				),
				'imagick' => array(
					'type' => 'function', 'name' => 'phpversion', 'asError' => false,
				),
				'json' => array(
					'type' => 'function', 'name' => 'phpversion', 'asError' => true,
				),
				'libxml' => array(
					'type' => 'constant', 'name' => 'LIBXML_DOTTED_VERSION', 'asError' => true,
				),
				'mbstring' => array(
					'type' => 'function_no_argument', 'name' => 'mb_get_info', 'asError' => true,
				),
				'PDO' => array(
					'type' => 'function', 'name' => 'phpversion', 'asError' => true,
				),
				'pdo_mysql' => array(
					'type' => 'function', 'name' => 'phpversion', 'asError' => true,
				),
			);
		}
		foreach ($libraries as $key => $library) {
			$versions[] = $this->__checkVersion($key, $library);
		}

		return $versions;
	}

/**
 * Console/cakeの実行チェック
 *
 * @return bool
 */
	private function __checkCakeConsole() {
		$messages = array();
		$result = null;
		exec(sprintf(
			'cd %s && Console%scake 2>&1',
			ROOT . DS . APP_DIR, DS
		), $messages, $result);

		foreach ($messages as $message) {
			$result = (bool)preg_match('/Welcome to CakePHP/', $message);
			if ($result) {
				return true;
			}
		}

		return false;
	}

/**
 * バージョンチェック
 *
 * @param string $key ライブラリ名
 * @param array $library ライブラリ情報
 * @return array
 */
	private function __checkVersion($key, $library) {
		$error = false;
		$warning = false;
		if ($library['type'] === 'function') {
			$version = call_user_func($library['name'], $key);
		} elseif ($library['type'] === 'function_no_argument') {
			$version = call_user_func($library['name']);
		} elseif ($library['type'] === 'constant' && defined($library['name'])) {
			$version = constant($library['name']);
		} else {
			$version = false;
		}

		if (! $version) {
			if ($library['asError']) {
				$message = Hash::get(
					$library, 'errorMessage',
					__d('install', 'Not found the %s. Please install the %s.', $key, $key)
				);
				$error = true;
			} else {
				$message = Hash::get(
					$library, 'errorMessage',
					__d('install', 'Not found the %s. Some functions can not be used.', $key)
				);
				$warning = true;
			}
		} else {
			if (is_array($version)) {
				$message = Hash::get(
					$library, 'successMessage',
					__d('install', '%s version success.', $key)
				);
			} else {
				$message = Hash::get(
					$library, 'successMessage',
					__d('install', '%s(%s) version success.', $key, $version)
				);
			}
		}

		return array('message' => $message, 'error' => $error, 'warning' => $warning);
	}

/**
 * バージョン(CLI)チェック
 *
 * @return array
 */
	public function cliVersions() {
		$versions = array();

		if ($this->__checkCakeConsole()) {
			$messages = array();
			$result = null;
			exec(sprintf(
				'cd %s && Console%scake Install.install check_lib_version 2>&1',
				ROOT . DS . APP_DIR, DS
			), $messages, $result);

			$versions = array();
			foreach ($messages as $message) {
				if (! $this->__displayVersionMessage($message)) {
					continue;
				}

				$result = (bool)preg_match('/^Error|^Notice|^Fatal|^Warning|^Console/', $message);
				$versions[] = array(
					'message' => preg_replace('/^Error:|^Success:/', '', $message),
					'error' => $result,
					'warning' => false,
				);
			}
		} else {
			$versions[] = array(
				'message' => __d(
					'install', 'Failed to execute %s. Please check permission.', APP . 'Console' . DS . 'cake'
				),
				'error' => true,
				'warning' => false,
			);
		}

		return $versions;
	}

/**
 * バージョンチェックを出力するかどうか
 *
 * @param string $message メッセージ
 * @return bool
 */
	private function __displayVersionMessage($message) {
		if (substr($message, 0, 2) === '--' ||
				$message === 'NetCommons Install' || ! $message || $message === 'NULL' ||
				! preg_match('/^Error|^Notice|^Fatal|^Success|^Warning|^Console/', $message)) {
			return false;
		} else {
			return true;
		}
	}

/**
 * サイト設定の入力チェック
 *
 * @param array $data リクエストパラメータ
 * @return bool
 */
	public function validatesSiteSetting($data) {
		$this->Language = ClassRegistry::init('M17n.Language');
		$this->_fields = array();

		if (! $this->Language->validateActive($data)) {
			$this->validationErrors = $this->Language->validationErrors;
		}

		return count($this->validationErrors) === 0;
	}

}
