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
class ConstantType extends Type implements GenericTypeInterface
{
    /**
     * @var T
     */
    private TypeInterface $type;

    /**
     * @param T $type
     */
    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
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
     * {@inheritDoc}
     */
    public function of(): TypeInterface
    {
        return $this->type;
    }
}
