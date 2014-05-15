<?php

App::uses('HtmlHelper', 'View/Helper');
App::uses('BookmarkHelper', 'Bookmark.View/Helper');
App::uses('View', 'View');

/**
 * Bookmark Test Case
 */
class BookmarkTest extends CakeTestCase {

	public $Bookmark;

	/**
	 * setUp method
	 */
	public function setUp() {
		parent::setUp();

		$this->Bookmark = new BookmarkHelper(new View(null));
		$this->Bookmark->Html = new HtmlHelper(new View(null));

		# otherwise images are not displayed in test suite
		$baseFixUrl = dirname(dirname($_SERVER['PHP_SELF']));
		$baseFixUrl = rtrim(substr($baseFixUrl, 1), '/');
		$this->Bookmark->settings['folder'] = $baseFixUrl . $this->Bookmark->settings['folder'];
	}

	/**
	 * BookmarkTest::testAvailableBookmarks()
	 *
	 * @return void
	 */
	public function testAvailableBookmarks() {
		echo '<h2>Available Bookmarks</h2>';
		$res = $this->Bookmark->availableBookmarks();
		echo implode(', ', $res);
		$this->assertEquals(count($res), 27);

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
				echo 'not in anymore: ' . $t . '<br  />';
			}
			$this->assertTrue(in_array($t, $res));
		}
		foreach ($test['out'] as $t) {
			if (in_array($t, $res)) {
				echo 'already in: ' . $t . '<br  />';
			}
			$this->assertFalse(in_array($t, $res));
		}
	}

	/**
	 * BookmarkTest::testGetBookmarks()
	 *
	 * @return void
	 */
	public function testGetBookmarks() {
		echo '<h2>' . __FUNCTION__ . '</h2>';
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
	 * BookmarkTest::testGetBigBookmarks()
	 *
	 * @return void
	 */
	public function testGetBigBookmarks() {
		echo '<h2>' . __FUNCTION__ . '</h2>';
		echo 'Defaults (requires img in app/webroot/img/icons32"!): ';
		$this->Bookmark->setFolder('img/icons32', 'png');
		$bookmarks = array('twitter', 'facebook', 'mister-wong');

		$res = $this->Bookmark->getBookmarks('x', Router::url('/', true), $bookmarks);
		pr($res);
		$this->assertTrue(!empty($res));
	}

	/**
	 * BookmarkTest::testGetBigBookmarksWithShortening()
	 *
	 * @return void
	 */
	public function testGetBigBookmarksWithShortening() {
		echo '<h2>' . __FUNCTION__ . '</h2>';
		echo 'Defaults (requires img in app/webroot/img/icons32"!): ';
		$this->Bookmark->setFolder('img/icons32', 'png');
		$this->Bookmark->set('shortenUrl', true);
		$bookmarks = array('twitter', 'facebook', 'mister-wong');

		$res = $this->Bookmark->getBookmarks('lokis - juhuu', 'http://www.lokalisten.de', $bookmarks);
		pr($res);
		$this->assertTrue(!empty($res));
	}

	/**
	 * BookmarkTest::testGooglePlus()
	 *
	 * @return void
	 */
	public function testGooglePlus() {
		echo $this->Bookmark->googlePlus();
	}

	/**
	 * tearDown method
	 */
	public function tearDown() {
		parent::tearDown();

		unset($this->Bookmark);
	}

}
