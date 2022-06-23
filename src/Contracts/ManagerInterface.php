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

use Hyperf\Jwt\Blacklist;
use Hyperf\Jwt\Payload;
use Hyperf\Jwt\Token;

interface ManagerInterface
{
    /**
     * Encode a Payload and return the Token.
     */
    public function encode(Payload $payload): Token;

    /**
     * Decode a Token and return the Payload.
     *
     * @throws \Hyperf\Jwt\Exceptions\TokenBlacklistedException
     */
    public function decode(Token $token, bool $checkBlacklist = true): Payload;

    /**
     * Refresh a Token and return a new Token.
     *
     * @throws \Hyperf\Jwt\Exceptions\TokenBlacklistedException
     * @throws \Hyperf\Jwt\Exceptions\JwtException
     */
    public function refresh(Token $token, bool $forceForever = false): Token;

    /**
     * Invalidate a Token by adding it to the blacklist.
     *
     * @throws \Hyperf\Jwt\Exceptions\JwtException
     */
    public function invalidate(Token $token, bool $forceForever = false): bool;
}
