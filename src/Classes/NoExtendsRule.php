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

namespace Ergebnis\PHPStan\Rules\Classes;

use PhpParser\Node;
use PHPStan\Analyser;
use PHPStan\Rules;
use PHPStan\ShouldNotHappenException;

final class NoExtendsRule implements Rules\Rule
{
    /**
     * @var array<int, class-string>
     */
    private static array $defaultClassesAllowedToBeExtended = [
        'PHPUnit\\Framework\\TestCase',
    ];

    /**
     * @var array<int, class-string>
     */
    private readonly array $classesAllowedToBeExtended;

    /**
     * @param array<int, class-string> $classesAllowedToBeExtended
     */
    public function __construct(array $classesAllowedToBeExtended)
    {
        $this->classesAllowedToBeExtended = \array_unique(\array_merge(
            self::$defaultClassesAllowedToBeExtended,
            \array_map(static function (string $classAllowedToBeExtended): string {
                /** @var class-string $classAllowedToBeExtended */
                return $classAllowedToBeExtended;
            }, $classesAllowedToBeExtended),
        ));
    }

    public function getNodeType(): string
    {
        return Node\Stmt\Class_::class;
    }

    public function processNode(
        Node $node,
        Analyser\Scope $scope,
    ): array {
        if (!$node instanceof Node\Stmt\Class_) {
            throw new ShouldNotHappenException(\sprintf(
                'Expected node to be instance of "%s", but got instance of "%s" instead.',
                Node\Stmt\Class_::class,
                $node::class,
            ));
        }

        if (!$node->extends instanceof Node\Name) {
            return [];
        }

        $extendedClassName = $node->extends->toString();

        if (\in_array($extendedClassName, $this->classesAllowedToBeExtended, true)) {
            return [];
        }

        if (!isset($node->namespacedName)) {
            $ruleErrorBuilder = Rules\RuleErrorBuilder::message(\sprintf(
                'Anonymous class is not allowed to extend "%s".',
                $extendedClassName,
            ));

            return [
                $ruleErrorBuilder->build(),
            ];
        }

        $ruleErrorBuilder = Rules\RuleErrorBuilder::message(\sprintf(
            'Class "%s" is not allowed to extend "%s".',
            $node->namespacedName->toString(),
            $extendedClassName,
        ));

        return [
            $ruleErrorBuilder->build(),
        ];
    }
}
