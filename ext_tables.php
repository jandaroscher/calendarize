<?php
/**
 * General ext_tables file and also an example for your own extension
 *
 * @category   Extension
 * @package    Calendarize
 * @author     Tim Lochmüller
 */

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\HDNET\Autoloader\Loader::extTables('HDNET', 'calendarize');

\HDNET\Calendarize\Register::extTables(\HDNET\Calendarize\Register::getDefaultCalendarizeConfiguration());

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('calendarize', 'Calendar', 'Calendar');