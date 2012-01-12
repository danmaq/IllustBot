<?php

require_once(IB01_CONSTANTS);
require_once(IB01_LIB_ROOT . '/dao/CChild.php');
require_once(IB01_LIB_ROOT . '/dao/CImage.php');
require_once(IB01_LIB_ROOT . '/state/scene/ranking/CSceneTop.php');
require_once(IB01_LIB_ROOT . '/view/CRedirector.php');

/**
 *	予備学習させます。
 */
class CSceneAutoStudy
	implements IState
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	親ぼっと。 */
	public $bot = null;

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
		try
		{
			$bot = $this->bot;
			if($bot === null || $bot->getExampleHash() < 0)
			{
				throw new Exception(_('ぼっとが変ですよん。。。'));
			}
			if($entity->connectDatabase())
			{
				$parents = $this->getChildPixels($bot);
				$params = count($parents) == 0 ? $bot->getChilds() : $parents;
				$img = new CImage($bot->getExampleHash());
				$result = CPixels::study($img->getPixels(), $params);
				$child = $this->createChildFromPixels($bot, $result);
				$bot->nextGeneration();
				$bot->commit();
				if($child === null)
				{
					throw new Exception(_('ぼっとがいるようで、実はいなかった、異常な事態(素敵な事態)'));
				}
				$this->id = $child->getID();
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
		$this->bot = null;
	}

	/**
	 *	ピクセル情報一覧を作成します。
	 *
	 *	@param CBot $bot 親ぼっとオブジェクト。
	 *	@return array[CPixels] ピクセル情報一覧。
	 */
	private function getChildPixels(CBot $bot)
	{
		$result = array();
		$bot->prevGeneration();
		$childs = CChild::getFromOwner($bot);
		$bot->nextGeneration();
		for($i = count($childs); --$i >= 0; )
		{
			$img = new CImage($childs[$i]->getHash());
			array_push($result, $img->getPixels());
		}
		return $result;
	}

	/**
	 *	ピクセル情報一覧を作成します。
	 *
	 *	@param CBot $bot 親ぼっとオブジェクト。
	 *	@param array[CPixels] $pixels ピクセル情報一覧。
	 *	@return CChild 生成された子ぼっとのうちの1体。
	 */
	private function createChildFromPixels(CBot $bot, $pixels)
	{
		$result = null;
		$gene = $bot->getGeneration();
		for($i = count($pixels); --$i >= 0; )
		{
			$cimg = new CImage($pixels[$i]);
			$cimg->commit();
			$result = new CChild();
			$result->setOwner($bot);
			$result->setGeneration($gene);
			$result->setHash($cimg->getID());
			$result->commit();
		}
		return $result;
	}
}

?>