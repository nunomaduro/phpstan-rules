<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/phpstan-rules
 */

namespace Ergebnis\PHPStan\Rules\Expressions;

use PhpParser\Node;
use PHPStan\Analyser;
use PHPStan\Rules;

final class NoIssetRule implements Rules\Rule
{
    public function getNodeType(): string
    {
        return Node\Expr\Isset_::class;
    }

    public function processNode(
        Node $node,
        Analyser\Scope $scope,
    ): array {
        $ruleErrorBuilder = Rules\RuleErrorBuilder::message('Language construct isset() should not be used.');

        return [
            $ruleErrorBuilder->build(),
        ];
    }
}
