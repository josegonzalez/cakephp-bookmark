<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Bookmark Helper
 *
 * @author Jose Diaz-Gonzalez
 * @version 1.1
 * @license MIT
 *
 * returns social bookmarklets
 * Note: You will need to change the domain name to match your domain name
 * Thanks to http://kevin.vanzonneveld.net/ for the social bookmark list
 * Thanks to Ritesh Agrawal for the original helper
 *
 * 2011-05-18 ms - added some functionality
 * 2014-05-15 ms - made composer aware
 */
class BookmarkHelper extends AppHelper {

	public $helpers = array('Html');

	public $settings = array(
		'folder' => 'bookmark/img/bookmarks/', # Folder where all the bookmark images are located.
		//'size' => 16, # 16, 32, 48
		'ext' => 'gif',
		'checkExists' => false, # if script should make sure icons actually exists...
		'target' => '_blank',
		'shortenUrl' => false,
	);

/**
 * change this defaults if you want to use a different set of bookmarklets
 * The array elements should correspond to $bookmarks keys
 */
	public $defaults = array('yahoo', 'google', 'windows', 'facebook', 'digg', 'technorati', 'delicious', 'stumble', 'slashdot');

/**
 * BookmarkHelper::set()
 *
 * @param mixed $key
 * @param mixed $value
 * @return void
 */
	public function set($key, $value) {
		$this->settings[$key] = $value;
	}

/**
 * BookmarkHelper::setFolder()
 *
 * @param mixed $path
 * @param mixed $ext
 * @return void
 */
	public function setFolder($path, $ext = null) {
		if (substr($path, -1, 1) !== '/') {
			$path .= '/';
		}
		$this->settings['folder'] = $path;
		if ($ext !== null) {
			$this->settings['ext'] = $ext;
		}
	}

/**
 * @param $pagetitle - (required) Title of the Page
 * @param $url - (optional) URL of the page
 * @param $sites - (optional)social bookmarks. If not provided the helper uses the defaults set above. The values should match to the keys of the "bookmarks" variable defined below
 * @return string Div with the specified social bookmarklets
 */
	public function getBookmarks($pagetitle = null, $url = null, $sites = array()) {
		if (empty($pagetitle)) {
			$pagetitle = Configure::read('Config.title');
		}
		if (empty($url)) {
			/* Note: As an alternative you can try Router::url("", true). This should return the absolute url of the current page, but wasn't working for me. So I used this hack. Hopefully someone can tell me a better way to find absolute path */
			$url = $this->Html->url(null, true);
		}
		if (empty($sites)) {
			$sites = $this->defaults;
		}
		$output = "";
		if ($this->settings['shortenUrl'] && (isset($this->Googl) || App::uses('GooglLib', 'Tools.Lib'))) {
			if (!isset($this->Googl)) {
				$this->Googl = new GooglLib();
			}
			$shortened = $this->Googl->getShort($url);
			$url = $shortened['id'];
		}
		$url = rawurlencode($url);
		$pagetitle = rawurlencode($pagetitle);

		foreach ($sites as $site) {
			if (!array_key_exists($site, $this->bookmarks))
				continue;

			//build url
			$link = $this->bookmarks[$site]['link'];
			$link = str_replace('{url}', $url, $link);
			if (substr_count($link, '{title}') > 0) {
				$link = str_replace('{title}', $pagetitle, $link);
			}

			$name = $this->bookmarks[$site]['name'];
			$title = !empty($this->bookmarks[$site]['text']) ? $this->bookmarks[$site]['text'] : $this->bookmarks[$site]['name'];
			//echo returns($this->request->base);
			$image = $this->Html->image(Router::url('/', true) . $this->settings['folder'] . $this->bookmarks[$site]['icon'] . '.' . $this->settings['ext'], array('title' => "{$title}", 'alt' => "{$name}", 'border' => "0"));
			$output .= $this->Html->link($image, $link, array('target' => $this->settings['target'], 'escape' => false)) . ' ';
		}
		return '<div id="bookmarklets">' . $output . '</div>';
	}

