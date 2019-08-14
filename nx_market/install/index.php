<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile($PathInstall."/install.php");
IncludeModuleLangFile(__FILE__);


if(class_exists("nx_market")) return;

Class nx_market extends CModule
{
	var $MODULE_ID = "nx_market";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $errors;

	function nx_market()
	{
		
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = $VOTE_VERSION;
			$this->MODULE_VERSION_DATE = $VOTE_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("NX_MARKET_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("NX_MARKET_MODULE_DESCRIPTION");
		$this->MODULE_CSS = "/bitrix/modules/nx_market/nx_market.css";
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		// Database tables creation
		if(!$DB->Query("SELECT 'x' FROM nx_market WHERE 1=0", true))
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/db/".strtolower($DB->type)."/install.sql");

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		else
		{
			RegisterModule("nx_market");
			CModule::IncludeModule("nx_market");

			COption::SetOptionString("nx_market", "NX_MARKET_DIR", "");
			COption::SetOptionString("nx_market", "NX_MARKET_COMPATIBLE_OLD_TEMPLATE", "N");

			
			RegisterModuleDependences("main", "OnBeforeProlog", "main", "", "", 100, "/modules/nx_market/before.php");
      

			return true;
		}
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		if(!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y")
		{
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/db/".strtolower($DB->type)."/uninstall.sql");
		}

		//delete agents
		CAgent::RemoveModuleAgents("nx_market");

		$db_res = $DB->Query("SELECT ID FROM b_file WHERE MODULE_ID = 'nx_market'");
		while($arRes = $db_res->Fetch())
			CFile::Delete($arRes["ID"]);

		// Events
		include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/events/del_events.php");

		UnRegisterModuleDependences("main", "OnBeforeProlog", "main", "", "", "/modules/nx_banner/before.php");
		UnRegisterModule("nx_market");

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}

		return true;
	}

	function InstallEvents()
	{
		
		RegisterModuleDependences('main', 'OnUserTypeBuildList', 'nx_market', "NXMarket\CNXUserTypeOrder", 'GetUserTypeDescription');

		/*global $DB;
		$sIn = "'NX_MARKET_NEW'";
		$rs = $DB->Query("SELECT count(*) C FROM b_event_type WHERE EVENT_NAME IN (".$sIn.") ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$ar = $rs->Fetch();
		if($ar["C"] <= 0)
		{
			include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/events/set_events.php");
		}*/
		return true;
	}

	function UnInstallEvents()
	{
		
		UnRegisterModuleDependences('main', 'OnUserTypeBuildList', 'NXMarket', "NXMarket\CNXUserTypeOrder", 'GetUserTypeDescription');

		//global $DB;
		//$sIn = "'VOTE_NEW'";
		//$DB->Query("DELETE FROM b_event_message WHERE EVENT_NAME IN (".$sIn.") ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		//$DB->Query("DELETE FROM b_event_type WHERE EVENT_NAME IN (".$sIn.") ", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return true;
	}

	function InstallFiles($arParams = array())
	{
		global $DB;

		if($_ENV["COMPUTERNAME"]!='BX')
		{
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/public/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/");
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/nx_market", true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/nx_market", true, true);
        }

		$bReWriteAdditionalFiles = (($GLOBALS["public_rewrite"] == "Y") ? True : False);

		if($GLOBALS["install_public"] == "Y" && !empty($GLOBALS["public_dir"]))
		{
			

			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/update", $_SERVER["DOCUMENT_ROOT"]."/update", $bReWriteAdditionalFiles, true);

			$sites = CLang::GetList($by, $order, Array("ACTIVE"=>"Y"));
			while($site = $sites->Fetch())
			{
				if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/public/".$site["LANGUAGE_ID"]))
					CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/public/".$site["LANGUAGE_ID"], $site['ABS_DOC_ROOT'].$site["DIR"].$GLOBALS["public_dir"], $bReWriteAdditionalFiles, true);
			}
		}

		return true;
	}

	function UnInstallFiles()
	{
		if($_ENV["COMPUTERNAME"]!='BX')
		{
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
            DeleteDirFilesEx("/bitrix/themes/.default/icons/nx_market/");//icons
            DeleteDirFilesEx("/bitrix/images/nx_market/");//images
            DeleteDirFilesEx("/bitrix/js/nx_market/");//images
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/public/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/");
        }
		return true;
	}

	function DoInstall()
	{
		global $DB, $APPLICATION, $step;
		$VOTE_RIGHT = $APPLICATION->GetGroupRight("nx_market");
		if ($VOTE_RIGHT=="W")
		{
			$step = IntVal($step);
			if($step<2)
			{
				$GLOBALS["install_step"] = 1;
				$APPLICATION->IncludeAdminFile(GetMessage("NX_MARKET_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/step.php");
			}
			elseif($step==2)
			{
				if($this->InstallDB())
				{
					$this->InstallEvents();
					$this->InstallFiles();
				}
				$GLOBALS["errors"] = $this->errors;
				$GLOBALS["install_step"] = 2;
				$APPLICATION->IncludeAdminFile(GetMessage("NX_MARKET_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/step.php");
			}
		}
	}

	function DoUninstall()
	{
		global $DB, $APPLICATION, $step;
		$VOTE_RIGHT = $APPLICATION->GetGroupRight("nx_market");
		if ($VOTE_RIGHT=="W")
		{
			if($step < 2)
			{
				$GLOBALS["uninstall_step"] = 1;
				$APPLICATION->IncludeAdminFile(GetMessage("NX_MARKET_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/unstep.php");
			}
			elseif($step == 2)
			{
				$this->UnInstallDB(array(
					"savedata" => $_REQUEST["savedata"],
				));
				//message types and templates
				if($_REQUEST["save_templates"] != "Y")
				{
					$this->UnInstallEvents();
				}
				$this->UnInstallFiles();
				$GLOBALS["errors"] = $this->errors;
				$GLOBALS["uninstall_step"] = 2;
				$APPLICATION->IncludeAdminFile(GetMessage("NX_MARKET_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/nx_market/install/unstep.php");
			}
		}
	}
}
?>