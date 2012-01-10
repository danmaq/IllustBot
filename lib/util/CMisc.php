<?php

/**
 *	雑多な関数群クラス。
 */
class CMisc
{

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 */
	private function __construct()
	{
	}

	//* class methods ────────────────────────────-*

	/**
	 *	線形補完します。
	 *
	 *	@param float $first 初期値。
	 *	@param float $final 最終値。
	 *	@param float $amount 0～1の重み。
	 *	@return float $first～$finalの値。
	 */
	public static function lerp($first, $final, $amount)
	{
		return $start + ($final - $start) * $amount;
	}

	/**
	 *	等速で重みを計算します。
	 *
	 *	@param float $target 現在値。
	 *	@param float $limit 最大値。
	 *	@return float 0～1の重み値。
	 */
	public static function amontLinear($target, $limit)
	{
		return $target / $limit;
	}

	/**
	 *	等速線形補完します。
	 *
	 *	@param float $first 初期値。
	 *	@param float $final 最終値。
	 *	@param float $target 現在値。
	 *	@param float $limit 最大値。
	 *	@return float $first～$finalの値。
	 */
	public static function lerpLinear($first, $final, $target, $limit)
	{
		return self::lerp($first, $final, self::amountLinear($target / $limit));
	}
}

?>
