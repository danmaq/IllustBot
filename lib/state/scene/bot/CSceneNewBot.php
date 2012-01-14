<?php

require_once(IB01_LIB_ROOT . '/dao/CBot.php');
require_once(IB01_LIB_ROOT . '/state/IState.php');
require_once(IB01_LIB_ROOT . '/view/CDocumentBuilder.php');

/**
 *	ぼっとにお題を教えるページを表示します。
 */
class CSceneNewBot
	implements IState
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	既定の値一覧。 */
	private $format = array(
		'id' => '',
	);

	/**	エラー表示。 */
	private $errors = null;

	/**	親ぼっと。 */
	private $bot = null;

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
			self::$instance = new CSceneNewBot();
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
		$this->bot = null;
		$this->errors = null;
		if($entity->connectDatabase())
		{
			$_GET += $this->format;
			if(strlen($_GET['id']) > 0)
			{
				$bot = new CBot($_GET['id']);
				if($bot->rollback())
				{
					$this->bot = $bot;
				}
			}
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
			$bot = $this->bot;
			if($bot !== null)
			{
				$xmlbuilder->createInfo('bot', array(
					'id' => $bot->getID(),
					'theme' => $bot->getTheme(),
					'generation' => $bot->getGeneration(),
				));
			}
			$xmlbuilder->output(CConstants::FILE_XSL_NEW);
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