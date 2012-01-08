<?php

error_reporting(E_ALL|E_STRICT);

define('IB01_LIB_ROOT', dirname(__FILE__));
define('IB01_ROOT', realpath(IB01_LIB_ROOT . '/..'));
define('IB01_CONSTANTS', IB01_LIB_ROOT . '/CConstants.php');

require_once(IB01_LIB_ROOT . '/util/CErrorException.php');
require_once(IB01_LIB_ROOT . '/entity/CEntity.php');

/**
 *	IB01�����s����N���X�B
 */
class CIB01
{

	/**
	 *	���s���܂��B
	 */
	public static function run()
	{
		date_default_timezone_set('Asia/Tokyo');
		$emptyState = CEmptyState::getInstance();
		$scene = new CScene(CSceneParseQuery::getInstance());
		do
		{
			$scene->execute();
		}
		while($scene->getCurrentState() != $emptyState);
		exit(0);
	}
}

?>
