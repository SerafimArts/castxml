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
class TypeDefinition extends Type implements NamedTypeInterface, GenericTypeInterface
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var T
     */
    private TypeInterface $of;

    /**
     * @param T $of
     * @param string $name
     */
    public function __construct(TypeInterface $of, string $name)
    {
        $this->of = $of;
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function withName(string $name): self
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }

    /**
     * @param TypeInterface $of
     * @return $this
     */
    public function withType(TypeInterface $of): self
    {
        $self = clone $this;
        $self->of = $of;

        return $self;
    }

    /**
     * @internal This method contains internal mutations and may damage the abstract syntax tree.
     * @param TypeInterface $type
     */
    public function setType(TypeInterface $type): void
    {
        $this->of = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function of(): TypeInterface
    {
        return $this->of;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
