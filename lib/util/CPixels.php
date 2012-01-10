<?php

require_once('CRGB.php');

/**
 *	ピクセル情報 クラス。
 */
class CPixels
{

	//* fields ────────────────────────────────*

	private $pixels;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 *
	 *	@param string $raw バイナリによるピクセル情報、またはピクセル数。
	 */
	public function __construct($raw)
	{
		$pixels = array();
		if(is_string($raw))
		{
			$len = strlen($raw);
			for($i = 0; $i < $len; $i += 2)
			{
				$color = new CRGB(substr($raw, $i, 2));
				array_push($pixels, $color);
			}
		}
		elseif(is_int($raw))
		{
			$this->resetPixels($raw);
		}
		else
		{
			throw new Exception(_('引数はバイナリか整数でなければなりません。'));
		}
		$this->pixels =& $pixels;
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
		$size = count($pixelsA);
		if($size != count($pixelsB))
		{
			throw new Exception(_('画素数を一致させる必要があります。'));
		}
		$result = new CPixels($size);
		$pixels =& $result->get();
		for($i = $size; --$i >= 0; )
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
	 *	平均色を取得します。
	 *
	 *	@return string バイナリによるピクセル情報。
	 */
	public function getAverage()
	{
		$result = 0;
		$pixels = $this->pixels;
		$size = count($pixels);
		for($i = $size; --$i >= 0; )
		{
			$result += $pixels[$i]->getRGB();
		}
		return floor($result / $size);
	}

	/**
	 *	ディープ コピーを取得します。
	 *
	 *	@return CPixels ピクセル情報。
	 */
	public function clone()
	{
		return new CPixels($this->getRawData());
	}

	/**
	 *	ピクセル情報をリセットします。
	 *
	 *	@param int $size ピクセル数。既定値は現在格納されている情報のピクセル数。
	 */
	public function reset($size = null)
	{
		if($size === null)
		{
			$size = count($this->pixels);
		}
		$pixels = array();
		for($i = 0; $i < $len; $i++)
		{
			$color = new CRGB();
			array_push($pixels, $color);
		}
		$this->pixels = $pixels;
	}
}

?>
