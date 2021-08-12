<?php

/*
 * This file is part of the EOffice project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Functional\EOffice\User;

use EOffice\Testing\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use EOffice\Testing\Concerns\InteractsWithORM;
use EOffice\User\Testing\InteractsWithUser;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

/**
 * @covers \EOffice\User\Model\Profile
 * @covers \EOffice\User\ProfileModule
 */
class ProfileApiTest extends ApiTestCase
{
    use InteractsWithORM;
    use InteractsWithUser;
    use RefreshDatabaseTrait;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

    }

    public function test_create_profile()
    {
        $client = $this->createClientWithCredentials();
        $response = $client->request('POST', '/api/profiles', ['json' => [
            'nama' => 'Bagong Handoko',
            'userId' => '01',
            'jabatanId' => 'Operator',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['nama' => 'Bagong Handoko']);

        $json = $response->toArray();
        $this->assertArrayHasKey('nama', $json);
        $this->assertArrayHasKey('userId', $json);
        $this->assertSame('foo', $response->toArray());
    }
}