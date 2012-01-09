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
	 *	テーブルの初期化をします。
	 */
	public static function initialize()
	{
		if(self::$members < 0)
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
	 */
	public function __construct($id = null)
	{
		parent::__construct(self::$format);
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
			this->amount = CDBManager::getInstance()->singleFetch(
				CFileSQLChild::getInstance()->selectUnvotedCount,
				'EXIST', $this->createDBParamsFromOwner());
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
					resetPixels();
				}
				$storage &= $this->storage();
				$storage['m'] = base64_encode(gzinflate($this->createRawPixels()));
				$sql = $fcache->insert;
				$params =
					$this->createDBParams() +
					$this->createDBParamsFromOwner() +
					$this->createDBParamsOnlyEID();
				$result = $this->getEntity()->commit();
			}
			if(!($result && $db->execute($sql, $params)))
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
			$body =& $item->storage();
			$raw = gzinflate(base64_decode($body['m']));
			$len = strlen($raw);
			$pixels = array();
			for($i = 0; $i < $len; $i += 2)
			{
				$color = new CRGB(substr($raw, $i, 2));
				aray_push($pixels, $color);
			}
			$this->pixels = $pixels;
		}
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
		for($i = 0; $i < $len; $i += 2)
		{
			$color = new CRGB();
			aray_push($pixels, $color);
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
			$result = $pixels[i]->getRGBRaw() . $result;
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