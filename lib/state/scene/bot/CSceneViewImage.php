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

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	ジャンプ先ID。 */
	private $id;

	/**	子ぼっと。 */
	private $child;

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
		$this->id = null;
		$this->child = null;
		if(isset($_GET['id']))
		{
			if($entity->connectDatabase())
			{
				$bot = new CBot($_GET['id']);
				if($bot->rollback())
				{
					if(CChild::getCountAllFromOwner($bot) <= 0)
					{
						$this->createChilds($entity, $bot);
					}
					else
					{
						$child = null;
						try
						{
							$child = CChild::getUnvotedFromOwner($bot);
						}
						catch(Exception $e)
						{
							$child = $this->createChildsInheritance($entity, $bot);
						}
						if($child === null)
						{
							$_GET['err'] = _('子ぼっとがいるようだけど初期化できなかった、異常な事態(素敵な事態)');
							$entity->setNextState(CSceneTop::getInstance());
						}
						else
						{
							$this->id = $child->getID();
						}
					}
				}
				else
				{
					$child = new CChild($_GET['id']);
					if($child->rollback())
					{
						$this->child = $child;
					}
					else
					{
						$_GET['err'] = _('存在しないIDです。');
						$entity->setNextState(CSceneTop::getInstance());
					}
				}
			}
		}
		else
		{
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
			if($this->id !== null)
			{
				CRedirector::seeOther($this->id);
				$entity->dispose();
			}
			elseif($this->child !== null)
			{
				$child = $this->child;
				$owner = $child->getOwner();
				$size = $owner->getSize();
				$pixels =& $child->getPixels();
				$xmlbuilder = new CDocumentBuilder();
				$xmlbuilder->setTitle($owner->getTheme());
				$xmlbuilder->createInfo('bot', array(
					'id' => $child->getID(),
					'generation' => $child->getGeneration() + 1,
					'amount' => $child->getAmount()));
				for($y = 0; $y < $size['y']; $y++)
				{
					$line = $xmlbuilder->createElement('line');
					for($x = 0; $x < $size['x']; $x++)
					{
						$xmlbuilder->createItem(
							array('color' => $pixels[$y * $size['x'] + $x]), $line);
					}
				}
				$xmlbuilder->output(CConstants::FILE_XSL_VIEW);
				$entity->setNextState(CEmptyState::getInstance());
			}
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

	/**
	 *	子ぼっとを生成します。
	 *
	 *	@param CEntity $entity この状態が適用されたオブジェクト。
	 *	@param CBot $bot 親ぼっと。
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
		if($child === null)
		{
			$_GET['err'] = _('子ぼっとがいるようでいないような、異常な事態(素敵な事態)');
			$entity->setNextState(CSceneTop::getInstance());
		}
		else
		{
			$this->id = $child->getID();
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
