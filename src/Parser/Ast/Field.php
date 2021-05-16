<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Parser\Ast;

/**
 * @template T of TypeInterface
 * @template-implements GenericTypeInterface<T>
 */
class Field extends Type implements OptionalNamedTypeInterface, GenericTypeInterface
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
     * @param T $type
     * @param string|null $name
     */
    public function __construct(TypeInterface $type, ?string $name)
    {
        $this->type = $type;
        $this->name = $name;
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
     * @internal This method contains internal mutations and may damage the abstract syntax tree.
     * @param TypeInterface $type
     */
    public function setType(TypeInterface $type): void
    {
        $this->type = $type;
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
