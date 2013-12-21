<?php
class AllTests extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$path = APP_TEST_CASES . DS;
		// core tests
		$pluginsPath = APP . 'Plugin' . DS;
		$testPath = DS . 'Test' . DS . 'Case';
		$modelPath = $testPath . DS . 'Model';
		$controllerPath = $testPath . DS . 'Controller';

		$behaviorPath = $modelPath . DS . 'Behavior';

		$suite->addTestFile($pluginsPath . 'Auctions' . $modelPath . DS . 'AuctionTest.php');

		return $suite;
	}

}
