<?php

require_once('CDataIndex.php');
require_once(IB01_LIB_ROOT . '/file/CFileSQLBot.php');

/**
 *	ぼっとDAOクラス。
 */
class CBot
	extends CDataIndex
{

	//* fields ────────────────────────────────*

	/**	実体のメンバとデフォルト値一覧。 */
	private static $format = array(
		'parent' => null,
		'x' => 8,
		'y' => 8,
		'childs' => 10,
		'example' => -1,
	);

	/**	ぼっと数。 */
	private static $members = -1;

	/**	お題。 */
	private $theme;

	/**	世代。 */
	private $generation;

	/**	累計スコア。 */
	private $score;

	/**	公開対象であるかどうか。 */
	private $publication = true;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 *
	 *	@param string $entity_id ぼっとID。実体IDと兼用(GUID)。
	 */
	public function __construct($entity_id = null)
	{
		parent::__construct(self::$format, $entity_id);
		self::getTotalCount();
		$this->setGeneration(0);
		$this->setScore(0);
	}

	//* class methods ────────────────────────────-*

	/**
	 *	ぼっと一覧を取得します。
	 *
	 *	@param CPager $pager ページャ オブジェクト。
	 *	@return array ぼっと一覧。
	 */
	public static function getAllOrderNewbie(CPager $pager = null)
	{
		return self::getAll(CFileSQLBot::getInstance()->selectNewbie, $pager);
	}

	/**
	 *	ぼっと一覧を取得します。
	 *
	 *	@param CPager $pager ページャ オブジェクト。
	 *	@return array ぼっと一覧。
	 */
	public static function getAllOrderScore(CPager $pager = null)
	{
		return self::getAll(CFileSQLBot::getInstance()->selectScore, $pager);
	}

	/**
	 *	ぼっと一覧を取得します。
	 *
	 *	@param CPager $pager ページャ オブジェクト。
	 *	@return array ぼっと一覧。
	 */
	public static function getAllOrderGeneration(CPager $pager = null)
	{
		return self::getAll(CFileSQLBot::getInstance()->selectGeneration, $pager);
	}

	/**
	 *	ぼっと一覧を取得します。
	 *
	 *	@param string $sql SQLクエリ。
	 *	@param CPager $pager ページャ オブジェクト。
	 *	@return array ぼっと一覧。
	 */
	public static function getAll($sql, CPager $pager = null)
	{
		$result = array();
		$totalCount = self::getTotalCount();
		if($totalCount > 0)
		{
			if($pager === null)
			{
				$pager = new CPager();
			}
			$all = CDBManager::getInstance()->execAndFetch($sql, $pager->getLimit());
			foreach($all as $item)
			{
				$bot = new CBot($item['ENTITY_ID']);
				if($bot->rollback())
				{
					array_push($result, $bot);
				}
			}
			$pager->setMaxPagesFromCount($totalCount);
		}
		return $result;
	}

	/**
	 *	ぼっと数を取得します。
	 *
	 *	ここで同時にテーブルの初期化も行われます。
	 *
	 *	@return integer ユーザ数。
	 */
	public static function getTotalCount()
	{
		if(self::$members < 0)
		{
			CDataEntity::initializeTable();
			$fcache = CFileSQLBot::getInstance();
			$db = CDBManager::getInstance();
			$db->execute($fcache->ddl);
			self::$members = $db->singleFetch($fcache->selectCount, 'COUNT');
		}
		return self::$members;
	}

	//* instance methods ───────────────────────────*

	/**
	 *	ぼっとIDを取得します。
	 *
	 *	@return string ぼっとID。実体IDと兼用(GUID)。
	 */
	public function getID()
	{
		return $this->getEntity()->getID();
	}

	/**
	 *	公開対象であるかどうかを取得します。
	 *
	 *	@return boolean 公開対象である場合、true。
	 */
	public function isPublication()
	{
		return $this->publication;
	}

	/**
	 *	公開対象であるかどうかを設定します。
	 *
	 *	@param boolean $value 公開対象であるかどうか。
	 */
	public function setPublication($value)
	{
		$this->publication = $value;
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
	 *	世代レベルをデクリメントします。
	 */
	public function prevGeneration()
	{
		$this->generation--;
	}

	/**
	 *	世代レベルをインクリメントします。
	 */
	public function nextGeneration()
	{
		$this->generation++;
	}

	/**
	 *	累計スコアを取得します。
	 *
	 *	@return integer 累計スコア。
	 */
	public function getScore()
	{
		return $this->score;
	}

	/**
	 *	累計スコアを設定します。
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
	 *	お題を取得します。
	 *
	 *	@return string お題。
	 */
	public function getTheme()
	{
		return $this->theme;
	}

	/**
	 *	お題を設定します。
	 *
	 *	@param string $value お題。
	 */
	public function setTheme($value)
	{
		$this->theme = $value;
	}

	/**
	 *	元となった親ぼっとIDを取得します。
	 *
	 *	@return string 親ぼっとID(GUID)。
	 */
	public function getParent()
	{
		$body =& $this->storage();
		return $body['parent'];
	}

	/**
	 *	元となった親ぼっとIDを設定します。
	 *
	 *	@param string $value 親ぼっとID(GUID)。
	 */
	public function setParent($value)
	{
		$body =& $this->storage();
		$body['parent'] = $value;
	}

	/**
	 *	子ぼっと数を取得します。
	 *
	 *	@return string 子ぼっと数。
	 */
	public function getChilds()
	{
		$body =& $this->storage();
		return $body['childs'];
	}

	/**
	 *	子ぼっと数を設定します。
	 *
	 *	@param string $value 子ぼっと数。
	 */
	public function setChilds($value)
	{
		$body =& $this->storage();
		$body['childs'] = $value;
	}

	/**
	 *	サンプル画像のハッシュを取得します。
	 *
	 *	@return int サンプル画像のハッシュ。
	 */
	public function getExampleHash()
	{
		$body =& $this->storage();
		return $body['example'];
	}

	/**
	 *	サンプル画像のハッシュを設定します。
	 *
	 *	@param int $value サンプル画像のハッシュ。
	 */
	public function setExampleHash($value)
	{
		$body =& $this->storage();
		$body['example'] = $value;
	}

	/**
	 *	画像サイズを取得します。
	 *
	 *	@return array 画像サイズ。{x => x, y => y}
	 */
	public function getSize()
	{
		$body =& $this->storage();
		return array(
			'x' => $body['x'],
			'y' => $body['y']);
	}

	/**
	 *	画像サイズを設定します。
	 *
	 *	@param integer $x X軸の画像サイズ。
	 *	@param integer $y Y軸の画像サイズ。
	 */
	public function setSize($x, $y)
	{
		$body =& $this->storage();
		$body['x'] = $x;
		$body['y'] = $y;
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
		return self::getTotalCount() > 0 &&
			CDBManager::getInstance()->singleFetch(CFileSQLBot::getInstance()->selectExists,
			'EXIST', $this->createDBParamsOnlyEID());
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
			self::getTotalCount();
			$pdo->beginTransaction();
			$result = $db->execute(CFileSQLBot::getInstance()->delete,
				$this->createDBParamsOnlyEID()) && parent::delete();
			if(!$result)
			{
				throw new Exception(_('DB書き込みに失敗'));
			}
			$pdo->commit();
			self::$members--;
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
		self::getTotalCount();
		$db = CDBManager::getInstance();
		$pdo = $db->getPDO();
		try
		{
			$pdo->beginTransaction();
			$exists = $this->isExists();
			$fcache = CFileSQLBot::getInstance();
			$result = $this->getEntity()->commit();
			if($result)
			{
				$params = $this->createDBParams() + $this->createDBParamsOnlyEID();
				$sql = $fcache->update;
				if(!$exists)
				{
					$params += $this->createDBParamsTheme();
					$sql = $fcache->insert;
				}
				$result = $db->execute($sql, $params);
			}
			if(!$result)
			{
				throw new Exception(_('DB書き込みに失敗'));
			}
			$pdo->commit();
			if(!$exists)
			{
				self::$members++;
			}
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
			CFileSQLBot::getInstance()->select, $this->createDBParamsOnlyEID());
		$result = count($body) > 0;
		if($result)
		{
			$this->createEntity($this->getID());
			$this->setPublication($body[0]['PUBLICATION']);
			$this->setGeneration($body[0]['GENERATION']);
			$this->setTheme($body[0]['THEME']);
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
		return array(
			'score' => array($this->getScore(), PDO::PARAM_INT),
			'generation' => array($this->getGeneration(), PDO::PARAM_INT),
			'publication' => array($this->isPublication(), PDO::PARAM_BOOL)
		);
	}

	/**
	 *	DB受渡し用のパラメータを生成します。
	 *
	 *	@return array DB受渡し用のパラメータ。
	 */
	private function createDBParamsTheme()
	{
		return array(
			'theme' => array($this->getTheme(), PDO::PARAM_STR)
		);
	}
}

?>