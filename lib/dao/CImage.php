<?php

require_once(IB01_LIB_ROOT . '/file/CFileSQLImage.php');
require_once(IB01_LIB_ROOT . '/util/CPixels.php');

/**
 *	画像DAOクラス。
 */
class CImage
	implements IDAO
{

	//* fields ────────────────────────────────*

	/**	初期化済みかどうか。 */
	private static $initialized = false;

	/**	ピクセル情報。 */
	private $pixels = null;

	/**	ハッシュ。 */
	private $id = -1;

	//* constructor & destructor ───────────────────────*

	/**
	 *	コンストラクタ。
	 *
	 *	@param mixed $data ハッシュ または 画像データ、またはCPixelsオブジェクト。
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
			$this->setRawData($data);
		}
		elseif($data instanceof CPixels)
		{
			$this->pixels = $data;
		}
		else
		{
			throw new Exception(_('画像データかハッシュ情報を指定してください。'));
		}
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
			CDBManager::getInstance()->execute(CFileSQLImage::getInstance()->ddl);
			self::$initialized = true;
		}
	}

	//* instance methods ───────────────────────────*

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
		$result = false;
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
	 *	同時にハッシュを更新し、DBと重複している場合は何もせずに戻ります。
	 *
	 *	@return boolean 成功した場合、true。
	 */
	public function commit()
	{
		self::initialize();
		$raw = $this->getPixels()->render();
		$this->id = hash('crc32', $raw);
		$result = $this->isExists();
		if(!$result)
		{
			$pdo = $db->getPDO();
			try
			{
				$pdo->beginTransaction();
				$result = $db->execute(CFileSQLImage::getInstance()->insert,
					$this->createDBParams() + array('body' => array($raw, PDO::PARAM_LOB));
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
		$result = true;
		try
		{
			$body = CDBManager::getInstance()->singleFetch(
				CFileSQLImage::getInstance()->select, 'BODY', $this->createDBParams());
			$this->setRawData($body);
		}
		catch(Exception e)
		{
			error_log($e);
			$result = false;
		}
		return $result;
	}

	/**
	 *	レンダリングします。
	 *
	 *	@return string レンダリングされた画像データ。(PNG24フォーマット)
	 */
	public function render()
	{
		return $this->pixels->render();
	}

	/**
	 *	RAWデータを設定します。
	 *
	 *	@return string RAWデータ。
	 */
	private function setRawData($data)
	{
		$pixels = new CPixels();
		$pixels->createFromData($data);
		$this->pixels = $pixels;
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