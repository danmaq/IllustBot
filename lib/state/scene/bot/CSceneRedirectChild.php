<?php

require_once(IB01_CONSTANTS);
require_once('CSceneViewImage.php');
require_once(IB01_LIB_ROOT . '/dao/CChild.php');
require_once(IB01_LIB_ROOT . '/state/scene/ranking/CSceneTop.php');
require_once(IB01_LIB_ROOT . '/view/CRedirector.php');

/**
 *	子ぼっとへリダイレクトします。
 */
class CSceneRedirectChild
	implements IState
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	ジャンプ先ID。 */
	private $id;

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
			self::$instance = new CSceneRedirectChild();
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
		$this->id = null;
		try
		{
			if(!isset($_GET['id']))
			{
				throw new Exception(_('ぼっとを指名してください。'));
			}
			if($entity->connectDatabase())
			{
				$bot = new CBot($_GET['id']);
				if($bot->rollback())
				{
					$child = $this->findChild($entity, $bot);
					if($child === null)
					{
						throw new Exception(
							_('子ぼっとがいるようだけど初期化できなかった、異常な事態(素敵な事態)'));
					}
					$this->id = $child->getID();
				}
				else
				{
					$entity->setNextState(CSceneViewImage::getInstance());
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
			CRedirector::seeOther($this->id);
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

	/**
	 *	子ぼっとを探索します。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 *	@param CBot $bot 親ぼっと。
	 *	@return CChild 子ぼっと。
	 */
	private function findChild(CEntity $entity, CBot $bot)
	{
		$child = null;
		if(CChild::getCountAllFromOwner($bot) <= 0)
		{
			$child = $this->createChilds($entity, $bot);
		}
		else
		{
			$child = CChild::getUnvotedFromOwner($bot);
			if($child === null)
			{
				$child = $this->createChildsInheritance($entity, $bot);
			}
		}
		return $child;
	}

	/**
	 *	子ぼっとを生成します。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 *	@param CBot $bot 親ぼっと。
	 *	@return CChild 生成した子ぼっとのうちの1体。
	 */
	private function createChilds(CEntity $entity, CBot $bot)
	{
		$child = null;
		for($i = $bot->getChilds(); --$i >= 0; )
		{
			$child = new CChild();
			$child->setOwner($bot);
			$child->setGeneration($bot->getGeneration());
			$child->commit();
		}
		return $child;
	}

	/**
	 *	子ぼっとを生成します。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 *	@param CBot $bot 親ぼっと。
	 *	@return CChild 子ぼっと。
	 */
	private function createChildsInheritance(CEntity $entity, CBot $bot)
	{
		$child = null;
		$childs = CChild::getFromOwner($bot);
		$len = min($bot->getChilds(), count($childs));
		$threshold = ceil($len * 0.2);
		for($i = 0; $i < $threshold; $i++)
		{
			$child = $childs[$i]->shallowCopy();
		}
		for($i = $len - $threshold; --$i >= 0; )
		{
			$child = CChild::inheritance(
				$childs[mt_rand(0, $threshold)],
				$childs[mt_rand(0, $threshold)]);
		}
		$bot->nextGeneration();
		$bot->commit();
		return $child;
	}
}

?>
