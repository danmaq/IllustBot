<?php

require_once('CMisc.php');

/**
 *	2次元ベクトル クラス。
 */
class CVector2
{

	//* fields ────────────────────────────────*

	/**	X座標。 */
	public $x;

	/**	Y座標。 */
	public $y;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 *
	 *	@param float $x X座標。既定値は0。
	 *	@param float $y Y座標。既定値はX座標と同じ(正方形になります)。
	 */
	public function __construct($x = 0, $y = null)
	{
	}

	//* class methods ────────────────────────────-*

	/**
	 *	線形補完します。
	 *
	 *	@param CVector2 $v1 ベクトル。
	 *	@param CVector2 $v2 ベクトル。
	 *	@param float $amount 0～1の重み。
	 *	@return CVector2 $first～$finalのベクトル値。
	 */
	public static function lerp(CVector2 $v1, CVector2 $v2, $amount)
	{
		return new CVector2(
			CMisc::lerp($v1->x, $v2->x, $amount),
			CMisc::lerp($v1->y, $v2->y, $amount));
	}
}

?>
