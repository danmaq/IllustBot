<?php

/**
 *	ピクセル情報 クラス。
 */
class CPixels
{

	//* fields ────────────────────────────────*

	/**	GDリソース。 */
	private $resource = null;

	//* constructor & destructor ───────────────────────*

	/**
	 *	デストラクタ。
	 */
	function __destruct()
	{
		$this->dispose();
	}

	//* class methods ────────────────────────────-*

	/**
	 *	交叉遺伝します。
	 *	交叉アルゴリズムとしてRGB毎の一様交叉を使用し、
	 *	また1.56%程度の確率で突然変異を発生させます。
	 *
	 *	@param CPixels $a ピクセル情報。
	 *	@param CPixels $b ピクセル情報。
	 *	@return CPixels 交叉されたピクセル情報。
	 */
	public static function inheritance(CPixels $a, CPixels $b)
	{
		$sa = $a->getSize();
		$sb = $b->getSize();
		if(!($sa['x'] == $sb['x'] || $sa['y'] == $sb['y']))
		{
			throw new Exception(_('画素数を一致させる必要があります。'));
		}
		$result = new CPixels();
		$resource = $result->createFromSizeBlank($sa['x'], $sa['y']);
		$resa = $a->getResource();
		$resb = $b->getResource();
		for($y = $sa['y']; --$y >= 0; )
		{
			for($x = $sa['x']; --$x >= 0; )
			{
				$rgba = self::getPixel($resa, $x, $y);
				$rgbb = self::getPixel($resb, $x, $y);
				$r = null;
				$g = null;
				$b = null;
				$rnd = mt_rand(0, 65535);
				if($rnd > 1024)
				{
					$r = ($rnd & 1) == 0 ? $rgba['r'] : $rgbb['r'];
					$g = ($rnd & 2) == 0 ? $rgba['g'] : $rgbb['g'];
					$b = ($rnd & 4) == 0 ? $rgba['b'] : $rgbb['b'];
				}
				setPixel($resource, $x, $y, $r, $g, $b);
			}
		}
		return $result;
	}

	/**
	 *	点の色情報を取得します。
	 *
	 *	@param int $resource 画像リソース。
	 *	@param int $x X座標。
	 *	@param int $y Y座標。
	 *	@return array 色情報。HASH=>{r, g, b}
	 */
	private static function getPixel($resource, $x, $y)
	{
		$rgb = imagecolorat($resource, $x, $y);
		$ra = (($rgb >> 16) & 255);
		$ga = (($rgb >> 8) & 255);
		$ba = (($rgb >> 0) & 255);
		return array(
			'r' => $r,
			'g' => $g,
			'b' => $b);
	}

	/**
	 *	点を描画します。
	 *
	 *	@param int $resource 画像リソース。
	 *	@param int $x X座標。
	 *	@param int $y Y座標。
	 *	@param int $r 赤輝度。既定値はランダム。
	 *	@param int $g 緑輝度。既定値はランダム。
	 *	@param int $b 青輝度。既定値はランダム。
	 *	@return boolean 成功したかどうか。
	 */
	private static function setPixel($resource, $x, $y, $r = null, $g = null, $b = null)
	{
		$result = false;
		$color = imagecolorallocate($result,
			$r === null ? round(mt_rand(0, 255)) : $r,
			$g === null ? round(mt_rand(0, 255)) : $g,
			$b === null ? round(mt_rand(0, 255)) : $b);
		if($color)
		{
			$result = imagesetpixel($result, $x, $y, $color);
			imagecolordeallocate($result, $color);
		}
		return $result;
	}

	//* instance methods ───────────────────────────*

	/**
	 *	解放されているかどうかを取得します。
	 *
	 *	@return bool 解放されている場合、true。
	 */
	public function isInitialized()
	{
		return $this->getResource() !== null;
	}

	/**
	 *	画像リソースIDを取得します。
	 *
	 *	@return int 画像リソースID。
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 *	画像サイズを取得します。
	 *
	 *	@return array 画像サイズ。HASH=>{x, y}
	 */
	public function getSize()
	{
		$x = imagesx($resource);
		$y = imagesy($resource);
		return array(
			'x' => $x,
			'y' => $y);
	}

