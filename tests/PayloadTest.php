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

use Hyperf\Jwt\Claims\Audience;
use Hyperf\Jwt\Claims\Collection;
use Hyperf\Jwt\Claims\Expiration;
use Hyperf\Jwt\Claims\IssuedAt;
use Hyperf\Jwt\Claims\Issuer;
use Hyperf\Jwt\Claims\JwtId;
use Hyperf\Jwt\Claims\NotBefore;
use Hyperf\Jwt\Claims\Subject;
use Hyperf\Jwt\Contracts\ClaimInterface;
use Hyperf\Jwt\Exceptions\PayloadException;
use Hyperf\Jwt\Payload;

/**
 * @internal
 * @coversNothing
 */
class PayloadTest extends AbstractTestCase
{
    /**
     * @var \Hyperf\Jwt\Payload
     */
    protected $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->payload = $this->getTestPayload();
    }

    /** @test */
    public function itShouldThrowAnExceptionWhenTryingToAddToThePayload()
    {
        $this->expectException(PayloadException::class);
        $this->expectExceptionMessage('The payload is immutable');
        $this->payload['foo'] = 'bar';
    }

    /** @test */
    public function itShouldThrowAnExceptionWhenTryingToRemoveAKeyFromThePayload()
    {
        $this->expectExceptionMessage('The payload is immutable');
        $this->expectException(PayloadException::class);
        unset($this->payload['foo']);
    }

    /** @test */
    public function itShouldCastThePayloadToAStringAsJson()
    {
        $this->assertSame((string) $this->payload, json_encode($this->payload->get(), JSON_UNESCAPED_SLASHES));
        $this->assertJsonStringEqualsJsonString((string) $this->payload, json_encode($this->payload->get()));
    }

    /** @test */
    public function itShouldAllowArrayAccessOnThePayload()
    {
        $this->assertTrue(isset($this->payload['iat']));
        $this->assertSame($this->payload['sub'], 1);
        $this->assertArrayHasKey('exp', $this->payload);
    }

    /** @test */
    public function itShouldGetPropertiesOfPayloadViaGetMethod()
    {
        $this->assertIsArray($this->payload->get());
        $this->assertSame($this->payload->get('sub'), 1);

        $this->assertSame(
            $this->payload->get(function () {
                return 'jti';
            }),
            'foo'
        );
    }

    /** @test */
    public function itShouldGetMultiplePropertiesWhenPassingAnArrayToTheGetMethod()
    {
        $values = $this->payload->get(['sub', 'jti']);

        $sub = $values[0];
        $jti = $values[1];

        $this->assertIsArray($values);
        $this->assertSame($sub, 1);
        $this->assertSame($jti, 'foo');
    }

    /** @test */
    public function itShouldDetermineWhetherThePayloadHasAClaim()
    {
        $this->assertTrue($this->payload->has(new Subject(1)));
        $this->assertFalse($this->payload->has(new Audience(1)));
    }

    /** @test */
    public function itShouldMagicallyGetAProperty()
    {
        $sub = $this->payload->getSubject();
        $jti = $this->payload->getJwtId();
        $iss = $this->payload->getIssuer();

        $this->assertSame($sub, 1);
        $this->assertSame($jti, 'foo');
        $this->assertSame($iss, 'http://example.com');
    }

    /** @test */
    public function itShouldInvokeTheInstanceAsACallable()
    {
        $payload = $this->payload;

        $sub = $payload('sub');
        $jti = $payload('jti');
        $iss = $payload('iss');

        $this->assertSame($sub, 1);
        $this->assertSame($jti, 'foo');
        $this->assertSame($iss, 'http://example.com');

        $this->assertSame($payload(), $this->payload->toArray());
    }

    /** @test */
    public function itShouldThrowAnExceptionWhenMagicallyGettingAPropertyThatDoesNotExist()
    {
        $this->expectExceptionMessage('The claim [getFoo] does not exist on the payload.');
        $this->expectException(\BadMethodCallException::class);
        $this->payload->getFoo();
    }

    /** @test */
    public function itShouldGetTheClaims()
    {
        $claims = $this->payload->getClaims();

        $this->assertInstanceOf(Expiration::class, $claims['exp']);
        $this->assertInstanceOf(JwtId::class, $claims['jti']);
        $this->assertInstanceOf(Subject::class, $claims['sub']);

        $this->assertContainsOnlyInstancesOf(ClaimInterface::class, $claims);
    }

    /** @test */
    public function itShouldGetTheObjectAsJson()
    {
        $this->assertJsonStringEqualsJsonString(json_encode($this->payload), $this->payload->toJson());
    }

    /** @test */
    public function itShouldCountTheClaims()
    {
        $this->assertSame(6, $this->payload->count());
        $this->assertCount(6, $this->payload);
    }

    /** @test */
    public function itShouldMatchValues()
    {
        $values = $this->payload->toArray();
        $values['sub'] = (string) $values['sub'];

        $this->assertTrue($this->payload->matches($values));
    }

    /** @test */
    public function itShouldMatchStrictValues()
    {
        $values = $this->payload->toArray();

        $this->assertTrue($this->payload->matchesStrict($values));
        $this->assertTrue($this->payload->matches($values, true));
    }

    /** @test */
    public function itShouldNotMatchEmptyValues()
    {
        $this->assertFalse($this->payload->matches([]));
    }

    /** @test */
    public function itShouldNotMatchValues()
    {
        $values = $this->payload->toArray();
        $values['sub'] = 'dummy_subject';

        $this->assertFalse($this->payload->matches($values));
    }

    /** @test */
    public function itShouldNotMatchStrictValues()
    {
        $values = $this->payload->toArray();
        $values['sub'] = (string) $values['sub'];

        $this->assertFalse($this->payload->matchesStrict($values));
        $this->assertFalse($this->payload->matches($values, true));
    }

    /** @test */
    public function itShouldNotMatchANonExistingClaim()
    {
        $values = ['foo' => 'bar'];

        $this->assertFalse($this->payload->matches($values));
    }

    /**
     * @return \Hyperf\Jwt\Payload
     */
    private function getTestPayload(array $extraClaims = [])
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foo'),
        ];

        if ($extraClaims) {
            $claims = array_merge($claims, $extraClaims);
        }

        $collection = Collection::make($claims);

        return new Payload($collection);
    }
}
