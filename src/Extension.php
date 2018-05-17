<?php

namespace AdvancedMeta;

class Extension {
	public static function onRegistration() {
		if( $GLOBALS['wgVersion'] >= '1.28' ) {
			return;
		}
		$GLOBALS['wgServiceWiringFiles'][] = $GLOBALS['wgExtensionDirectory']
			."/AdvancedMeta/includes/ServiceWiring.php";
	}
}
