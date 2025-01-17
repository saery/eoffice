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

namespace EOffice\User\Model;

use EOffice\Contracts\User\Model\UserInterface;

/**
 * @psalm-suppress MixedReturnTypeCoercion
 * @psalm-suppress PropertyNotSetInConstructor
 */
class User implements UserInterface
{
    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected string $id;
    protected string $username;
    protected string $email;
    protected string $password;
    protected ?string $salt = null;
    protected array $roles;
    protected bool $enabled          = true;
    protected ?string $plainPassword = null;

    /**
     * @param string      $username
     * @param string      $email
     * @param string|null $plainPassword
     * @param array       $roles
     * @param bool        $enabled
     */
    public function __construct(
        string $username,
        string $email,
        ?string $plainPassword = null,
        array $roles = [],
        bool $enabled = true
    ) {
        $this->username      = $username;
        $this->email         = $email;
        $this->plainPassword = $plainPassword;
        $this->roles         = $roles;
        $this->enabled       = $enabled;
    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        $roles   = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function __call(string $name, array $arguments): string
    {
        // TODO: Implement @method string getUserIdentifier()
        return $this->id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
