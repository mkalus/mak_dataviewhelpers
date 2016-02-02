<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Register cache 'mak_dataviewhelpers'
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mak_dataviewhelpers'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mak_dataviewhelpers'] = array();

	// turn on REDIS backend, if ok
	//if (class_exists('\\Redis'))
	//	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mak_dataviewhelpers']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\RedisBackend';
}