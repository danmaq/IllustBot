<?php

require_once(IB01_CONSTANTS);
require_once(IB01_LIB_ROOT . '/dao/CBot.php');
require_once(IB01_LIB_ROOT . '/dao/CChild.php');
require_once(IB01_LIB_ROOT . '/state/IState.php');
require_once(IB01_LIB_ROOT . '/state/scene/ranking/CSceneTop.php');
require_once(IB01_LIB_ROOT . '/view/CDocumentBuilder.php');
require_once(IB01_LIB_ROOT . '/view/CRedirector.php');

/**
 *	ぼっとを育てるページを表示します。
 */
class CSceneVotePost
	implements IState
{

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**
	 *	この状態のオブジェクトを取得します。
	 *
	 *	@return IState この状態のオブジェクト。
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			self::$instance = new CSceneVotePost();
		}
		return self::$instance;
	}

	/**
	 *	コンストラクタ。
	 */
	private function __construct()
	{
	}

	/**
	 *	この状態が開始されたときに呼び出されます。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 */
	public function setup(CEntity $entity)
	{
		$this->errors = null;
		try
		{
			if($_SERVER['REQUEST_METHOD'] !== 'POST')
			{
				throw new Exception(_('POSTメソッド以外は受理不可。'));
			}
			if($entity->connectDatabase())
			{
				$_POST += $this->format;
				throw new Exception(_('未実装なのだ。'));
			}
		}
		catch(Exception $e)
		{
			$this->errors = $e->getMessage();
			error_log($e->getTraceAsString());
		}
	}

	/**
	 *	状態が実行されたときに呼び出されます。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 */
	public function execute(CEntity $entity)
	{
		if($entity->getNextState() === null)
		{
			$query = array();
			if($this->errors === null)
			{
			}
			else
			{
				$query = array(
					'f' => 'core/top',
					'err' => $this->errors);
			}
			CRedirector::seeOther($query);
			$entity->dispose();
		}
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
