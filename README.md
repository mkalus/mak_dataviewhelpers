# mak_dataviewhelpers

Extension for TYPO3 with my onw view helpers. Right now, there is not much, but it will grow...

## Using the View Helpers

Namespace:

    {namespace dv=AUXNET\MakDataviewhelpers\ViewHelpers}

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