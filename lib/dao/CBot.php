<?php

require_once('CDataIndex.php');
require_once(IB01_LIB_ROOT . '/file/CFileSQLBot.php');

/**
 *	ぼっとDAOクラス。
 */
class CBot
	extends CDataIndex
{

	/**	実体のメンバとデフォルト値一覧。 */
	private static $format = array(
		'theme' => '',
		'x' => 8,
		'y' => 8,
		'childs' => 10,
	);

	/**	ぼっと数。 */
	private static $members = -1;

	/**	世代。 */
	private $generation;

	/**	累計スコア。 */
	private $score;

	/**
	 *	ユーザ数を取得します。
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
	 *	お題を取得します。
	 *
	 *	@return string お題。
	 */
	public function getTheme()
	{
		$storage &= $this->storage();
		return $storage['theme'];
	}

	/**
	 *	お題を設定します。
	 *
	 *	@param string $value お題。
	 */
	public function setTheme($value)
	{
		$storage &= $this->storage();
		$storage['theme'] = $value;
	}

	/**
	 *	子ぼっと数を取得します。
	 *
	 *	@return string 子ぼっと数。
	 */
	public function getChilds()
	{
		$storage &= $this->storage();
		return $storage['childs'];
	}

	/**
	 *	子ぼっと数を設定します。
	 *
	 *	@param string $value 子ぼっと数。
	 */
	public function setChilds($value)
	{
		$storage &= $this->storage();
		$storage['childs'] = $value;
	}

	/**
	 *	X軸の画像サイズを取得します。
	 *
	 *	@return array 画像サイズ。{x => x, y => y}
	 */
	public function getSize()
	{
		$storage &= $this->storage();
		return array(
			'x' => $storage['x'],
			'y' => $storage['y']);
	}

	/**
	 *	画像サイズを設定します。
	 *
	 *	@param integer $x X軸の画像サイズ。
	 *	@param integer $y Y軸の画像サイズ。
	 */
	public function setSize($x, $y)
	{
		$storage &= $this->storage();
		$storage['x'] = $x;
		$storage['y'] = $y;
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
		$entity = $this->getEntity();
		$db = CDBManager::getInstance();
		$pdo = $db->getPDO();
		try
		{
			$pdo->beginTransaction();
			$exists = $this->isExists();
			$fcache = CFileSQLBot::getInstance();
			$result = $entity->commit() && $db->execute(
				$exists ? $fcache->update : $fcache->insert,
				$this->createDBParams() + $this->createDBParamsOnlyEID());
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
			$this->createEntity($body[0]['ENTITY_ID']);
			$this->setScore($body[0]['SCORE']);
			$this->setGeneration($body[0]['GENERATION']);
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
			'generation' => array($this->getGeneration(), PDO::PARAM_INT)
		);
	}
}

?>