	/**
	 *	平均色を取得します。
	 *
	 *	@return int 平均色(RGB888)。
	 */
	public function getAverage()
	{
		$size = $this->getSize();
		$resource = $this->getResource();
		$pixels = $size['x'] * $size['y'];
		$r = 0;
		$g = 0;
		$b = 0;
		for($y = $size['y']; --$y >= 0; )
		{
			for($x = $size['x']; --$x >= 0; )
			{
				$rgb = self::getPixel($resource, $x, $y);
				$r += $rgb['r'];
				$g += $rgb['g'];
				$b += $rgb['b'];
			}
		}
		$r = (floor($r / $pixels) & 255);
		$g = (floor($g / $pixels) & 255);
		$b = (floor($b / $pixels) & 255);
		return ($r << 16) + ($g << 8) + ($b << 0);
	}

	/**
	 *	レンダリングします。
	 *
	 *	@return string レンダリング結果(バイナリ文字列)。
	 */
	public function render()
	{
		ob_start();
		imagepng($this->getResource(), null, 9);
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	/**
	 *	サイズを変更します。
	 *	引数のサイズが同じ場合、何もしません。
	 *
	 *	@param int $x X軸サイズ。
	 *	@param int $y Y軸サイズ。省略時は$xと同等値(正方形となります)。
	 *	@return boolean 成功したかどうか。
	 */
	public function resize($x, $y = null)
	{
		if($y === null)
		{
			$y = $x;
		}
		$size = $this->getSize();
		$result = ($x == $size['x'] && $y == $size['y']);
		if(!$result)
		{
			$result = imagecreatetruecolor($x, $y);
			if($result)
			{
				imagecopyresampled(
					$result, $this->getResource(), 0, 0, 0, 0, $x, $y, $size['x'], $size['y']);
				$this->dispose();
				$this->resource = $result;
			}
		}
		return $result;
	}

	/**
	 *	大きさから画像を初期化します。
	 *
	 *	@param int $x X軸サイズ。
	 *	@param int $y Y軸サイズ。省略時は$xと同等値(正方形となります)。
	 *	@return boolean 成功したかどうか。
	 */
	public function createFromSizeBlank($x, $y = null)
	{
		if($y === null)
		{
			$y = $x;
		}
		$this->dispose();
		$result = imagecreatetruecolor($x, $y);
		if($result)
		{
			$this->resource = $result;
		}
		return $result;
	}

	/**
	 *	大きさから画像を初期化します。
	 *	画像には自動的にランダムノイズが入ります。
	 *
	 *	@param int $x X軸サイズ。
	 *	@param int $y Y軸サイズ。省略時は$xと同等値(正方形となります)。
	 *	@return boolean 成功したかどうか。
	 */
	public function createFromSize($x, $y = null)
	{
		$result = $this->createFromSizeBlank($x, $y);
		if($result)
		{
			for($_y = $y; --$_y >= 0; )
			{
				for($_x = $x; --$_x >= 0; )
				{
					self::setPixel($resource, $_x, $_y);
				}
			}
		}
		return $result;
	}

	/**
	 *	RAWデータから画像を初期化します。
	 *
	 *	@param int $raw RAWデータ。
	 *	@return boolean 成功したかどうか。
	 */
	public function createFromData($raw)
	{
		$this->dispose();
		$result = imagecreatefromstring($raw);
		if($result)
		{
			$this->resource = $result;
		}
		return $result;
	}

	/**
	 *	別のオブジェクトから画像をコピーします。
	 *
	 *	@param CPixels $obj 別のオブジェクト。
	 *	@return boolean 成功したかどうか。
	 */
	public function createFromObject(CPixels $obj)
	{
		$this->dispose();
		$size = $obj->getSize();
		$result = imagecreatetruecolor($size['x'], $size['y']);
		if($result)
		{
			imagecopy($result, $obj->getResource(), 0, 0, 0, 0, $size['x'], $size['y']);
			$this->resource = $result;
		}
		return $result;
	}

	/**
	 *	ディープ コピーを取得します。
	 *	createFromObject()のラッパーです。
	 *
	 *	@return CPixels ピクセル情報。
	 */
	public function clone()
	{
		$result = new CPixels();
		$result->createFromObject($this);
		return $result;
	}

	/**
	 *	状態をリセットします。
	 */
	public function dispose()
	{
		if($this->resource !== null)
		{
			imagedestroy($this->resource);
			$this->resource = null;
		}
	}
}

?>
