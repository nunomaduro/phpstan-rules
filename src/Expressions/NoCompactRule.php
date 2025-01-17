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
use PHPStan\ShouldNotHappenException;

final class NoCompactRule implements Rules\Rule
{
    public function getNodeType(): string
    {
        return Node\Expr\FuncCall::class;
    }

    public function processNode(
        Node $node,
        Analyser\Scope $scope,
    ): array {
        if (!$node instanceof Node\Expr\FuncCall) {
            throw new ShouldNotHappenException(\sprintf(
                'Expected node to be instance of "%s", but got instance of "%s" instead.',
                Node\Expr\FuncCall::class,
                $node::class,
            ));
        }

        if (!$node->name instanceof Node\Name) {
            return [];
        }

        if ('compact' !== $scope->resolveName($node->name)) {
            return [];
        }

        $ruleErrorBuilder = Rules\RuleErrorBuilder::message('Function compact() should not be used.');

        return [
            $ruleErrorBuilder->build(),
        ];
    }
}
