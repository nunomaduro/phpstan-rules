<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/phpstan-rules
 */

namespace Localheinz\PHPStan\Rules\Statements;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

final class NoSwitchRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\Switch_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        return [
            'Control structures using switch should not be used.',
        ];
    }
}
