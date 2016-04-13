<?php

/**
 * KE Search Indexer
 */

namespace HDNET\Calendarize\Hooks;

use HDNET\Autoloader\Utility\IconUtility;
use HDNET\Calendarize\Domain\Model\Index;
use HDNET\Calendarize\Domain\Repository\IndexRepository;
use HDNET\Calendarize\Features\KeSearchIndexInterface;
use HDNET\Calendarize\Utility\HelperUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * KE Search Indexer
 *
 * @hook TYPO3_CONF_VARS|EXTCONF|ke_search|registerIndexerConfiguration
 * @hook TYPO3_CONF_VARS|EXTCONF|ke_search|customIndexer
 */
class KeSearchIndexer extends AbstractHook
{

    /**
     * Register the indexer configuration
     *
     * @param array  $params
     * @param object $pObj
     */
    function registerIndexerConfiguration(&$params, $pObj)
    {
        $newArray = [
            'Calendarize Indexer',
            'calendarize',
            IconUtility::getByExtensionKey('calendarize')
        ];
        $params['items'][] = $newArray;
    }

    /**
     * Calendarize indexer for ke_search
     *
     * @param array                $indexerConfig Configuration from TYPO3 Backend
     * @param \tx_kesearch_indexer $indexerObject Reference to indexer class.
     *
     * @return string|null
     */
    public function customIndexer(&$indexerConfig, &$indexerObject)
    {
        if ($indexerConfig['type'] !== 'calendarize') {
            return null;
        }

        /** @var IndexRepository $indexRepository */
        $indexRepository = HelperUtility::create(IndexRepository::class);
        $indexRepository->setOverridePageIds(GeneralUtility::intExplode(',', $indexerConfig['sysfolder']));
        $indexObjects = $indexRepository->findList()
            ->toArray();

        foreach ($indexObjects as $index) {
            /** @var $index Index */
            /** @var KeSearchIndexInterface $originalObject */
            $originalObject = $index->getOriginalObject();
            if (!($originalObject instanceof KeSearchIndexInterface)) {
                continue;
            }

            $title = strip_tags($originalObject->getKeSearchTitle($index));
            $abstract = strip_tags($originalObject->getKeSearchAbstract($index));
            $content = strip_tags($originalObject->getKeSearchContent($index));
            $fullContent = $title . "\n" . $abstract . "\n" . $content;
            $tags = [];
            foreach ($originalObject->getCategories() as $category) {
                $tags[] = "#syscat{$category->getUid()}#";
            }
            $tags = implode(',', $tags);

            // @todo Add year and month information
            $additionalFields = [
                'sortdate' => $index->getStartDateComplete()->getTimestamp(),
                'orig_uid' => $index->getUid(),
                'orig_pid' => $index->getPid(),
            ];

            $indexerObject->storeInIndex(
                $indexerConfig['storagepid'],
                $title,
                'calendarize',
                $indexerConfig['targetpid'],
                $fullContent,
                $tags,
                "&tx_calendarize_calendar[index]={$index->getUid()}",
                $abstract,
                $index->_getProperty('_languageUid'),
                0,  // starttime (TYPO3)
                0,  // endtime (TYPO3)
                '', // fe_group
                false,  // debugOnly
                $additionalFields
            );
        }

        return '<p><b>Custom Indexer "' . $indexerConfig['title'] . '": ' . sizeof($indexObjects) . ' elements have been indexed.</b></p>';
    }
}
