<?php

/**
 *	どちらがサンプル画像に近い画像かを取得します。
 *
 *	@param CPixels $a 画像。
 *	@param CPixels $b 画像。
 *	@return float $aが近い場合負数、$bが近い場合正数。
 */
function cmpPixelsForSort($a, $b)
{
	return (($a->sort - $b->sort) * 10000);
}

/**
 *	ピクセル情報 クラス。
 */
class CPixels
{

	//* fields ────────────────────────────────*

	/**	ソート用一時格納領域。 */
	public $sort = -1;

	/**	GDリソース。 */
	private $resource = null;

	/**	大きさ情報。 */
	private $size;

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
	 *	学習します。
	 *
	 *	@param CPixels $expr サンプル画像。
	 *	@param mixed $parent 親となるピクセル情報、または数。
	 *	@param float $threshold 存続する閾値。
	 *	@return array(CPixels) ピクセル情報一覧。
	 */
	public static function study(CPixels $expr, $parent = 50, $threshold = 0.15)
	{
		$len = 0;
		if(is_int($parent))
		{
			$size = $expr->getSize();
			$len = $parent;
			$parent = array();
			for($i = $len; --$i >= 0; )
			{
				$item = new CPixels();
				$item->createFromSize($size['x'], $size['y']);
				array_push($parent, $item);
			}
		}
		else
		{
			$len = count($parent);
		}
		for($i = $len; --$i >= 0; )
		{
			$p = $parent[$i];
			if($p->sort < 0)
			{
				$p->sort = CPixels::compare($p, $expr);
			}
		}
		usort($parent, 'cmpPixelsForSort');
		$result = array();
		$threshold = (int)round($len * $threshold);
		for($i = $threshold; --$i >= 0; )
		{
			array_unshift($result, $parent[$i]);
		}
		$ia_ = 0;
		for($i = $len - $threshold; --$i >= 0; )
		{
			$ia = ($ia_++ + mt_rand(0, 2)) % $threshold;
			$ib = 0;
			do
			{
				$ib = mt_rand(0, $i < 3 ? $len - 1 : $threshold);
			}
			while($ia == $ib);
			array_push($result, self::inheritance(
				$parent[$ia],
				$parent[$ib]));
		}
		return $result;
	}

	/**
	 *	交叉遺伝します。
	 *	交叉アルゴリズムとしてRGB毎の一様交叉を使用し、
	 *	また0.98%程度の確率で突然変異を発生させます。
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
				if($rnd > 2048 || $rnd < 640)
				{
					$r = ($rnd & 1) == 0 ? $rgba['r'] : $rgbb['r'];
					$g = ($rnd & 2) == 0 ? $rgba['g'] : $rgbb['g'];
					$b = ($rnd & 4) == 0 ? $rgba['b'] : $rgbb['b'];
				}
				else
				{
					$r = (int)($rgba['r'] + $rgbb['r'] * 0.5);
					$g = (int)($rgba['g'] + $rgbb['g'] * 0.5);
					$b = (int)($rgba['b'] + $rgbb['b'] * 0.5);
				}
				if($rnd < 640)
				{
					$r = (int)($r + round(mt_rand(0, 255)) * 0.5);
					$g = (int)($g + round(mt_rand(0, 255)) * 0.5);
					$b = (int)($b + round(mt_rand(0, 255)) * 0.5);
				}
				self::setPixel($resource, $x, $y, $r, $g, $b);
			}
		}
		return $result;
	}

	/**
	 *	ピクセルごとに輝度を比較します。
	 *
	 *	@param CPixels $a ピクセル情報。
	 *	@param CPixels $b ピクセル情報。
	 *	@return float 0～1の値。(誤差が少ないほど小さい値になります)
	 */
	public static function compare(CPixels $a, CPixels $b)
	{
		$sa = $a->getSize();
		$sb = $b->getSize();
		$sx = $sa['x'];
		$sy = $sa['y'];
		if(!($sx == $sb['x'] || $sy == $sb['y']))
		{
			throw new Exception(_('画素数を一致させる必要があります。'));
		}
		$resa = $a->getResource();
		$resb = $b->getResource();
		$result = 0;
		for($y = $sy; --$y >= 0; )
		{
			for($x = $sx; --$x >= 0; )
			{
				$result += self::comparePixel($resa, $resb, $x, $y);
			}
		}
		return ($result / ($sx * $sy));
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
		$r = (($rgb >> 16) & 255);
		$g = (($rgb >> 8) & 255);
		$b = (($rgb >> 0) & 255);
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
		$color = imagecolorallocate($resource,
			$r === null ? round(mt_rand(0, 255)) : $r,
			$g === null ? round(mt_rand(0, 255)) : $g,
			$b === null ? round(mt_rand(0, 255)) : $b);
		if($color)
		{
			$result = imagesetpixel($resource, $x, $y, $color);
			imagecolordeallocate($resource, $color);
		}
		return $result;
	}

	/**
	 *	点の輝度を比較します。
	 *
	 *	@param int $a 画像リソース。
	 *	@param int $b 画像リソース。
	 *	@param int $x X座標。
	 *	@param int $y Y座標。
	 *	@return float 点の輝度誤差を示す0～1の値(同党の場合、0)。
	 */
	private static function comparePixel($a, $b, $x, $y)
	{
		$rgba = imagecolorat($a, $x, $y);
		$rgbb = imagecolorat($b, $x, $y);
		$rgap = abs((($rgba >> 16) & 255) - (($rgbb >> 16) & 255));
		$ggap = abs((($rgba >> 8) & 255) - (($rgbb >> 8) & 255));
		$bgap = abs((($rgba >> 0) & 255) - (($rgbb >> 0) & 255));
		return ($rgap + $ggap + $bgap) / 767;
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
		return $this->size;
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
				$this->resetSize();
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
			$this->resetSize();
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
					self::setPixel($result, $_x, $_y);
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
			$this->resetSize();
		}
		return $result;
	}

	/**
	 *	ファイルから画像を初期化します。
	 *
	 *	@param int $name ファイル名。
	 *	@return boolean 成功したかどうか。
	 */
	public function createFromFile($name)
	{
		$fh = fopen($name, 'rb');
		$result = $this->createFromData(fread($fh, filesize($name)));
		fclose($fh);
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
			$this->resetSize();
		}
		return $result;
	}

	/**
	 *	ディープ コピーを取得します。
	 *	createFromObject()のラッパーです。
	 *
	 *	@return CPixels ピクセル情報。
	 */
	public function copy()
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
			$this->size = null;
		}
	}

	/**
	 *	画像サイズをリセットします。
	 */
	private function resetSize()
	{
		$resource = $this->getResource();
		$x = imagesx($resource);
		$y = imagesy($resource);
		$this->size = array(
			'x' => $x,
			'y' => $y);
	}
}

?>