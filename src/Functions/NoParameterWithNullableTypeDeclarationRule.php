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

namespace Ergebnis\PHPStan\Rules\Functions;

use PhpParser\Node;
use PHPStan\Analyser;
use PHPStan\Rules;
use PHPStan\ShouldNotHappenException;

final class NoParameterWithNullableTypeDeclarationRule implements Rules\Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\Function_::class;
    }

    public function processNode(
        Node $node,
        Analyser\Scope $scope,
    ): array {
        if (!$node instanceof Node\Stmt\Function_) {
            throw new ShouldNotHappenException(\sprintf(
                'Expected node to be instance of "%s", but got instance of "%s" instead.',
                Node\Stmt\Function_::class,
                $node::class,
            ));
        }

        if (0 === \count($node->params)) {
            return [];
        }

        $params = \array_filter($node->params, static function (Node\Param $node): bool {
            return self::isNullable($node);
        });

        if (0 === \count($params)) {
            return [];
        }

        $functionName = $node->namespacedName;

        return \array_map(static function (Node\Param $node) use ($functionName): Rules\RuleError {
            /** @var Node\Expr\Variable $variable */
            $variable = $node->var;

            /** @var string $parameterName */
            $parameterName = $variable->name;

            $ruleErrorBuilder = Rules\RuleErrorBuilder::message(\sprintf(
                'Function %s() has parameter $%s with a nullable type declaration.',
                $functionName,
                $parameterName,
            ));

            return $ruleErrorBuilder->build();
        }, $params);
    }

    private static function isNullable(Node\Param $node): bool
    {
        if ($node->type instanceof Node\NullableType) {
            return true;
        }

        if ($node->type instanceof Node\UnionType) {
            foreach ($node->type->types as $type) {
                if (!$type instanceof Node\Identifier) {
                    continue;
                }

                if ('null' !== $type->toString()) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }
}
