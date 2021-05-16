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
class Pointer extends Type implements GenericTypeInterface
{
    /**
     * @var positive-int|0
     */
    private int $size;

    /**
     * @var positive-int|0
     */
    private int $align;

    /**
     * @var T
     */
    private TypeInterface $of;

    /**
     * @param T $of
     * @param positive-int|0 $size
     * @param positive-int|0 $align
     */
    public function __construct(TypeInterface $of, int $size = 8, int $align = 8)
    {
        $this->of = $of;
        $this->size = $size;
        $this->align = $align;
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
     * @param positive-int|0 $size
     * @return $this
     */
    public function withSize(int $size): self
    {
        assert($size >= 0);

        $self = clone $this;
        $self->size = $size;

        return $self;
    }

    /**
     * @param positive-int|0 $align
     * @return $this
     */
    public function withAlign(int $align): self
    {
        assert($align >= 0);

        $self = clone $this;
        $self->align = $align;

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
     * {@inheritDoc}
     */
    public function of(): TypeInterface
    {
        return $this->of;
    }

    /**
     * @return positive-int|0
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return positive-int|0
     */
    public function getAlign(): int
    {
        return $this->align;
    }
}
