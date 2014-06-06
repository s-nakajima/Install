<?php
class AllInstallTest extends CakeTestSuite {

/**
 * All test suite
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @return CakeTestSuite
 */
	public static function suite() {
		$plugin = preg_replace('/^All([\w]+)Test$/', '$1', __CLASS__);
		$suite = new CakeTestSuite(sprintf('All %s Plugin tests', $plugin));
		$tasks = array(
			'InstallControllerMysqlPreInit',
			'InstallControllerMysqlPostInit',
			/* 'InstallControllerPostgresqlPreInit', */
			/* 'InstallControllerPostgresqlPostInit', */
		);
		foreach ($tasks as $task) {
			$suite->addTestFile(CakePlugin::path($plugin) . 'Test' . DS . 'Case' . DS . 'Controller' . DS . $task . 'Test.php');
		}
		return $suite;
	}
}
