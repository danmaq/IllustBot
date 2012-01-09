<?php

require_once(IB01_CONSTANTS);
require_once(IB01_LIB_ROOT . '/dao/CBot.php');
require_once(IB01_LIB_ROOT . '/state/IState.php');
require_once(IB01_LIB_ROOT . '/util/CPager.php');
require_once(IB01_LIB_ROOT . '/view/CDocumentBuilder.php');

/**
 *	トップ ページ。
 */
class CSceneTop
	implements IState
{

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	ぼっと一覧。 */
	private $botsOrderNewbie;

	/**	ぼっと一覧。 */
	private $botsOrderScore;

	/**	ぼっと一覧。 */
	private $botsOrderGeneration;

	/**
	 *	この状態のオブジェクトを取得します。
	 *
	 *	@return IState この状態のオブジェクト。
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			self::$instance = new CSceneTop();
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
		if($entity->connectDatabase())
		{
			$pager = new CPager(0, 3);
			$this->botsOrderGeneration = CBot::getAllOrderGeneration($pager);
			$this->botsOrderNewbie = CBot::getAllOrderNewbie($pager);
			$this->botsOrderScore = CBot::getAllOrderScore($pager);
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
			$xmlbuilder = new CDocumentBuilder();
			$newbie = $xmlbuilder->createElement('new');
			$score = $xmlbuilder->createElement('score');
			$gene = $xmlbuilder->createElement('gene');
			$botsNewbie = $this->botsOrderNewbie;
			$botsScore = $this->botsOrderScore;
			$botsGene = $this->botsOrderGeneration;
			foreach($botsNewbie as $item)
			{
				$xmlbuilder->createItem(array(
					'id' => $item->getID(),
					'theme' => $item->getTheme(),
				), $newbie);
			}
			foreach($botsScore as $item)
			{
				$xmlbuilder->createItem(array(
					'id' => $item->getID(),
					'theme' => $item->getTheme(),
				), $score);
			}
			foreach($botsGene as $item)
			{
				$xmlbuilder->createItem(array(
					'id' => $item->getID(),
					'theme' => $item->getTheme(),
				), $gene);
			}
			$xmlbuilder->output(CConstants::FILE_XSL_TOP);
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
