<?php

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function () {
    ExtensionManagementUtility::addStaticFile(
        'powermail_limits',
        'Configuration/TypoScript',
        'Powermail Limits'
    );
})();
