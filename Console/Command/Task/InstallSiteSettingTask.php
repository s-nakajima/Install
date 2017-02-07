<?php
/**
 * Installの開始
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('InstallAppTask', 'Install.Console/Command');

/**
 * サイト設定
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Install\Console\Command
 */
class InstallSiteSettingTask extends InstallAppTask {

/**
 * アクティブな言語のオプションKey
 *
 * @var string
 */
	const KEY_ACTIVAL_LANGUAGES = 'actival-langs';

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		parent::execute();

		//引数のセット
		if (array_key_exists(self::KEY_ACTIVAL_LANGUAGES, $this->params)) {
			if ($this->params[self::KEY_ACTIVAL_LANGUAGES]) {
				$languageActive = $this->params[self::KEY_ACTIVAL_LANGUAGES];
			} else {
				$languageActive = 'ja';
			}
		} else {
			$languageActive = $this->in(
				__d('install', 'Actival languages?'), ['en,ja', 'en', 'ja'], 'ja'
			);
		}
		$data['Language']['code'] = explode(',', strtolower($languageActive));
		if (! $this->InstallUtil->saveSiteSetting($data)) {
			$message = __d(
				'net_commons',
				'Failed on validation errors. Please check the input data.'
			);
			return $this->error($message);
		}
	}

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(__d('install', 'NetCommons Install Step 7'))
			->addOption(self::KEY_ACTIVAL_LANGUAGES, array(
				'help' => __d('install', 'Actival languages. (en,ja/en/ja)'),
				'required' => false
			));

		return $parser;
	}
}
