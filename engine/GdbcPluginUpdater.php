<?php
/** 
 * Copyright (C) 2014 Mihai Chelaru
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

class GdbcPluginUpdater
{
	/**
	 *
	 * @var \GdbcModulesController
	 */
	//private static $modulesController = null;

	public static function updateToCurrentVersion()
	{

		$settingsModuleInstance  = GoodByeCaptcha::getModulesControllerInstance()->getAdminModuleInstance(GdbcModulesController::MODULE_SETTINGS);

		$savedPluginVersionId   = (int)$settingsModuleInstance->getModuleSetting()->getSettingOption(GdbcSettingsAdminModule::OPTION_PLUGIN_VERSION_ID);
		$currentPluginVersionId = MchWp::getVersionIdFromString(GoodByeCaptcha::PLUGIN_VERSION);

		if($currentPluginVersionId === $savedPluginVersionId)
			return;

		if($savedPluginVersionId < MchWp::getVersionIdFromString('1.1.0'))
		{
			self::updateToVersion_1_1_0();
		}

//		if($savedPluginVersionId < MchWp::getVersionIdFromString('1.1.1'))
//		{
//			self::updateToVersion_1_1_1();
//		}

		#Save the new version of the plugin
		$settingsModuleInstance->setSettingOption(GdbcSettingsAdminModule::OPTION_PLUGIN_VERSION_ID, $currentPluginVersionId);

	}

//	private static function updateToVersion_1_1_1()
//	{
//		GdbcTaskScheduler::scheduleGdbcTasks();
//	}



	private static function updateToVersion_1_1_0()
	{
		$arrDefaultModuleOptions = get_option('gdbcdefaultadminmodule-settings');

		if(!empty($arrDefaultModuleOptions))
		{
			$wordpressModuleInstance = GoodByeCaptcha::getModulesControllerInstance()->getAdminModuleInstance(GdbcModulesController::MODULE_WORDPRESS);

			foreach($arrDefaultModuleOptions as $optionName => $optionValue)
			{
				$wordpressModuleInstance->getModuleSetting()->setSettingOption($optionName, $optionValue);
			}
		}

		delete_option('gdbcdefaultadminmodule-settings');

		$gdbcAttemptEntity = new GdbcAttemptEntity();

		$createTableQry = "CREATE TABLE " . $gdbcAttemptEntity->getTableName() . " (
						  Id bigint unsigned NOT NULL auto_increment,
						  CreatedDate datetime NOT NULL,
						  ModuleId tinyint unsigned NOT NULL,
						  SectionId tinyint unsigned default NULL,
						  ClientIp varbinary(16) default NULL,
						  CountryId smallint unsigned default NULL,
						  ReasonId tinyint unsigned NOT NULL,
						  IsIpBlocked tinyint default NULL,
						  IsDeleted tinyint NOT NULL,
						  PRIMARY KEY  (Id),
						  KEY index_CreatedDate (CreatedDate),
						  KEY index_ClientIp (ClientIp),
						  KEY index_IsDeleted (IsDeleted)
						)";

		MchWpDbManager::createTable($gdbcAttemptEntity->getTableName(), $createTableQry);
    }


	private static function generateRandomPublicIP()
	{
		$firstDecimal = 0;
		$arrNotAllowedDecimals = array(0, 10, 100, 127, 169, 172, 192, 198, 203, 224, 240);
		while(true)
		{
			if(in_array($firstDecimal = rand(1, 255), $arrNotAllowedDecimals))
				continue;

			break;
		}

		return $firstDecimal . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' .rand(0, 255);
	}

	private function __construct()
	{}
} 