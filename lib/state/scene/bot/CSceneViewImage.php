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
		if(isset($_GET['id']))
		{
			if($entity->connectDatabase())
			{
				$bot = new CBot($_GET['id']);
				if($bot->rollback())
				{
					if(CChild::getCountFromOwner($bot) <= 0)
					{
						$child = null;
						for($i = $bot->getChilds(); --$i >= 0; )
						{
							$child = new CChild();
							$child->setOwner($bot);
							$child->setGeneration($bot->getGeneration());
							$child->commit();
						}
						if($child !== null)
						{
							$this->id = $child->getID();
							$this->child = null;
						}
					}
				}
				else
				{
					$child = new CChild($_GET['id']);
					if($child->rollback())
					{
						$this->child = $child;
						$this->id = null;
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
				$xmlbuilder = new CDocumentBuilder();
				$xmlbuilder->createSimpleMessage(_('ERROR'), _('HOGEE!'));
				$xmlbuilder->output(CConstants::FILE_XSL_MESSAGE);
				$entity->setNextState(CEmptyState::getInstance());
			}
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
