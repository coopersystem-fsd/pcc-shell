<?php

use iThemesSecurity\Modules\HIBP\HIBP_Requirement;
use iThemesSecurity\User_Groups\Matcher;

return static function ( \Pimple\Container $c ) {
	$c[ HIBP_Requirement::class ] = static function ( \Pimple\Container $c ) {
		return new HIBP_Requirement( $c[ Matcher::class ], ITSEC_Modules::get_config( 'hibp' ), 'hibp' );
	};

	$c['ITSEC_HIBP'] = static function ( \Pimple\Container $c ) {
		return new ITSEC_HIBP( $c[ HIBP_Requirement::class ] );
	};
};
