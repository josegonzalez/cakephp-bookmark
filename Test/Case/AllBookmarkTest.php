<?php
/**
 * All Bookmark plugin tests
 */
class AllBookmarkTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite method, defines tests for this suite.
 *
 * @return void
 */
	public static function suite() {
		$Suite = new CakeTestSuite('All Bookmark tests');
		$path = CakePlugin::path('Bookmark') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);
		return $Suite;
	}
}
