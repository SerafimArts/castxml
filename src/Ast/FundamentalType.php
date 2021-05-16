<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Ast;

class FundamentalType extends Type implements NamedTypeInterface
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var positive-int|0
     */
    private int $size;

    /**
     * @var positive-int|0
     */
    private int $align;

    /**
     * @param string $name
     * @param positive-int|0 $size
     * @param positive-int|0 $align
     */
    public function __construct(string $name, int $size = 8, int $align = 8)
    {
        $this->name = $name;
        $this->size = $size;
        $this->align = $align;
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
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
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
