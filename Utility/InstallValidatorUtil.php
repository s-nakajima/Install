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
	public function validates($options = array()) {
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
					'rule' => array('custom', '/^[\w]+$/'),
					'message' => __d('install', 'Only alphabets and numbers are allowed.'),
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

		$writables = [APP . 'Config', APP . 'tmp', APP . 'webroot' . DS . 'files'];
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
 * @return array
 */
	public function versions() {
		$versions = array();

		if (is_executable(APP . 'Console' . DS . 'cake')) {
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

				$result = (bool)preg_match('/^Error|^Notice|^Fatal|^Warning/', $message);
				$versions[] = array(
					'message' => preg_replace('/^Error:|^Success:/', '', $message),
					'error' => $result,
				);
			}
		} else {
			$versions[] = array(
				'message' => __d(
					'install', 'Failed to execute %s. Please check permission.', APP . 'Console' . DS . 'cake'
				),
				'error' => true,
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
				! preg_match('/^Error|^Notice|^Fatal|^Success|^Warning/', $message)) {
			return false;
		} else {
			return true;
		}
	}

}
