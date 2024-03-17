<?php

declare(strict_types=1);

namespace PewPew\Hydrator\Tests\Functional;

use PewPew\Map\Tests\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('functional'), Group('pew-pew/hydrator')]
abstract class TestCase extends BaseTestCase {}
