<?php
/**
 * Hook for cmd map processing
 *
 * @author  Tim Lochmüller
 */

namespace HDNET\Calendarize\Hooks;

use HDNET\Calendarize\Register;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook for cmd map processing
 *
 * @hook TYPO3_CONF_VARS|SC_OPTIONS|t3lib/class.t3lib_tcemain.php|processCmdmapClass
 */
class ProcessCmdmapClass extends AbstractHook
{

    /**
     * Handle CMD
     *
     * @param string      $command
     * @param string      $table
     * @param int         $uid
     * @param             $value
     * @param DataHandler $handler
     * @param             $pasteUpdate
     * @param             $pasteDatamap
     */
    public function processCmdmap_postProcess($command, $table, $uid, $value, $handler, $pasteUpdate, $pasteDatamap)
    {
        $register = Register::getRegister();
        foreach ($register as $key => $configuration) {
            if ($configuration['tableName'] == $table) {
                $indexer = GeneralUtility::makeInstance('HDNET\\Calendarize\\Service\\IndexerService');
                $indexer->reindex($key, $table, $uid);
            }
        }
    }
}
