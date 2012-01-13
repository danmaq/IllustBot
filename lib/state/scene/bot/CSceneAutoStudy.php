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
				$result = null;
				if(count($parents) > 0)
				{
					$bot->nextGeneration();
					$bot->commit();
					$img = new CImage($bot->getExampleHash());
					$p = $img->getPixels();
					$result = $parents;
					$t = time();
					for($t = time() + 10; $t >= time(); )
					{
						$result = CPixels::study($img->getPixels(), $result);
					}
				}
				$child = $this->createChildFromPixels($bot, $result);
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
		$childs = CChild::getFromOwner($bot);
		for($i = count($childs); --$i >= 0; )
		{
			$img = new CImage($childs[$i]->getHash());
			array_push($result, $img->getPixels());
			$childs[$i]->delete();
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
		if($pixels === null)
		{
			$pixels = array();
			$size = $bot->getSize();
			for($i = $bot->getChilds(); --$i >= 0; )
			{
				$img = new CPixels();
				$img->createFromSize($size['x'], $size['y']);
				array_push($pixels, $img);
			}
		}
		for($i = count($pixels); --$i >= 0; )
		{
			$cimg = new CImage($pixels[$i]);
			$cimg->commit();
			$result = new CChild();
			$result->setOwner($bot);
			$result->setGeneration($gene);
			$result->setHash($cimg->getID());
			$result->addVoteCount();
			$result->commit();
		}
		return $result;
	}
}

?>