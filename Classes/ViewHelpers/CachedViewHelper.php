<?php
namespace AUXNET\MakDataviewhelpers\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Dr. Maximilian Kalus <info@auxnet.de>, AUXNET
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * View helper for creating cached views
 *
 * Usage:
 * <dv:cached key="show_stuff_123" lifetime="120">Stuff</dv:cached>
 *
 * @package mak_dataviewhelpers
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class CachedViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	 * @var \TYPO3\CMS\Core\Cache\Frontend\AbstractFrontend
	 */
	protected static $cacheInstance = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->initializeCache();
	}

	/**
	 * Initialize cache instance to be ready to use
	 *
	 * @return void
	 */
	protected function initializeCache() {
		if (static::$cacheInstance == null) {
			\TYPO3\CMS\Core\Cache\Cache::initializeCachingFramework();
			try {
				static::$cacheInstance = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('mak_dataviewhelpers');
			} catch (\TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException $e) {
				static::$cacheInstance = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheFactory')->create(
					'mak_fs',
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mak_dataviewhelpers']['frontend'],
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mak_dataviewhelpers']['backend'],
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mak_dataviewhelpers']['options']
				);
			}
		}
	}

	/**
	 * Renders the content with a certain lifetime
	 *
	 * @param string $key cache key identifier (must be unique per cache entry)
	 * @param int $lifetime lifetime of the view part in seconds (0 = default as per page)
	 * @param mixed $tags optional tags
	 * @param mixed $noCache do not cache if this is non empty
	 * @return string
	 * @author Maximilian Kalus <info@auxnet.de>
	 */
	public function render($key, $lifetime = 0, $tags = null, $noCache = null) {
		// do not cache if $excludeIfSet not empty
		if (!empty($noCache)) return $this->renderChildren();

		// get cached entry
		$output = static::$cacheInstance->get($key);

		// lifetime of page
		if ($lifetime === 0)
			$lifetime = intval($GLOBALS['TSFE']->get_cache_timeout());

		// cache miss
		if (!$output || is_null($output)) {
			$output = $this->renderChildren();
			if (!empty($tags)) {
				if (is_string($tags)) $tags = array($tags);
			} else $tags = array();

			static::$cacheInstance->set($key, $output, $tags, $lifetime);
		}

		return $output;
	}
}