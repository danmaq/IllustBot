<?php

require_once(IB01_CONSTANTS);
require_once(IB01_LIB_ROOT . '/dao/CChild.php');
require_once(IB01_LIB_ROOT . '/dao/CComment.php');
require_once(IB01_LIB_ROOT . '/state/IState.php');
require_once(IB01_LIB_ROOT . '/view/CRedirector.php');

/**
 *	ぼっとにお題を教えるページを表示します。
 */
class CSceneCommentPost
	implements IState
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	既定の値一覧。 */
	private static $format = array(
		'id' => '',
		'comment' => ''
	);

	/**	子ぼっとのID。 */
	private $id = null;

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
			self::$instance = new CSceneNewBotPost();
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
		$this->id = null;
		try
		{
			if($_SERVER['REQUEST_METHOD'] !== 'POST')
			{
				throw new Exception(_('POSTメソッド以外は受理不可。'));
			}
			if($entity->connectDatabase())
			{
				$_POST += self::$format;
				$child = new CChild($_POST['id']);
				if(!$child->rollback())
				{
					throw new Exception(_('不正なぼっとIDです。'));
				}
				$this->id = $child->getID();
				$message = trim($_POST['comment']);
				$len = strlen($message);
				if($len < 1 || $len > 80)
				{
					throw new Exception(_('コメントは1～80バイト以内。'));
				}
				$comment = new CComment();
				$comment->setMessage($message);
				$comment->setOwner($child->getOwner());
				$comment->commit();
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
			$query = null;
			if($this->errors === null)
			{
				$query = $this->id;
			}
			else
			{
				$query = array('err' => $this->errors);
				if($this->id != null)
				{
					$query += array(
						'f' => 'core/viewImage',
						'id' => $this->id);
				}
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