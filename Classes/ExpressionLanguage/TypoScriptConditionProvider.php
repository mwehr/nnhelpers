<?php

namespace Nng\Nnhelpers\ExpressionLanguage;

use Nng\Nnhelpers\ExpressionLanguage\FunctionsProvider\Typo3ConditionFunctionsProvider;
use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;

/**
 * Class TypoScriptConditionProvider
 *
 * @internal
 */
class TypoScriptConditionProvider extends AbstractProvider
{
    /**
     * `expressionLanguageVariables` sind Variablen, die direkt im TypoScript 
     * verfügbar und können für Conditions verwendet werden, z.B.
     * 
     * ```
     * [nnt3 == true]
     *  ...
     * [GLOBAL]
     * ```
     * 
     * `expressionLanguageProviders` sind Methoden, die im TypoScript innerhalb
     * einer Condition verwendet werden können und Parameter empfangen können.
     * 
     * ```
     * [nnt3_pidInRootline([12,15])]
     * ...
     * [GLOBAL]
     * ```
     */
    public function __construct()
    {
        $this->expressionLanguageVariables = [
            'nnt3' => true
        ];
        $this->expressionLanguageProviders = [
            Typo3ConditionFunctionsProvider::class
        ];
    }
}
