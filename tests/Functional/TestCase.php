<?php

declare(strict_types=1);

namespace PewPew\Hydrator\JMS\Tests\Functional;

use PewPew\Hydrator\JMS\Tests\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('functional'), Group('pew-pew/hydrator-jms')]
abstract class TestCase extends BaseTestCase {}
