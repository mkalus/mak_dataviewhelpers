# mak_dataviewhelpers

Extension for TYPO3 with my own view helpers. Description below.

## Using the View Helpers

Namespace:

    {namespace dv=AUXNET\MakDataviewhelpers\ViewHelpers}

**CachedViewHelper**

Example:

    <dv:cached key="show_stuff_123" lifetime="120">Stuff</dv:cached>

Parameters:

* `key` unique cache key
* `lifetime` cache lifetime in seconds (0 = default as per page)
* `tags` optional cache tags
* `noCache` if value is set in this parameter, do not cache entry

Put content into TYPO3 cache with a certain key and lifetime. Great to speed up
rendering complex elements. Type of cache can be set by (re-)defining following TYPO3
config variable:

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mak_dataviewhelpers']
    // example: turn on Redis cache for my cached view parts
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['mak_dataviewhelpers']['backend']
        = 'TYPO3\\CMS\\Core\\Cache\\Backend\\RedisBackend';


**CategoriesViewHelper**

Example:

    <dv:categories categories="12" firstOnly="true" titleOnly="true" />

Parameters:

* `categories` single uid or array of uids
* `pid` single page uid containg categories
* `firstOnly` true/false, only return first element (instead of array of elements)
* `titleOnly` true/false, return title only (either string or array of strings)
* `as` set variable name to set variable instead of returning elements

Returns either array of elements, single entry, string or output. If nothing is found,
null is returned.


**CompactViewHelper**

Example:

    <dv:compact>blah</dv:compact>

Strips extra whitespace from content to compact output in order to save bandwidth.