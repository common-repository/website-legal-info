<?php
	/**
	 * See legalMain::legalInfoDisplay
	 */
	function wp_legal_info() {
		// Instantiate our class
		$legalOLPEP = legalMain::getInstance();
		return $legalOLPEP->legalInfoDisplay();
	}

?>