<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/jwt
 *
 * @link     https://github.com/hyperf-ext/jwt
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/jwt/blob/master/LICENSE
 */
namespace Hyperf\Jwt;

use Hyperf\Jwt\Commands\GenJwtKeypairCommand;
use Hyperf\Jwt\Commands\GenJwtSecretCommand;
use Hyperf\Jwt\Contracts\JwtFactoryInterface;
use Hyperf\Jwt\Contracts\ManagerInterface;
use Hyperf\Jwt\Contracts\PayloadValidatorInterface;
use Hyperf\Jwt\Contracts\RequestParser\RequestParserInterface;
use Hyperf\Jwt\Contracts\TokenValidatorInterface;
use Hyperf\Jwt\RequestParser\RequestParserFactory;
use Hyperf\Jwt\Validators\PayloadValidator;
use Hyperf\Jwt\Validators\TokenValidator;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                ManagerInterface::class => ManagerFactory::class,
                TokenValidatorInterface::class => TokenValidator::class,
                PayloadValidatorInterface::class => PayloadValidator::class,
                RequestParserInterface::class => RequestParserFactory::class,
                JwtFactoryInterface::class => JwtFactory::class,
            ],
            'commands' => [
                GenJwtSecretCommand::class,
                GenJwtKeypairCommand::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for hyperf-ext/jwt.',
                    'source' => __DIR__ . '/../publish/jwt.php',
                    'destination' => BASE_PATH . '/config/autoload/jwt.php',
                ],
            ],
        ];
    }
}
