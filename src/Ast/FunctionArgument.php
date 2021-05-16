<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Ast;

/**
 * @template T of TypeInterface
 * @template-implements GenericTypeInterface<T>
 */
class FunctionArgument extends Type implements OptionalNamedTypeInterface, GenericTypeInterface
{
    /**
     * @var T
     */
    private TypeInterface $type;

    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @var bool
     */
    private bool $variadic = false;

    /**
     * @param T $type
     * @param string|null $name
     */
    public function __construct(TypeInterface $type, ?string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * @internal This method contains internal mutations and may damage the abstract syntax tree.
     * @param TypeInterface $type
     */
    public function setType(TypeInterface $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isVariadic(): bool
    {
        return $this->variadic;
    }

    /**
     * @param bool $variadic
     * @return $this
     */
    public function withVariadic(bool $variadic): self
    {
        $self = clone $this;
        $self->variadic = $variadic;

        return $self;
    }

    /**
     * @param TypeInterface $type
     * @return $this
     */
    public function withType(TypeInterface $type): self
    {
        $self = clone $this;
        $self->type = $type;

        return $self;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function withName(?string $name): self
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }


    /**
     * {@inheritDoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function of(): TypeInterface
    {
        return $this->type;
    }
}
