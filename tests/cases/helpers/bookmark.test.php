<?php

App::import('Helper', 'Html');
App::import('Helper', 'Bookmark.Bookmark');

/**
 * Bookmark Test Case
 * 2010-06-07 ms
 */
class BookmarkTest extends CakeTestCase {

	/**
	 * setUp method
	 */
	function setUp() {
		$this->Bookmark = new BookmarkHelper();
		$this->Bookmark->Html = new HtmlHelper();

		# otherwise images are not displayed in test suite
		$baseFixUrl = dirname(dirname($_SERVER['PHP_SELF']));
		$baseFixUrl = substr($baseFixUrl, 1);

		$this->Bookmark->imgFolder = $baseFixUrl.'/'.$this->Bookmark->imgFolder;
	}


	function testAvailableBookmarks() {
		echo '<h3>Available Bookmarks</h3>';
		$res = $this->Bookmark->availableBookmarks();
		echo implode(', ', $res);
		$this->assertEqual(count($res), 27);

		$test = array(
			'in' => array(
				'facebook',
				'twitter',
				'delicious',
				'digg',
				'reddit',
				'google',
				'yahoo',
				'mister-wong'
			),
			'out' => array( //TODO: add
				'myspace',
				'hyves',
				'tumblr',
				'orkut',
				'print',
			)
		);
		echo '<br  /><br  />';
		foreach ($test['in'] as $t) {
			if (!in_array($t, $res)) {
				echo 'not in anymore: '.$t.'<br  />';
			}
			$this->assertTrue(in_array($t, $res));
		}
		foreach ($test['out'] as $t) {
			if (in_array($t, $res)) {
				echo 'already in: '.$t.'<br  />';
			}
			$this->assertFalse(in_array($t, $res));
		}
	}


	function testGetBookmarks() {
		echo '<h3>Bookmark Icons</h3>';
		echo 'Defaults: ';
		$res = $this->Bookmark->getBookmarks();
		pr($res);
		$this->assertTrue(!empty($res));

		echo 'All: ';
		$res = $this->Bookmark->getBookmarks(null, null, $this->Bookmark->availableBookmarks());
		pr($res);
		$this->assertTrue(!empty($res));
	}


	/**
	 * tearDown method
	 */
	function tearDown() {
		unset($this->Bookmark);
	}
}
?>