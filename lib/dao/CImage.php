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
	 *	@param boolean $autoload 自動的に画像をデータベースから読み出すかどうか。
	 */
	public function __construct($data, $autoload = true)
	{
		if(is_int($data))
		{
			$this->id = $data;
			if($autoload)
			{
				$this->rollback();
			}
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

	/**
	 *	オブジェクトを介さず、直接データをDBから読み出します。
	 *
	 *	@param mixed $data ハッシュ。
	 *	@return string 画像のRAWデータ(PNG)。存在しない場合、null。
	 */
	public static function directLoad($id)
	{
		self::initialize();
		return CDBManager::getInstance()->singleFetch(
			CFileSQLImage::getInstance()->select, 'BODY',
			array('hash' => array($id, PDO::PARAM_INT)));
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
		$result = $this->getID() >= 0;
		if($result)
		{
			self::initialize();
			$result = CDBManager::getInstance()->singleFetch(
				CFileSQLImage::getInstance()->selectExists,
				'EXIST', $this->createDBParams());
		}
		return $result;
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
		$raw = $this->getPixels()->render();
		// 32bit環境では7FFF FFFF以上の整数が扱えない
		$this->id = (int)(floor(hexdec(hash('crc32', $raw)) * 0.5));
		self::initialize();
		$result = $this->isExists();
		if(!$result)
		{
			$db = CDBManager::getInstance();
			$pdo = $db->getPDO();
			try
			{
				$pdo->beginTransaction();
				$result = $db->execute(CFileSQLImage::getInstance()->insert,
					$this->createDBParams() + array('body' => array($raw, PDO::PARAM_LOB)));
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
		$raw = self::directLoad($this->getID());
		$result = $raw !== null;
		if($result)
		{
			$this->setRawData(self::directLoad($this->getID()));
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