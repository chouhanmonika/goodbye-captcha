<?php

/* 
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

final class GdbcRequest
{
	public static function isValid(array $arrParameters = null)
	{
		$isTokenValid = GdbcTokenController::getInstance()->isReceivedTokenValid();

		if(true === $isTokenValid)
			return true;

		if($isTokenValid === GdbcReasonDataSource::CLIENT_IP_BLOCKED)
			return false;

		(null === ($attemptEntity = GdbcAttemptsManager ::getSoftDeletedAttempt())) ? $attemptEntity = new GdbcAttemptEntity() : null;

		$attemptEntity->ClientIp    = MchHttpRequest::getClientIp(array());
		$attemptEntity->CreatedDate = current_time('mysql');
		$attemptEntity->CountryId   = GdbcCountryDataSource::getCountryIdByCode(MchHttpUtil::getCountryCodeByIp($attemptEntity->ClientIp));
		$attemptEntity->IsDeleted   = 0;
		$attemptEntity->IsIpBlocked = 0;
		$attemptEntity->ReasonId    = $isTokenValid;
		$attemptEntity->ModuleId    = isset($arrParameters['module'])  ? GoodByeCaptcha::getModulesControllerInstance()->getModuleIdByName($arrParameters['module']) : null;
		$attemptEntity->SectionId   = isset($arrParameters['section']) && null !==  $attemptEntity->ModuleId ? GoodByeCaptcha::getModulesControllerInstance()->getAdminModuleInstance($arrParameters['module'])->getSettingOptionIdByOptionName($arrParameters['section']) : null;


		empty($attemptEntity->Id) ? GdbcAttemptsManager::createAttempt($attemptEntity) : GdbcAttemptsManager::saveAttempt($attemptEntity);

		return false;
	}
}
