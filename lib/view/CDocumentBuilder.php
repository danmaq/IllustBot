<?php

require_once(IB01_CONSTANTS);
require_once(IB01_LIB_ROOT . '/util/CPager.php');

// TODO : これそろそろ分割考えたほうがいいんじゃねえの？

/**
 *	ドキュメントを生成するクラス。
 */
class CDocumentBuilder
{

	/**	XML名前空間URL。 */
	const URI_XMLNS = 'http://www.w3.org/2000/xmlns/';

	/**	XHTML名前空間URL。 */
	const URI_XHTML = 'http://www.w3.org/1999/xhtml';

	/**	XML Schema名前空間URL。 */
	const URI_XSI = 'http://www.w3.org/2001/XMLSchema-instance';

	/**	XHTML名前空間。 */
	const NS_XHTML = 'xhtml';

	/**	XHTML名前空間。 */
	const NS_XSI = 'xsi';

	/**	トレース メッセージ。 */
	public static $trace = '';

	/**	DOMオブジェクト。 */
	private $dom;

	/**	XMLルート要素。 */
	private $body;

	/**	XMLルートのタイトル属性。 */
	private $title;

	/**
	 *	ルート要素を作成します。
	 *
	 *	@param DOMDocument $dom DOMオブジェクト。
	 *	@return DOMElement ルート要素。
	 */
	public static function createBody(DOMDocument $dom)
	{
		$result = $dom->createElement('body');
		$result->setAttributeNS(self::URI_XMLNS , 'xmlns:' . self::NS_XHTML, self::URI_XHTML);
		$result->setAttributeNS(self::URI_XMLNS , 'xmlns:' . self::NS_XSI, self::URI_XSI);
		$dom->appendChild($result);
		return $result;
	}

	/**
	 *	XSLスキンへのパスを取得します。
	 *
	 *	@param string $xslpath XSLファイルへのパス。
	 *	@return string XSLファイルへのパス。
	 */
	private static function getSkinPath($xslpath)
	{
		$result = sprintf('skin/%s/%s', $_GET['skin'], $xslpath);
		if(!file_exists(sprintf('%s/%s', IB01_ROOT, $result)))
		{
			$_GET['skin'] = CConfigure::SKINSET;
			// 既定も存在しない場合無限ループになるので再帰にはしない。
			$result = sprintf('skin/%s/%s', $_GET['skin'], $xslpath);
		}
		return $result;
	}

