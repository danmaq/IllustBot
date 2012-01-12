<?php

require_once(IB01_CONSTANTS);
require_once('CSceneRedirectChild.php');
require_once(IB01_LIB_ROOT . '/dao/CImage.php');
require_once(IB01_LIB_ROOT . '/state/scene/ranking/CSceneTop.php');

/**
 *	ぼっとを育てるページを表示します。
 */
class CSceneViewRawImage
	implements IState
{

	//* fields ────────────────────────────────*

	/**	クラス オブジェクト。 */
	private static $instance = null;

	/**	画像DAO。 */
	private $image;

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
			self::$instance = new CSceneViewRawImage();
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
			if(!isset($_GET['id']))
			{
				throw new Exception(_('画像IDを指名してください。'));
			}
			if($entity->connectDatabase())
			{
				$image = CImage::directLoad($_GET['id']);
				if($image === null)
				{
					throw new Exception(_('存在しない画像IDです。'));
				}
				$this->image = $image;
			}
		}
		catch(Exception $e)
		{
			$_GET['err'] = $e->getMessage();
			// TODO : 存在しない場合は×アイコンを出力するようにする
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
			$fn = '';
			$ln = 0;
			header('Content-Type: image/png');
			header(sprintf('Content-Disposition: inline; filename=%d.png', $_GET['id']));
			echo $this->image;
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