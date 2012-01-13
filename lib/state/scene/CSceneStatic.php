<?php

require_once(IB01_LIB_ROOT . '/view/CDocumentBuilder.php');
require_once(IB01_LIB_ROOT . '/state/IState.php');

/**
 *	静的ページを表示するシーンです。
 */
class CSceneStatic
	implements IState
{

	//* fields ────────────────────────────────*

	/**	ヘルプ用 クラス オブジェクト。 */
	private static $help = null;

	/**	XMLスタイル シート。 */
	private $xsl;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 *
	 *	@param string $xsl XSLスタイル シート。
	 */
	private function __construct($xsl)
	{
		$this->xsl = $xsl;
	}

	//* class methods ────────────────────────────-*

	/**
	 *	存在しないページを指定した場合のエラー オブジェクトを取得します。
	 *
	 *	@return IState 状態のオブジェクト。
	 */
	public static function getHelpInstance()
	{
		if(self::$help == null)
		{
			self::$help = new CSceneStatic('help.xsl');
		}
		return self::$help;
	}

	//* instance methods ───────────────────────────*

	/**
	 *	この状態が開始されたときに呼び出されます。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 */
	public function setup(CEntity $entity)
	{
	}

	/**
	 *	状態が実行されたときに呼び出されます。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 */
	public function execute(CEntity $entity)
	{
		$xmlbuilder = new CDocumentBuilder();
		$xmlbuilder->output($this->xsl);
		$entity->setNextState(CEmptyState::getInstance());
	}

	/**
	 *	別の状態へ移行される直前に呼び出されます。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 */
	public function teardown(CEntity $entity)
	{
	}
}

?>