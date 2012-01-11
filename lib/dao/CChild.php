<?php

require_once('CBot.php');
require_once('CImage.php');
require_once(IB01_LIB_ROOT . '/file/CFileSQLChild.php');

/**
 *	子ぼっとDAOクラス。
 */
class CChild
	extends CDataIndex
{

	//* fields ────────────────────────────────*

	/**	実体のメンバとデフォルト値一覧。 */
	private static $format = array(
		'hash'	=> -1,
	);

	/**	初期化済みかどうか。 */
	private static $initialized = false;

	/**	子ぼっとID。 */
	private $id;

	/**	親ぼっと。 */
	private $owner = null;

	/**	世代。 */
	private $generation = 0;

	/**	投票数。 */
	private $voteCount = 0;

	/**	スコア。 */
	private $score = 0;

	/**	未投票ぼっとの数。 */
	private $amount = -1;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 *
	 *	@param string $id 子ぼっとID。
	 *	@param string $entity_id 実体ID(GUID)。
	 */
	public function __construct($id = null)
	{
		parent::__construct(self::$format);
		if($id === null)
		{
			$id = CDataEntity::createGUID();
		}
		$this->id = $id;
	}

	//* class methods ────────────────────────────-*

	/**
	 *	テーブルの初期化をします。
	 */
	public static function initialize()
	{
		if(!self::$initialized)
		{
			CDataEntity::initializeTable();
			CDBManager::getInstance()->execute(CFileSQLChild::getInstance()->ddl);
			self::$initialized = true;
		}
	}

	/**
	 *	交叉遺伝します。
	 *
	 *	@param CChild $a 子ぼっと。
	 *	@param CChild $b 子ぼっと。
	 *	@return CChild 子ぼっと。
	 */
	public static function inheritance(CChild $a, CChild $b)
	{
		$ia = new CImage($a->getHash());
		$ib = new CImage($b->getHash());
		$ic = new CImage(CPixels::inheritance($ia->getPixels(), $ib->getPixels()));
		$ic->commit();
		$result = new CChild();
		$result->setOwner($a->getOwner());
		$result->setGeneration($a->getGeneration() + 1);
		$result->setHash($ic->getID());
		$result->commit();
		return $result;
	}

	/**
	 *	未投票の子ぼっとを1件取得します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@return CChild 子ぼっと。
	 */
	public static function getUnvotedFromOwner(CBot $owner)
	{
		$result = null;
		try
		{
			self::initialize();
			$info = CDBManager::getInstance()->singleFetch(
				CFileSQLChild::getInstance()->selectUnvoted, 'ID',
				self::createDBParamsFromOwner($owner));
			$result = new CChild($info);
			if(!$result->rollback())
			{
				throw new Exception();
			}
		}
		catch(Exception $e)
		{
			$result = null;
		}
		return $result;
	}

	/**
	 *	子ぼっとを取得します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@return int 子ぼっと一覧。
	 */
	public static function getFromOwner(CBot $owner)
	{
		self::initialize();
		$info = CDBManager::getInstance()->execAndFetch(
			CFileSQLChild::getInstance()->selectFromOwner,
			self::createDBParamsFromOwner($owner));
		$result = array();
		$len = count($info);
		for($i = 0; $i < $len; $i++)
		{
			$obj = new CChild($info[$i]['ID']);
			if($obj->rollback())
			{
				array_push($result, $obj);
			}
		}
		return $result;
	}

	/**
	 *	子ぼっと数を取得します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@return int 子ぼっとの数。
	 */
	public static function getCountAllFromOwner(CBot $owner)
	{
		return self::getNumberFromOwner(
			$owner, CFileSQLChild::getInstance()->selectExistsFromOwner);
	}

	/**
	 *	子ぼっと数を取得します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@return int 子ぼっとの数。
	 */
	public static function getCountUnvotedFromOwner(CBot $owner)
	{
		return self::getNumberFromOwner(
			$owner, CFileSQLChild::getInstance()->selectUnvotedCount);
	}

	/**
	 *	子ぼっと数を取得します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@param string $sql SQLクエリ。
	 *	@return int 子ぼっとの数。
	 */
	public static function getNumberFromOwner(CBot $owner, $sql)
	{
		self::initialize();
		return CDBManager::getInstance()->singleFetch($sql, 'COUNT',
			self::createDBParamsFromOwner($owner));
	}

	/**
	 *	DB受渡し用のパラメータを生成します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@return array DB受渡し用のパラメータ。
	 */
	private static function createDBParamsFromOwner(CBot $owner)
	{
		return array(
			'owner' => array($owner->getID(), PDO::PARAM_STR),
			'generation' => array($owner->getGeneration(), PDO::PARAM_INT));
	}

	//* instance methods ───────────────────────────*

	/**
	 *	子ぼっとIDを取得します。
	 *
	 *	@return string 子ぼっとID。(GUID)。
	 */
	public function getID()
	{
		return $this->id;
	}

	/**
	 *	親ぼっとを取得します。
	 *
	 *	@param boolean $autoCreate 自動的にオブジェクトを生成するかどうか。
	 *	@return CBot 親ぼっと。
	 */
	public function getOwner($autoCreate = true)
	{
		$result = $this->owner;
		if($autoCreate && is_string($result))
		{
			$result = new CBot($result);
			$result->rollback();
		}
		return $result;
	}

	/**
	 *	親ぼっとを設定します。
	 *
	 *	@param CBot or string $value 親ぼっと。
	 */
	public function setOwner($value)
	{
		$this->owner = $value;
	}

	/**
	 *	世代レベルを取得します。
	 *
	 *	@return integer 世代レベル。
	 */
	public function getGeneration()
	{
		return $this->generation;
	}

	/**
	 *	世代レベルを設定します。
	 *
	 *	@param integer $value 世代レベル。
	 */
	public function setGeneration($value)
	{
		$this->generation = $value;
	}

	/**
	 *	累計投票数を取得します。
	 *
	 *	@return integer 累計投票数。
	 */
	public function getVoteCount()
	{
		return $this->voteCount;
	}

	/**
	 *	累計投票数を設定します。
	 *
	 *	@param integer $value 累計投票数。
	 */
	public function setVoteCount($value)
	{
		$this->voteCount = $value;
	}

	/**
	 *	累計投票数をインクリメントします。
	 */
	public function addVoteCount()
	{
		$this->voteCount++;
	}

	/**
	 *	スコアを取得します。
	 *
	 *	@return integer 累計スコア。
	 */
	public function getScore()
	{
		return $this->score;
	}

	/**
	 *	スコアを設定します。
	 *
	 *	@param integer $value 累計スコア。
	 */
	public function setScore($value)
	{
		$this->score = $value;
	}

	/**
	 *	累計スコアを加算します。
	 *
	 *	@param integer $value 累計スコア。
	 */
	public function addScore($value)
	{
		$this->score += $value;
	}

	/**
	 *	画像ハッシュを取得します。
	 *
	 *	@return int 画像ハッシュ。
	 */
	public function getHash()
	{
		$body =& $this->storage();
		return $body['hash'];
	}

	/**
	 *	画像ハッシュを設定します。
	 *
	 *	@param integer $value 画像ハッシュ。
	 */
	public function setHash($value)
	{
		$body =& $this->storage();
		$body['hash'] = $value;
	}

	/**
	 *	未投票ぼっとの数を取得します。
	 *
	 *	@return integer 未投票ぼっとの数。
	 */
	public function getAmount()
	{
		if($this->amount < 0)
		{
			$this->amount = self::getCountUnvotedFromOwner($this->getOwner());
		}
		return $this->amount;
	}

	/**
	 *	次の世代となるクローンを取得します。
	 *
	 *	@return CChild 子ぼっとオブジェクト。
	 */
	public function createNextGeneration()
	{
		$result = new CChild();
		$result->setOwner($this->getOwner(false));
		$result->setGeneration($this->getGeneration() + 1);
		$result->setHash($this->getHash());
		$result->commit();
		return $result;
	}

	/**
	 *	画像をリセットします。
	 */
	public function resetImage()
	{
		$size = $this->getOwner()->getSize();
		if($this->getHash() >= 0)
		{
			$image = new CImage($hash, false);
			$image->delete();
		}
		$p = new CPixels();
		$p->createFromSize($size['x'], $size['y']);
		$image = new CImage($p);
		$image->commit();
		$this->setHash($image->getID());
	}

	/**
	 *	データベースに保存されているかどうかを取得します。
	 *
	 *	注意: この関数は、コミットされているかどうかを保証するものではありません。
	 *
	 *	@return boolean 保存されている場合、true。
	 */
	public function isExists()
	{
		self::initialize();
		return CDBManager::getInstance()->singleFetch(
			CFileSQLChild::getInstance()->selectExists,
			'EXIST', $this->createDBParams());
	}

	/**
	 *	削除します。
	 *
	 *	@return boolean 削除に成功した場合、true。
	 */
	public function delete()
	{
		$db = CDBManager::getInstance();
		$pdo = $db->getPDO();
		try
		{
			self::initialize();
			$pdo->beginTransaction();
			$result = $db->execute(CFileSQLChild::getInstance()->delete,
				$this->createDBParams()) && parent::delete();
			if(!$result)
			{
				throw new Exception(_('DB書き込みに失敗'));
			}
			$pdo->commit();
		}
		catch(Exception $e)
		{
			error_log($e->toString());
			$pdo->rollback();
		}
		return $result;
	}

	/**
	 *	コミットします。
	 *
	 *	@return boolean 成功した場合、true。
	 */
	public function commit()
	{
		self::initialize();
		$db = CDBManager::getInstance();
		$pdo = $db->getPDO();
		$result = false;
		if($this->getHash() < 0)
		{
			$this->resetImage();
		}
		try
		{
			$pdo->beginTransaction();
			$fcache = CFileSQLChild::getInstance();
			$sql = null;
			$params = null;
			if($this->isExists())
			{
				$sql = $fcache->update;
				$params = $this->createDBParams() + array(
					'vote_count' => array($this->getVoteCount(), PDO::PARAM_INT),
					'score' => array($this->getScore(), PDO::PARAM_INT));
			}
			else
			{
				$sql = $fcache->insert;
				$params = $this->createDBParams() +	$this->createDBParamsOnlyEID() + array(
					'owner' => array($this->getOwner()->getID(), PDO::PARAM_STR),
					'generation' => array($this->getGeneration(), PDO::PARAM_INT));
			}
			$result = $this->getEntity()->commit() && $db->execute($sql, $params);
			if(!$result)
			{
				throw new Exception(_('DB書き込みに失敗'));
			}
			$pdo->commit();
		}
		catch(Exception $e)
		{
			error_log($e);
			$pdo->rollback();
		}
		return $result;
	}

	/**
	 *	ロールバックします。
	 *
	 *	@return boolean 成功した場合、true。
	 */
	public function rollback()
	{
		$body = CDBManager::getInstance()->execAndFetch(
			CFileSQLChild::getInstance()->select, $this->createDBParams());
		$result = count($body) > 0;
		if($result)
		{
			$this->createEntity($body[0]['ENTITY_ID']);
			$this->setOwner($body[0]['OWNER']);
			$this->setGeneration($body[0]['GENERATION']);
			$this->setVoteCount($body[0]['VOTE_COUNT']);
			$this->setScore($body[0]['SCORE']);
		}
		return $result;
	}

	/**
	 *	DB受渡し用のパラメータを生成します。
	 *
	 *	@return array DB受渡し用のパラメータ。
	 */
	private function createDBParams()
	{
		return array('id' => array($this->getID(), PDO::PARAM_STR));
	}
}

?>