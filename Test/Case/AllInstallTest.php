<?php
/**
 * All test suite
 */

/**
 * All test suite
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
class AllInstallTest extends CakeTestSuite {

/**
 * All test suite
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return CakeTestSuite
 * @codeCoverageIgnore
 */
	public static function suite() {
		$plugin = preg_replace('/^All([\w]+)Test$/', '$1', __CLASS__);
		$suite = new CakeTestSuite(sprintf('All %s Plugin tests', $plugin));
		$tasks = array(
			'InstallControllerMysqlPreInit',
			'InstallControllerMysqlPostInit',
		);

		if (isset($_SERVER['DB'])) {
			if ($_SERVER['DB'] === 'pgsql') {
				$tasks = array_merge(
					$tasks,
					array(
						'InstallControllerPostgresqlPreInit',
						'InstallControllerPostgresqlPostInit',
					));
			}
		}

		foreach ($tasks as $task) {
			$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . DS . 'Controller' . DS . $task . 'Test.php');
		}
		return $suite;
	}
}
