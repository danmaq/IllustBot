<?php

require_once('CRGB.php');

/**
 *	ピクセル情報 クラス。
 */
class CPixels
{

	//* fields ────────────────────────────────*

	/**	ピクセル情報。 */
	private $pixels;

	/**	X軸サイズ。 */
	private $x;

	/**	Y軸サイズ。 */
	private $y;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 *
	 *	@param string $raw バイナリによるピクセル情報、またはピクセル数。
	 *	@param int $x X軸サイズ。
	 *	@param int $x Y軸サイズ。
	 */
	public function __construct($raw, $x = 0, $y = 0)
	{
		if(is_string($raw))
		{
			$pixels = array();
			$len = strlen($raw);
			for($i = 0; $i < $len; $i += 2)
			{
				$color = new CRGB(substr($raw, $i, 2));
				array_push($pixels, $color);
			}
			$this->pixels =& $pixels;
		}
		elseif(is_int($raw))
		{
			$this->resetPixels($raw);
		}
		else
		{
			throw new Exception(_('引数はバイナリか整数でなければなりません。'));
		}
		if($x * $y <= 0)
		{
			$x = floor(sqrt(count($this->get())));
			$y = $x;
		}
		$this->setSize($x, $y);
	}

	//* class methods ────────────────────────────-*

	/**
	 *	交叉遺伝します。
	 *	交叉アルゴリズムに一様交叉を使用します。
	 *
	 *	@param CPixels $a ピクセル情報。
	 *	@param CPixels $b ピクセル情報。
	 *	@return CPixels 交叉されたピクセル情報。
	 */
	public static function inheritance(CPixels $a, CPixels $b)
	{
		$pixelsA =& $a->get();
		$pixelsB =& $b->get();
		$len = count($pixelsA);
		$size = $a->getSize();
		if($len != count($pixelsB))
		{
			throw new Exception(_('画素数を一致させる必要があります。'));
		}
		$result = new CPixels($len);
		$result->setSize($size['x'], $size['y']);
		$pixels =& $result->get();
		for($i = $len; --$i >= 0; )
		{
			$rnd = mt_rand(0, 65535);
			if($rnd > 1024)
			{
				$pixels[$i] = ($rnd & 1) == 0 ? $pixelsA[$i] : $pixelsB[$i];
			}
		}
	}

	//* instance methods ───────────────────────────*

	/**
	 *	画像サイズを取得します。
	 *
	 *	@return array 画像サイズ。{x => x, y => y}
	 */
	public function getSize()
	{
		return array(
			'x' => $this->x,
			'y' => $this->y);
	}

	/**
	 *	画像サイズを設定します。
	 *
	 *	@param integer $x X軸の画像サイズ。
	 *	@param integer $y Y軸の画像サイズ。
	 */
	public function setSize($x, $y)
	{
		$this->x = $x;
		$this->y = $y;
	}

	/**
	 *	ピクセル情報を取得します。
	 *
	 *	@return array[CRGB] ピクセル情報。
	 */
	public function &get()
	{
		return $this->pixels;
	}

	/**
	 *	ピクセル情報を取得します。
	 *
	 *	@return string バイナリによるピクセル情報。
	 */
	public function getRawData()
	{
		$result = '';
		$pixels = $this->pixels;
		for($i = count($pixels); --$i >= 0; )
		{
			$result = $pixels[$i]->getRGBRaw() . $result;
		}
		return $result;
	}

	/**
	 *	ディープ コピーを取得します。
	 *
	 *	@return CPixels ピクセル情報。
	 */
	public function clone()
	{
		$result = new CPixels($this->getRawData());
		$result->setSize($this->x, $this->y);
		return $result;
	}

	/**
	 *	平均色を取得します。
	 *
	 *	@return string バイナリによるピクセル情報。
	 */
	public function getAverage()
	{
		$result = 0;
		$pixels = $this->pixels;
		$len = count($pixels);
		for($i = $len; --$i >= 0; )
		{
			$result += $pixels[$i]->getRGB();
		}
		return floor($result / $len);
	}

	/**
	 *	ピクセル情報をリセットします。
	 *
	 *	@param int $len ピクセル数。既定値は現在格納されている情報のピクセル数。
	 */
	public function reset($len = 0)
	{
		if($len <= 0)
		{
			$len = count($this->pixels);
		}
		$pixels = array();
		for($i = 0; $i < $len; $i++)
		{
			$color = new CRGB();
			array_push($pixels, $color);
		}
		$this->pixels = $pixels;
	}
	
//	public function 
}

?>
