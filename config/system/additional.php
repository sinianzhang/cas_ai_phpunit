<?php

class AdditionalConfiguration
{
    /**
     * Include additional configurations by TYPO3_CONTEXT server variable
     *
     * Of example:
     * - TYPO3_CONTEXT: Production/Qa
     * - Possible configuration files:
     *   1. config/AdditionalConfiguration/ProductionContext.php
     *   2. config/AdditionalConfiguration/Production/QaContext.php (higher prio)
     *
     * Default TYPO3_CONTEXT values:
     * - Development
     * - Testing
     * - Production
     */
    public function loadContextDependentConfigurations(): self
    {
        $orderedListOfContextNames = [];
        $currentContext = \TYPO3\CMS\Core\Core\Environment::getContext();
        do {
            $orderedListOfContextNames[] = (string)$currentContext;
        } while (($currentContext = $currentContext->getParent()));
        $orderedListOfContextNames = array_reverse($orderedListOfContextNames);
        foreach ($orderedListOfContextNames as $contextName) {
            $contextConfigFilePath = \TYPO3\CMS\Core\Core\Environment::getConfigPath() . '/AdditionalConfiguration/' . $contextName . 'Context.php';
            if (file_exists($contextConfigFilePath)) {
                require($contextConfigFilePath);
            }
        }
        return $this;
    }

    /**
     * Append TYPO3_CONTEXT to site name in the TYPO3 backend
     */
    public function appendContextToSiteName(): self
    {
        if (
            \TYPO3\CMS\Core\Core\Environment::getContext()->isProduction() === false ||
            (string)\TYPO3\CMS\Core\Core\Environment::getContext() === 'Production/Content'
        ) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] .=
                ' - ' . (string)\TYPO3\CMS\Core\Core\Environment::getContext();
        }
        return $this;
    }
}

$additionalConfiguration = new \AdditionalConfiguration();
$additionalConfiguration
    ->loadContextDependentConfigurations()
    ->appendContextToSiteName();
