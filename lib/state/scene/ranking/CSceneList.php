<?php

require_once(IB01_CONSTANTS);
require_once(IB01_LIB_ROOT . '/dao/CBot.php');
require_once(IB01_LIB_ROOT . '/state/IState.php');
require_once(IB01_LIB_ROOT . '/util/CPager.php');
require_once(IB01_LIB_ROOT . '/view/CDocumentBuilder.php');

/**
 *	ぼっとにお題を教えるページを表示します。
 */
class CSceneList
	implements IState
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	既定の値一覧。 */
	private $format = array(
		'theme' => '',
	);

	/**	ぼっと一覧。 */
	private $botsKeyword;

	/**	ぼっと一覧。 */
	private $botsOrderNewbie;

	/**	ぼっと一覧。 */
	private $botsOrderScore;

	/**	ぼっと一覧。 */
	private $botsOrderGeneration;

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
			self::$instance = new CSceneList();
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
		$this->botsKeyword = null;
		if($entity->connectDatabase())
		{
			$pager = new CPager(0, 30);
			$_GET += $this->format;
			if(strlen($_GET['theme']) > 0)
			{
				$this->botsKeyword = CBot::getFromKeyword($pager);
			}
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
			if($this->botsKeyword !== null)
			{
				$keyword = $xmlbuilder->createInfo('theme', array('expr' => $_GET['theme']));
				$botsKeyword = $this->botsKeyword;
				foreach($botsKeyword as $item)
				{
					$xmlbuilder->createItem(array(
						'id' => $item->getID(),
						'theme' => $item->getTheme(),
					), $keyword);
				}
			}
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
			$xmlbuilder->output(CConstants::FILE_XSL_LIST);
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