<?php
/**
 * システム管理者IDの登録
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('InstallAppTask', 'Install.Console/Command');

/**
 * システム管理者IDの登録
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Console\Command
 */
class SaveAdministratorTask extends InstallAppTask {

/**
 * usernameのオプションKey
 *
 * @var string
 */
	const KEY_USERNAME = 'username';

/**
 * passwordのオプションKey
 *
 * @var string
 */
	const KEY_PASSWORD = 'password';

/**
 * ハンドル名のオプションKey
 *
 * @var string
 */
	const KEY_HANDLENAME = 'handlename';

/**
 * Override startup
 *
 * @return void
 */
	public function startup() {
		$this->hr();
		$this->out(__d('install', 'NetCommons Install Step 6'));
		$this->hr();
	}

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		parent::execute();

		//引数のセット
		$data = array();

		// * login
		if (array_key_exists(self::KEY_USERNAME, $this->params)) {
			$data['User'][self::KEY_USERNAME] = Hash::get($this->params, self::KEY_USERNAME);
		} else {
			$data['User'][self::KEY_USERNAME] = $this->in(
				__d('install', 'NetCommons Login id?')
			);
		}

		// * password
		if (array_key_exists(self::KEY_PASSWORD, $this->params)) {
			$data['User'][self::KEY_PASSWORD] = Hash::get($this->params, self::KEY_PASSWORD);
		} else {
			$data['User'][self::KEY_PASSWORD] = $this->in(
				__d('install', 'NetCommons Login password?')
			);
		}
		$data['User'][self::KEY_PASSWORD . '_again'] = $data['User'][self::KEY_PASSWORD];

		// * handlename
		$data['User'][self::KEY_HANDLENAME] = __d('install', 'System administrator');

		//アカウント作成
		if (! $this->InstallUtil->saveAdminUser($data)) {
			return $this->error(__d('install', 'The user could not be saved. Please try again.'));
		}
	}

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(__d('install', 'NetCommons Install Step 6'))
			->addOption(self::KEY_USERNAME, array(
				'help' => __d('install', 'Username'),
				'required' => false
			))
			->addOption(self::KEY_PASSWORD, array(
				'help' => __d('install', 'Password'),
				'required' => false
			));

		return $parser;
	}
}
