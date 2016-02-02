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

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Load entities from any repository into view
 *
 * @package mak_dataviewhelpers
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class LoadEntitiesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @param string $repository full name of repository, e.g. AUXNET\MakDataviewhelpers\Domain\Repository\XYZRepository
     * @param int $uid optional uid of record to find
     * @param int $pid page id to search in (otherwise global)
     * @param string $orderByField order by this field
     * @param bool $asc ascending or descending
     * @param int $limit limit query to maximum hits
     * @return array|null|object
     */
    public function render($repository, $uid = NULL, $pid = NULL, $orderByField = NULL, $asc = TRUE, $limit = NULL) {
        try {
            /**
             * @var \TYPO3\CMS\Extbase\Persistence\Repository $rep
             */
            $rep = $this->objectManager->get($repository);
        } catch (\Error $e) {
            return 'ERROR: could not create instance of repository '.$repository;
        }

        // create query for this repository
        $query = $rep->createQuery();
        $constraints = array();

        // add constraints
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
        if ($pid !== NULL)
            $constraints[] = $query->equals('pid', $pid);

        if (!empty($uid))
            $constraints[] = $query->equals('uid', $uid);

        // ordering?
        if (!empty($orderByField))
            $query->setOrderings(array(
                $orderByField => $asc?QueryInterface::ORDER_ASCENDING:QueryInterface::ORDER_DESCENDING
            ));

        // limit?
        if (!empty($limit))
            $query->setLimit($limit);

        // execute query
        $results = $query->execute();

        // no results -> return null
        if ($results->count() == 0) return NULL;

        // in case of uid set, return only one result
        if (!empty($uid))
            return $results->getFirst();

        return $results;
    }
}