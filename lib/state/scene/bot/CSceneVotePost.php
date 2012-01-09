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
class CSceneVotePost
	implements IState
{

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	既定の値一覧。 */
	private $format = array(
		'id' => '',
		'max' => '0',
		'score' => '0'
	);

	/**	子ぼっとオブジェクト。 */
	private $child = null;

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
			self::$instance = new CSceneVotePost();
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
		$this->child = null;
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
				$child = new CChild($_POST['id']);
				if(!$child->rollback())
				{
					throw new Exception(_('存在しないぼっとです。'));
				}
				$this->child = $child;
				$owner = $child->getOwner();
				if($owner->getGeneration() != $child->getGeneration())
				{
					throw new Exception(_('この絵への投票はすでに〆切っています。'));
				}
				$maxScore = intval($_POST['max']);
				$score = round(intval($_POST['score']) - $maxScore / 2);
				$owner->addScore($score);
				$child->addScore($score);
				$child->addVoteCount();
				$child->commit();
				$owner->commit();
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
			$query = array();
			if($this->errors === null)
			{
				$owner = $this->child->getOwner();
				$query = $owner->getID();
			}
			else
			{
				if($this->child === null)
				{
					$query = array(
						'f' => 'core/top',
						'err' => $this->errors);
				}
				else
				{
					$query = $this->child->getID();
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
