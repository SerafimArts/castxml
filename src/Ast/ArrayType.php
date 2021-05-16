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
class ArrayType extends Type implements GenericTypeInterface
{
    /**
     * @var positive-int|0
     */
    private int $min;

    /**
     * @var positive-int|0
     */
    private int $max;

    /**
     * @var T
     */
    private TypeInterface $of;

    /**
     * @param T $of
     * @param positive-int|0 $min
     * @param positive-int|0 $max
     */
    public function __construct(TypeInterface $of, int $min, int $max)
    {
        $this->of = $of;
        $this->min = $min;
        $this->max = $max;
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
     * @param positive-int|0 $min
     * @return $this
     */
    public function withMin(int $min): self
    {
        assert($min >= 0);

        $self = clone $this;
        $self->min = $min;

        return $self;
    }

    /**
     * @param positive-int|0 $max
     * @return $this
     */
    public function withMax(int $max): self
    {
        assert($max >= 0);

        $self = clone $this;
        $self->max = $max;

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
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * {@inheritDoc}
     */
    public function of(): TypeInterface
    {
        return $this->of;
    }
}
