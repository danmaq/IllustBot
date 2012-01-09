<?php

require_once(IB01_CONSTANTS);
require_once(IB01_LIB_ROOT . '/dao/CBot.php');
require_once(IB01_LIB_ROOT . '/view/CDocumentBuilder.php');
require_once(IB01_LIB_ROOT . '/view/CRedirector.php');
require_once(IB01_LIB_ROOT . '/state/IState.php');

/**
 *	ぼっとにお題を教えるページを表示します。
 */
class CSceneNewBotPost
	implements IState
{

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	既定の値一覧。 */
	private $format = array(
		'childs' => '10',
		'x' => '8',
		'y' => '0',
		'theme' => ''
	);

	/**	ぼっとさん。 */
	private $bot = null;

	/**	エラー表示。 */
	private $errors = null;

	/**
	 *	この状態のオブジェクトを取得します。
	 *
	 *	@return IState この状態のオブジェクト。
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			self::$instance = new CSceneNewBotPost();
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
				$theme = trim($_POST['theme']);
				$len = strlen($theme);
				if($len < 1 || $len > 255)
				{
					throw new Exception(_('お題は1～255バイト以内。'));
				}
				if(strlen($_POST['description']) <= 2)
				{
					throw new Exception(_('お題なしは受理不可。'));
				}
				$x = intval($_POST['x']);
				$y = intval($_POST['y']);
				$childs = intval($_POST['childs']);
				if($y <= 0)
				{
					$y = $x;
					$_POST['y'] = sprintf('%d', $y);
				}
				if($x <= 0 || $x > 128 || $y <= 0 || $y > 128)
				{
					throw new Exception(_('不正なサイズは受理不可。'));
				}
				if($childs < 10 || $childs > 100)
				{
					throw new Exception(_('ぼっとさんをいじめちゃだめー。'));
				}
				$bot = new CBot();
				$bot->setChilds($childs);
				$bot->setSize($x, $y);
				$bot->commit();
				$this->bot = $bot;
				throw new Exception(_('実は未実装。'));
			}
		}
		catch(Exception $e)
		{
			$this->errors = $e->getMessage();
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
				$query = $this->bot->getID();
			}
			else
			{
				$query = array(
					'f' => 'core/newGame',
					'childs' => $_POST['childs'],
					'x' => $_POST['x'],
					'y' => $_POST['y'],
					'theme' => $_POST['theme'],
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