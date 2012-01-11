<?php

require_once(IB01_CONSTANTS);
require_once('CEntity.php');
require_once(IB01_LIB_ROOT . '/state/scene/error/CSceneSimpleError.php');
require_once(IB01_LIB_ROOT . '/state/scene/error/CSceneDBFailed.php');

/**
 *	状態を持ったオブジェクト。
 */
class CScene
	extends CEntity
{

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 *
	 *	@param $firstState 最初の状態。既定ではnull。
	 */
	public function __construct(IState $firstState = null)
	{
		parent::__construct($firstState);
	}

	//* instance methods ───────────────────────────*

	/**
	 *	データベースに接続します。
	 *
	 *	失敗した場合、自動的にエラーメッセージを表示するシーンへとジャンプします。
	 *	シーンのコミットは行われないため、明示的に行うか、現在の状態を1ループ実行する必要があります。
	 *
	 *	@return boolean 接続に成功した場合、true。
	 */
	public function connectDatabase()
	{
		$db = CDBManager::getInstance();
		$result = $db->connect();
		if(!$result)
		{
			$this->setNextState(CSceneDBFailed::getInstance());
		}
		return $result;
	}
}

?>
