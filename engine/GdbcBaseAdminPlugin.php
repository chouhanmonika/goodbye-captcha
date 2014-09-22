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

abstract class GdbcBaseAdminPlugin extends MchWpAdminPlugin
{
	protected function __construct(array $arrPluginInfo)
	{
		parent::__construct($arrPluginInfo);
	}
	
	/**
	 *
	 * @return \GdbcModulesController 
	 */	
	public function getModulesControllerInstance(array $arrPluginInfo)
	{
		return GdbcModulesController::getInstance($arrPluginInfo);
	}

	public function getPluginAdminActionLinks(array $pluginActionLinks)
	{
		return array_merge(
			array('settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->PLUGIN_SLUG ) . '">' . __( 'Settings', $this->PLUGIN_SLUG ) . '</a>'),
			$pluginActionLinks
		);
	}
	
	
	public function enqueueAdminScriptsAndStyles()
	{
		if(null === $this->AdminSettingsPageHook)
			return;
		
		if( self::WP_VERSION_ID >= 30100 && ($this->AdminSettingsPageHook !== get_current_screen()->id) )
			return;

		
		//wp_enqueue_script( $this->PLUGIN_SLUG . '-admin-script', plugins_url( 'admin/assets/js/admin.js', $this->PLUGIN_MAIN_FILE ), array( 'jquery' ), $this->PLUGIN_VERSION );

		//wp_enqueue_style( $this->PLUGIN_SLUG  .'-admin-styles', plugins_url( 'admin/assets/css/admin.css', $this->PLUGIN_MAIN_FILE ), array(), $this->PLUGIN_VERSION );

	}
	
}