	/**
	 *	コンストラクタ。
	 *
	 *	@param string $title タイトル メッセージ。
	 */
	public function __construct($title = '')
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
		$body = self::createBody($dom);
		$this->dom = $dom;
		$title = $this->createAttribute($body, 'title', $title);
		$this->createAttribute($body, 'ver', CConstants::VERSION);
		$this->createAttribute($body, 'ua', $_SERVER['HTTP_USER_AGENT']);
		$body->setAttributeNS(self::URI_XSI, self::NS_XSI . ':noNamespaceSchemaLocation', 'skin/IB01.xsd');
		$this->body = $body;
		$this->title = $title;
	}

	/**
	 *	DOMオブジェクトを取得します。
	 *
	 *	@return DOMDocument DOMオブジェクト。
	 */
	public function getDOM()
	{
		return $this->dom;
	}

	/**
	 *	ルート要素を取得します。
	 *
	 *	@return DOMElement ルート要素。
	 */
	public function getRootElement()
	{
		return $this->body;
	}

	/**
	 *	タイトルを取得します。
	 *
	 *	@return string タイトル。
	 */
	public function getTitle()
	{
		return $this->title->value;
	}

	/**
	 *	タイトルを設定します。
	 *
	 *	@param string $value タイトル。
	 */
	public function setTitle($value)
	{
		$this->title->value = $value;
	}

	/**
	 *	XSLTを介してHTMLを生成し、出力します。
	 *
	 *	@param string $xslpath XSLファイルへのパス。
	 *	@return string 出力されたHTML文字列。
	 */
	public function output($xslpath)
	{
		if(isset($_GET['err']))
		{
			$this->createSimpleMessage(_('エラー'), $_GET['err']);
		}
		if(strlen(self::$trace) > 0)
		{
			$this->createSimpleMessage(_('デバッグ用メッセージ'), self::$trace);
		}
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		if(CConfigure::COMPRESS)
		{
			ob_start("ob_gzhandler");
		}
		if(CConfigure::USE_CLIENT_XSLT)
		{
			header('Content-Type: text/xml; charset=UTF-8');
			$dom = $this->getDOM();
			$xsl = $dom->createProcessingInstruction('xml-stylesheet',
				sprintf('type="text/xsl" href="%s"', self::getSkinPath($xslpath)));
			$dom->insertBefore($xsl, $dom->firstChild);
			echo $dom->saveXML();
		}
		else
		{
			$xhtml = 'application/xhtml+xml';
			$accept = isset($_SERVER{'HTTP_ACCEPT'}) ? $_SERVER{'HTTP_ACCEPT'} : $xhtml;
			$pattern = sprintf('/%s/', preg_quote($xhtml, '/'));
			header(sprintf('Content-Type: %s; charset=UTF-8',
				preg_match($pattern, $accept) ? $xhtml : 'text/html'));
			header('X-UA-Compatible: IE=edge');
			echo $this->createHTML($xslpath);
		}
	}

	/**
	 *	XSLTを介してHTMLを生成します。
	 *
	 *	@param string $xslpath XSLファイルへのパス。
	 *	@return string HTML文字列。
	 */
	public function createHTML($xslpath)
	{
		$xslt = new XSLTProcessor();
		$xsl = new DOMDocument();
		$xsl->load(sprintf('%s/%s', IB01_ROOT, self::getSkinPath($xslpath)));
		$xslt->importStyleSheet($xsl);
		return $xslt->transformToXML($this->getDOM());
	}

	/**
	 *	ページャ情報を作成します。
	 *
	 *	@param CPager $pager ページャ オブジェクト。
	 *	@return DOMElement ユーザ情報 オブジェクト。
	 */
	public function createPagerInfo(CPager $pager = null)
	{
		if($pager === null)
		{
			$pager = new CPager();
		}
		$result = $this->getDOM()->createElement('pager');
		$this->createAttribute($result, 'page', $pager->target);
		$this->createAttribute($result, 'tpp', $pager->TopicsPerPage);
		$this->createAttribute($result, 'max', $pager->maxPage);
		$this->createAttribute($result, 'topics', $pager->topics);
		$this->getRootElement()->appendChild($result);
		return $result;
	}

	/**
	 *	シンプルなメッセージを生成します。
	 *
	 *	@param string $caption 見出し。
	 *	@param string $description 本文。
	 *	@param string $seeother 参考資料など。
	 *	@return DOMElement トピック オブジェクト。
	 */
	public function createSimpleMessage($caption, $description)
	{
		if(!($this->getTitle()))
		{
			$this->setTitle($caption);
		}
		$dom = $this->getDOM();
		$topic = $dom->createElement('topic');
		$title = $caption;
		$this->createAttribute($topic, 'title', $title);
		$topic->appendChild($dom->createTextNode($description));
		$this->getRootElement()->appendChild($topic);
		return $topic;
	}

	/**
	 *	属性を作成します。
	 *
	 *	@param DOMNode $element 所属させる要素。
	 *	@param string $name 属性。
	 *	@param string $value 値。
	 *	@preturn DOMNode 作成された属性オブジェクト。
	 */
	public function createAttribute(DOMNode $element, $name, $value)
	{
		$attr = $this->getDOM()->createAttribute($name);
		$attr->value = $value;
		$element->appendChild($attr);
		return $attr;
	}
}

/**
 *	ログを追加します。
 *
 *	@param string $body ログ。
 */
function trace($body)
{
	CDocumentBuilder::$trace .= "\n\n" . $body;
}

?>
