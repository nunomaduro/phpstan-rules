<?php

declare(strict_types=1);

namespace Ergebnis\PHPStan\Rules\Test\Fixture\Classes\NoExtendsRuleWithClassesAllowedToBeExtended\Success;

use PHPUnit\Framework;

/**
 * @internal
 *
 * @coversNothing
 */
final class ClassExtendingPhpUnitFrameworkTestCase extends Framework\TestCase
{
}