	public function availableBookmarks() {
		return array_keys($this->bookmarks);
	}

/**
 * temporary!
 * @param options
 * - annotation (none, inline, short)
 * - lang
 * - size (small, medium, tall, default)
 * - url
 * @return string
 */
	public function googlePlus($options = array()) {
		$defaults = array(
			'lang' => 'de',
			'size' => 'small',
			'annotation' => 'inline',
			'url' => $this->Html->url(null, true),
		);
		$options = am($defaults, $options);
		if ($options['size'] == 'default') {
			$options['size'] = '';
		}
		if ($options['annotation'] == 'short') {
			$options['annotation'] = '';
		}

		return '<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
	{lang: \'' . $options['lang'] . '\'}
</script>
<div class="g-plusone" data-size="' . $options['size'] . '" data-annotation="' . $options['annotation'] . '" data-href="' . $options['url'] . '"></div>';
	}

/**
 * temporary!
 * @param options
 * - lang
 * - size
 * - url
 * @return string
 */
	public function facebook($options = array()) {
		$defaults = array(
			'lang' => 'de',
			'size' => 'small',
			'annotation' => 'inline',
			'url' => $this->Html->url(null, true),
		);
		$options = am($defaults, $options);

		$res = '<div class="fb-like" data-href="' . $options['url'] . '" data-send="false" data-layout="button_count" data-font="arial" data-show-faces="false" data-action="like"></div>';
		$res .= '<div id="fb-root"></div>
<script>!(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/de_DE/all.js#xfbml=1";
	fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
		return $res;
	}

/**
 * temporary!
 * @param options
 * - lang
 * - count (none,	horizontal,	vertical)
 * - url
 * - text
 * - hashtags,
 * - related
 * @return string
 */
	public function twitter($options = array()) {
		$defaults = array(
			'text' => 'Try #' . Configure::read('Config.title') . '!',
			//'related' => Configure::read('Config.title'),
			'lang' => 'de',
			'hashtags' => '',
			'via' => '',
			'count' => 'horizontal',
			'url' => $this->Html->url(null, true),
		);
		$options = am($defaults, $options);

		$res = '<a class="twitter-share-button" data-count="' . $options['count'] . '" data-url="' . $options['url'] . '" data-text="' . $options['text'] . '" data-lang="' . $options['lang'] . '" href="https://twitter.com/share">' . __('Twittern') . '</a>';
		$res .= '<script>!(function (d,s,id) {var js,fjs=d.getElementsByTagName(s)[0];if (!d.getElementById(id)) {js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs"));</script>';

		if ($options['width']) {
			//$res = '<iframe allowtransparency="true" frameborder="0" scrolling="no" src="//platform.twitter.com/widgets/tweet_button.html" style="width:'.$options['width'].'; height:20px;"></iframe>';
		}
		return $res;
	}

/**
 * list of social bookmarks.
 * if you want to use any other social bookmark, replace the actual URL with "{url}" and title with "{title}".
 * See below bookmarks for more details
 */
	public $bookmarks = array(
		/*
		'bookmarks' => array(
			'name' => 'Bookmarks',
			'link' => 'http://de.wikipedia.org/wiki/Social_Bookmarks',
			'icon' => 'bookmarks'
		),
		*/
		'yahoo' => array(
			'name' => 'Yahoo! My Web',
			'link' => 'http://myweb2.search.yahoo.com/myresults/bookmarklet?u={url}&t={title}',
			'icon' => 'yahoo'
		),
		'google' => array(
			'name' => 'Google Bookmarks',
			'link' => 'http://www.google.com/bookmarks/mark?op=edit&bkmk={url}&title={title}',
			'icon' => 'google'
		),
		'windows' => array(
			'name' => 'Windows Live',
			'link' => 'https://favorites.live.com/quickadd.aspx?url={url}&title={title}',
			'icon' => 'windows'
		),
		'facebook' => array(
			'name' => 'Facebook',
			'link' => 'http://www.facebook.com/sharer.php?u={url}&t={title}',
			'icon' => 'facebook'
		),
		'digg' => array(
			'name' => 'Digg',
			'link' => 'http://digg.com/submit?phase=2&url={url}&title={title}',
			'icon' => 'digg'
		),
		'ask' => array(
			'name' => 'Ask',
			'link' => 'http://myjeeves.ask.com/mysearch/BookmarkIt?v=1.2&t=webpages&url={url}&title={title}',
			'icon' => 'ask',
		),
		'technorati' => array(
			'name' => 'Technorati',
			'link' => 'http://www.technorati.com/faves?add={url}',
			'icon' => 'technorati'
		),
		'delicious' => array(
			'name' => 'del.icio.us',
			'link' => 'http://del.icio.us/post?url={url}&title={title}',
			'icon' => 'delicious'
		),
		'stumble' => array(
			'name' => 'StumbleUpon',
			'link' => 'http://www.stumbleupon.com/submit?url={url}&title={title}',
			'icon' => 'stumble'
		),
		'squidoo' => array(
			'name' => 'Squidoo',
			'link' => 'http://www.squidoo.com/lensmaster/bookmark?{url}',
			'icon' => 'squidoo'
		),
		'netscape' => array(
			'name' => 'Netscape',
			'link' => 'http://www.netscape.com/submit/?U={url}&T={title}',
			'icon' => 'netscape'
		),
		'slashdot' => array(
			'name' => 'Slashdot',
			'link' => 'http://slashdot.org/bookmark.pl?url={url}&title={title}',
			'icon' => 'slashdot'
		),
		'reddit' => array(
			'name' => 'reddit',
			'link' => 'http://reddit.com/submit?url={url}&title={title}',
			'icon' => 'reddit'
		),
		'furl' => array(
			'name' => 'Furl',
			'link' => 'http://furl.net/storeIt.jsp?u={url}&t={title}',
			'icon' => 'furl'
		),
		'blinklist' => array(
			'name' => 'BlinkList',
			'link' => 'http://blinklist.com/index.php?Action=Blink/addblink.php&Url={url}&Title={title}',
			'icon' => 'blinklist'
		),
		'dzone' => array(
			'name' => 'dzone',
			'link' => 'http://www.dzone.com/links/add.html?url={url}&title={title}',
			'icon' => 'dzone'
		),
		'swik' => array(
			'name' => 'SWiK',
			'link' => 'http://stories.swik.net/?submitUrl&url={url}',
			'icon' => 'swik'
		),
		'shoutwire' => array(
			'name' => 'Shoutwrie',
			'link' => 'http://www.shoutwire.com/?p=submit&link={url}',
			'icon' => 'shoutwire'
		),
		'blinkbits' => array(
			'name' => 'Blinkbits',
			'link' => 'http://www.blinkbits.com/bookmarklets/save.php?v=1&source_url={url}',
			'icon' => 'blinkbits'
		),
		'spurl' => array(
			'name' => 'Spurl',
			'link' => 'http://www.spurl.net/spurl.php?url={url}&title={title}',
			'icon' => 'spurl'
		),
		'diigo' => array(
			'name' => 'Diigo',
			'link' => 'http://www.diigo.com/post?url={url}&title={title}',
			'icon' => 'diigo'
		),
		'tailrank' => array(
			'name' => 'Tailrank',
			'link' => 'http://tailrank.com/share/?link_href={url}&title={title}',
			'icon' => 'tailrank'
		),
		'rawsugar' => array(
			'name' => 'Rawsugar',
			'link' => 'http://www.rawsugar.com/tagger/?turl={url}&tttl={title}&editorInitialized=1',
			'icon' => 'rawsugar'
		),
		'twitter' => array(
			'name' => 'Twitter',
			'text' => 'Tweet this',
			'link' => 'http://twitter.com/?status={url}+{title}',
			'icon' => 'twitter'
		),
		'vz' => array(
			'name' => 'VZ',
			'text' => 'studiVz, meinVz, schülerVz',
			'link' => 'http://www.studivz.net/Link/Share/?url={url}&descr={title}',
			'icon' => 'vz'
		),
		'mister-wong' => array(
			'name' => 'Mister Wong',
			'link' => 'http://www.mister-wong.de/addurl/?bm_url={url}&bm_description={title}',
			'icon' => 'mister-wong'
		),
		'posterous' => array(
			'name' => 'Posterous',
			'link' => 'http://posterous.com/share?linkto={url}&title={title}',
			'icon' => 'posterous'
		),
	);

}
