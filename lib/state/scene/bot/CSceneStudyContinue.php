<?php

require_once(IB01_CONSTANTS);
require_once('CSceneViewImage.php');
require_once(IB01_LIB_ROOT . '/dao/CBot.php');
require_once(IB01_LIB_ROOT . '/dao/CImage.php');
require_once(IB01_LIB_ROOT . '/state/scene/ranking/CSceneTop.php');
require_once(IB01_LIB_ROOT . '/view/CRedirector.php');

/**
 *	予備学習を継続させます。
 */
class CSceneStudyContinue
	implements IState
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	既定の値一覧。 */
	private $format = array(
		'id' => '',
		'continue' => 'NO',
	);

	/**	親ぼっとオブジェクト。 */
	private $bot;

	/**	エラー表示。 */
	private $errors = null;

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
			self::$instance = new CSceneAutoStudy();
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
				$bot = new CBot($_POST['id']);
				if(!$bot->rollback())
				{
					throw new Exception(_('存在しないぼっとです。'));
				}
				$cont = strtolower(trim($_POST['continue']));
				if($cont === 'no')
				{
					$bot->setExampleHash(-1);
					$bot->commit();
				}
				$this->bot = $bot;
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
			$query = '';
			if($this->errors === null)
			{
				$query = $this->bot->getID();
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