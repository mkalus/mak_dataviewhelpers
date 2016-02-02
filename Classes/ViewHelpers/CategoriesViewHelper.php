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

/**
 * Category view helper - return categories from input
 *
 * @package mak_dataviewhelpers
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class CategoriesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Inject repository to enable DI
     *
     * @param \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository $categoryRepository
     * @return void
     */
    public function injectCategoryRepository(\TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param string|array $categories categories either string (comma separated) or list of uids as array (can be null)
     * @param string|integer $pid parent page id containing categories
     * @param string $as render as variable instead of returning array directly
     * @param boolean $firstOnly if true return only first record
     * @param boolean $titleOnly if true return titles only (either array or string if firstOnly set)
     * @return array|string|\TYPO3\CMS\Extbase\Domain\Model\Category array of categories (if as is set) or output or single entry
     */
    public function render($categories = NULL, $pid = NULL, $as = NULL, $firstOnly = FALSE, $titleOnly = FALSE) {
        if (!empty($categories)) {
            // explode if string
            if (is_string($categories)) $categories = explode(',', $categories);
        } else $categories = NULL;

        // page id
        if (!empty($pid)) $pid = intval($pid);

        // define contain array
        $contain = array();

        // get categories
        $query = $this->categoryRepository->createQuery();
        if (!empty($categories))
            $contain[] = $query->in('uid', $categories);
        if (!empty($pid))
            $contain[] = $query->equals('pid', $pid);
        if ($titleOnly)
            $query->setLimit(1);

        // any filters?
        if (!empty($contain))
            $query->matching($query->logicalAnd($contain));

        // Ignore storage space
        $query->getQuerySettings()->setRespectStoragePage(false);

        $categoriesFound = $query->execute();

        // nothing found?
        if ($categoriesFound->count() == 0) $categoriesFound = NULL;

        if ($categoriesFound != null) {
            if ($firstOnly) {
                $categoriesFound = $categoriesFound->getFirst();

                // title only?
                /**
                 * @var \TYPO3\CMS\Extbase\Domain\Model\Category $categoriesFound
                 */
                if ($titleOnly) $categoriesFound = $categoriesFound->getTitle();
            } // title only? -> create array of titles
            elseif ($titleOnly) {
                $titles = array();
                /**
                 * @var \TYPO3\CMS\Extbase\Domain\Model\Category $category
                 */
                foreach ($categoriesFound as $category)
                    $titles[] = $category->getTitle();

                $categoriesFound = $titles;
            }
        }

        // return directly
        if (empty($as)) return $categoriesFound;

        $this->templateVariableContainer->add($as, $categoriesFound);
        $output = $this->renderChildren();
        $this->templateVariableContainer->remove($as);
        return $output;
    }
}