<?php

require_once(IB01_LIB_ROOT . '/file/CFileSQLImage.php');
require_once(IB01_LIB_ROOT . '/util/CPixels.php');

/**
 *	画像DAOクラス。
 */
class CImage
	implements IDAO
{

	/**	初期化済みかどうか。 */
	private static $initialized = false;

	/**	ピクセル情報。 */
	private $pixels = null;

	/**	ハッシュ。 */
	private $id = -1;

	/**
	 *	テーブルの初期化をします。
	 */
	public static function initialize()
	{
		if(!self::$initialized)
		{
			CDataEntity::initializeTable();
			CDBManager::getInstance()->execute(CFileSQLImage::getInstance()->ddl);
			self::$initialized = true;
		}
	}

	/**
	 *	コンストラクタ。
	 *
	 *	@param mixed $data ハッシュ または 画像データ。
	 */
	public function __construct($data = null)
	{
		if(is_int($data))
		{
			$this->id = $data;
			$this->rollback();
		}
		elseif(is_string($data))
		{
			$pixels = new CPixels();
			$pixels->createFromData($data);
			$this->pixels = $pixels;
		}
		else
		{
			throw new Exception(_('画像データかハッシュ情報を指定してください。'));
		}
	}

	/**
	 *	ハッシュを取得します。
	 *
	 *	@return int ハッシュ。
	 */
	public function getID()
	{
		return $this->id;
	}

	/**
	 *	実体オブジェクトを取得します。
	 *
	 *	@return CDataEntity 実体オブジェクトは存在しないため、常にnull。
	 */
	public function getEntity()
	{
		return null;
	}

	/**
	 *	ピクセル情報を取得します。
	 *
	 *	@return CPixels ピクセル情報。
	 */
	public function &getPixels()
	{
		return $this->pixels;
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
			CFileSQLImage::getInstance()->selectExists,
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
			$result = $db->execute(CFileSQLImage::getInstance()->delete,
				$this->createDBParams());
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
		$raw = $this->getPixels()->render();
		$this->hash = hash('crc32', $raw);
		if(!$this->isExists())
		{
			$pdo = $db->getPDO();
			try
			{
				$pdo->beginTransaction();
				$db->execute(CFileSQLImage::getInstance()->insert, $params)
				$fcache = CFileSQLImage::getInstance();
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
					throw new Exception(_('DB書き込みに失敗'));
				}
				$pdo->commit();
			}
			catch(Exception $e)
			{
				error_log($e);
				$pdo->rollback();
			}

		}
		$db = CDBManager::getInstance();
		$pdo = $db->getPDO();
		try
		{
			$pdo->beginTransaction();
			$fcache = CFileSQLImage::getInstance();
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
			CFileSQLImage::getInstance()->select, $this->createDBParams());
		$result = count($body) > 0;
		if($result)
		{
			$this->createEntity($body[0]['ENTITY_ID']);
			$this->setOwner($body[0]['OWNER']);
			$this->setGeneration($body[0]['GENERATION']);
			$this->setVoteCount($body[0]['VOTE_COUNT']);
			$this->setScore($body[0]['SCORE']);
			$body =& $this->storage();
			$this->pixels = new CPixels(gzinflate(base64_decode($body['m'])));
		}
		return $result;
	}

	/**
	 *	レンダリングします。
	 *
	 *	@return string レンダリングされた画像データ。(PNG24フォーマット)
	 */
	private function render()
	{
		return $this->pixels->render();
	}

	/**
	 *	DB受渡し用のパラメータを生成します。
	 *
	 *	@return array DB受渡し用のパラメータ。
	 */
	private function createDBParams()
	{
		return array('hash' => array($this->getID(), PDO::PARAM_INT));
	}
}

?>
