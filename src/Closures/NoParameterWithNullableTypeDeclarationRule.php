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

namespace Ergebnis\PHPStan\Rules\Closures;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;

final class NoParameterWithNullableTypeDeclarationRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Expr\Closure::class;
    }

    public function processNode(
        Node $node,
        Scope $scope,
    ): array {
        if (!$node instanceof Node\Expr\Closure) {
            throw new ShouldNotHappenException(\sprintf(
                'Expected node to be instance of "%s", but got instance of "%s" instead.',
                Node\Expr\Closure::class,
                $node::class,
            ));
        }

        if (0 === \count($node->params)) {
            return [];
        }

        $params = \array_filter($node->params, static function (Node\Param $node): bool {
            return $node->type instanceof Node\NullableType;
        });

        if (0 === \count($params)) {
            return [];
        }

        return \array_map(static function (Node\Param $node): string {
            /** @var Node\Expr\Variable $variable */
            $variable = $node->var;

            /** @var string $parameterName */
            $parameterName = $variable->name;

            return \sprintf(
                'Closure has parameter $%s with a nullable type declaration.',
                $parameterName,
            );
        }, $params);
    }
}
