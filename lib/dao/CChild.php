<?php

require_once('CBot.php');
require_once(IB01_LIB_ROOT . '/file/CFileSQLChild.php');
require_once(IB01_LIB_ROOT . '/util/CRGB.php');

/**
 *	子ぼっとDAOクラス。
 */
class CChild
	extends CDataIndex
{

	/**	実体のメンバとデフォルト値一覧。 */
	private static $format = array(
		'm'	=> '',
	);

	/**	初期化済みかどうか。 */
	private static $initialized = false;

	/**	ピクセル情報。 */
	private $pixels;

	/**	子ぼっとID。 */
	private $id;

	/**	親ぼっと。 */
	private $owner;

	/**	世代。 */
	private $generation;

	/**	投票数。 */
	private $voteCount;

	/**	スコア。 */
	private $score;

	/**	未投票ぼっとの数。 */
	private $amount;

	/**
	 *	交叉遺伝します。
	 *	交叉アルゴリズムに一様交叉を使用します。
	 *
	 *	@param CChild $a 子ぼっと。
	 *	@param CChild $b 子ぼっと。
	 *	@return CChild 子ぼっと。
	 */
	public static function inheritance(CChild $a, CChild $b)
	{
		$result = new CChild();
		$result->setOwner($a->getOwner());
		$result->setGeneration($a->getGeneration() + 1);
		$result->resetPixels();
		$pixels =& $result->getPixels();
		$pixelsA =& $a->getPixels();
		$pixelsB =& $b->getPixels();
		for($i = count($pixels); --$i >= 0; )
		{
			$rnd = mt_rand(0, 65535);
			$pixels[$i] = ($rnd & 1) == 0 ? $pixelsA[$i] : $pixelsB[$i];
			if($rnd < 1024)
			{
				$pixels[$i] = new CRGB();
			}
		}
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
		self::initialize();
		$params = array(
			'owner' => array($owner->getID(), PDO::PARAM_STR),
			'generation' => array($owner->getGeneration(), PDO::PARAM_INT)
		);
		$info = CDBManager::getInstance()->singleFetch(
			CFileSQLChild::getInstance()->selectUnvoted, 'ID', $params);
		$result = new CChild($info);
		if(!$result->rollback())
		{
			$result = null;
		}
		return $result;
	}

	/**
	 *	子ぼっとを取得します。
	 *
	 *	@param CBot $owner 親ぼっと。
	 *	@return int 子ぼっとの数。
	 */
	public static function getFromOwner(CBot $owner)
	{
		self::initialize();
		$params = array(
			'owner' => array($owner->getID(), PDO::PARAM_STR),
			'generation' => array($owner->getGeneration(), PDO::PARAM_INT)
		);
		$info = CDBManager::getInstance()->execAndFetch(
			CFileSQLChild::getInstance()->selectFromOwner, $params);
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
		$params = array(
			'owner' => array($owner->getID(), PDO::PARAM_STR),
			'generation' => array($owner->getGeneration(), PDO::PARAM_INT)
		);
		return CDBManager::getInstance()->singleFetch($sql, 'COUNT', $params);
	}

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
	 *	コンストラクタ。
	 *
	 *	@param string $id 子ぼっとID。
	 *	@param string $entity_id 実体ID(GUID)。
	 */
	public function __construct($id = null, $entity_id = null)
	{
		parent::__construct(self::$format, $entity_id);
		self::initialize();
		if($id === null)
		{
			$id = CDataEntity::createGUID();
		}
		$this->id = $id;
		$this->pixels = array();
		$this->setGeneration(0);
		$this->setVoteCount(0);
		$this->setScore(0);
	}

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
	 *	@return CBot 親ぼっと。
	 */
	public function getOwner()
	{
		$result = $this->owner;
		if(is_string($result))
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
	 *	ピクセル情報を取得します。
	 *
	 *	@return array ピクセル情報。
	 */
	public function &getPixels()
	{
		return $this->pixels;
	}

	/**
	 *	未投票ぼっとの数を取得します。
	 *
	 *	@return integer 未投票ぼっとの数。
	 */
	public function getAmount()
	{
		if($this->amount === null)
		{
			$this->amount = self::getCountUnvotedFromOwner($this->getOwner());
		}
		return $this->amount;
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
		try
		{
			$pdo->beginTransaction();
			$fcache = CFileSQLChild::getInstance();
			$sql = null;
			$params = null;
			$result = true;
			if($this->isExists())
			{
				$sql = $fcache->update;
				$params = $this->createDBParams() + $this->createDBParamsScore();
			}
			else
			{
				if(count($this->getPixels()) == 0)
				{
					$this->resetPixels();
				}
				$storage =& $this->storage();
				$storage['m'] = base64_encode(gzdeflate($this->createRawPixels()));
				$sql = $fcache->insert;
				$params =
					$this->createDBParams() +
					$this->createDBParamsFromOwner() +
					$this->createDBParamsOnlyEID();
				$entity = $this->getEntity();
				$result = $entity->isExists() || $entity->commit();
			}
			if(!($result && $db->execute($sql, $params)))
			{
error_log($db->execute($sql, $params));
error_log($sql);
error_log(print_r($params, true));
				throw new Exception(_('DEAD!'));
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
			$body =& $this->storage();
			$raw = gzinflate(base64_decode($body['m']));
			$len = strlen($raw);
			$pixels = array();
			for($i = 0; $i < $len; $i += 2)
			{
				$color = new CRGB(substr($raw, $i, 2));
				array_push($pixels, $color);
			}
			$this->pixels = $pixels;
		}
		return $result;
	}

	/**
	 *	クローンを生成します。
	 *	世代レベルは自動的にインクリメントします。
	 *	実体はクローンしません。(シャローコピーに近いです)
	 *
	 *	@return CChild クローン オブジェクト。
	 */
	public function shallowCopy()
	{
		$result = new CChild();
		$result->setEntity($this->getEntity());
		$result->setOwner($this->owner);
		$result->setGeneration($this->getGeneration() + 1);
		$result->commit();
		return $result;
	}

	/**
	 *	ピクセル情報をリセットします。
	 */
	public function resetPixels()
	{
		$size = $this->getOwner()->getSize();
		$len = $size['x'] * $size['y'];
		$pixels = array();
		for($i = 0; $i < $len; $i++)
		{
			$color = new CRGB();
			array_push($pixels, $color);
		}
		$this->pixels = $pixels;
	}

	/**
	 *	生のピクセル情報を取得します。
	 *
	 *	@return string 生のピクセル情報。
	 */
	private function createRawPixels()
	{
		$result = '';
		$pixels = $this->pixels;
		for($i = count($pixels); --$i >= 0; )
		{
			$result = $pixels[$i]->getRGBRaw() . $result;
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
		return array(
			'id' => array($this->getID(), PDO::PARAM_STR)
		);
	}

	/**
	 *	DB受渡し用のパラメータを生成します。
	 *
	 *	@return array DB受渡し用のパラメータ。
	 */
	private function createDBParamsFromOwner()
	{
		return array(
			'owner' => array($this->getOwner()->getID(), PDO::PARAM_STR),
			'generation' => array($this->getGeneration(), PDO::PARAM_INT)
		);
	}

	/**
	 *	DB受渡し用のパラメータを生成します。
	 *
	 *	@return array DB受渡し用のパラメータ。
	 */
	private function createDBParamsScore()
	{
		return array(
			'vote_count' => array($this->getVoteCount(), PDO::PARAM_INT),
			'score' => array($this->getScore(), PDO::PARAM_INT)
		);
	}
}

?>
