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
class CSceneViewImage
	implements IState
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	子ぼっと。 */
	private $child;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 */
	private function __construct()
	{
	}

	//* class methods ────────────────────────────-*

	/**
	 *	この状態のオブジェクトを取得します。
	 *
	 *	@return IState この状態のオブジェクト。
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			self::$instance = new CSceneViewImage();
		}
		return self::$instance;
	}

	//* instance methods ───────────────────────────*

	/**
	 *	この状態が開始されたときに呼び出されます。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 */
	public function setup(CEntity $entity)
	{
		$this->child = null;
		try
		{
			if(!isset($_GET['id']))
			{
				throw new Exception(_('ぼっとを指名してください。'));
			}
			if($entity->connectDatabase())
			{
				$child = new CChild($_GET['id']);
				if($child->rollback())
				{
					$this->child = $child;
				}
				else
				{
					throw new Exception(_('存在しないIDです。'));
					$entity->setNextState(CSceneTop::getInstance());
				}
			}
		}
		catch(Exception $e)
		{
			$_GET['err'] = $e->getMessage();
			$entity->setNextState(CSceneTop::getInstance());
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
			$child = $this->child;
			$owner = $child->getOwner();
			$xmlbuilder = new CDocumentBuilder();
			$xmlbuilder->setTitle($owner->getTheme());
			$xmlbuilder->createInfo('bot', array(
				'id' => $child->getID(),
				'owner' => $owner->getID(),
				'generation' => $child->getGeneration() + 1,
				'amount' => $child->getAmount()));
			$xmlbuilder->output(CConstants::FILE_XSL_VIEW);
			$entity->setNextState(CEmptyState::getInstance());
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
