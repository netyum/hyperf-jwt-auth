<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/jwt
 *
 * @link     https://github.com/hyperf-ext/jwt
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/jwt/blob/master/LICENSE
 */
namespace Hyperf\Jwt\Contracts;

use Hyperf\Jwt\Claims\Collection;

interface PayloadValidatorInterface
{
    /**
     * Perform some checks on the value.
     * @throws \Hyperf\Jwt\Exceptions\TokenInvalidException
     * @throws \Hyperf\Jwt\Exceptions\TokenExpiredException
     */
    public function check(Collection $value, bool $ignoreExpired = false): Collection;

    /**
     * Helper function to return a boolean.
     */
    public function isValid(Collection $value, bool $ignoreExpired = false): bool;
}
