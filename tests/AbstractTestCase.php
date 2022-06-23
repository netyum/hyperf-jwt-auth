<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/jwt
 *
 * @link     https://github.com/hyperf-ext/jwt
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/jwt/blob/master/LICENSE
 */
namespace HyperfTest;

use Carbon\Carbon;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Jwt\Claims\Factory;
use Hyperf\Jwt\Contracts\ManagerInterface;
use Hyperf\Jwt\ManagerFactory;
use Mockery;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @var int
     */
    protected $testNowTimestamp;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Hyperf\Jwt\Contracts\ManagerInterface|\Hyperf\Jwt\Manager|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    protected $manager;

    /**
     * @var \Hyperf\Jwt\Claims\Factory
     */
    protected $claimFactory;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow($now = Carbon::now());
        $this->testNowTimestamp = $now->getTimestamp();
        $this->container = ApplicationContext::getContainer();
        $this->container->set(ManagerInterface::class, $this->manager = Mockery::mock(ManagerFactory::class));
        $this->manager->shouldReceive('getClaimFactory')->andReturn($this->claimFactory = new Factory(3600, 3600 * 24 * 14));
    }

    public function tearDown(): void
    {
        Carbon::setTestNow();
        Mockery::close();

        parent::tearDown();
    }
}
