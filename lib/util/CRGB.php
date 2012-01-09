<?php

/**
 *	ピクセル情報 クラス。
 */
class CRGB
{

	/**	RGB565。 */
	private $rgb = 0;

	/**
	 *	コンストラクタ。
	 *
	 *	@param int $raw RGB565。
	 */
	public function __construct($raw = null)
	{
		$rgb = 0;
		if($raw === null)
		{
			$rgb = mt_rand(0, 65535);
		}
		else
		{
			$rgb = (ord($raw[0]) << 4) + ord($raw[1]);
		}
		$this->rgb = $rgb;
	}

	/**
	 *	RGB情報を取得します。
	 *
	 *	@return int RGB565情報。
	 */
	public function getRGB()
	{
		return ($this->rgb & 65535);
	}

	/**
	 *	RGB情報を取得します。
	 *
	 *	@return int RGB565情報。
	 */
	public function getRGBRaw()
	{
		$rgb = $this->getRGB();
		return chr(($rgb >> 8) & 255) . chr($rgb & 255);
	}

	/**
	 *	カラーコードを取得します。
	 *
	 *	@return int 24bitRGBカラーコード情報。
	 */
	public function getRGBCode()
	{
		$rgb = $this->rgb;
		$r = round(($rgb & 31) * (255 / 31));
		$rgb >> 5;
		$g = round(($rgb & 63) * (255 / 63));
		$rgb >> 6;
		$b = round(($rgb & 31) * (255 / 31));
		return sprintf('%02X%02X%02X', $r, $g, $b);
	}

	/**
	 *	カラーコード文字列を取得します。
	 *
	 *	@return int カラーコード文字列。
	 */
	public function __toString()
	{
		return $this->getRGBCode();
	}
}

?>
