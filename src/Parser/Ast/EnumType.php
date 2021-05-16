<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Parser\Ast;

class EnumType extends Type implements OptionalNamedTypeInterface
{
    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @var positive-int|0
     */
    private int $size;

    /**
     * @var positive-int|0
     */
    private int $align;

    /**
     * @var array<string, positive-int|0>
     */
    private array $values;

    /**
     * @param string|null $name
     * @param positive-int|0 $size
     * @param positive-int|0 $align
     * @param array<string, positive-int|0> $values
     */
    public function __construct(?string $name, int $size = 8, int $align = 8, array $values = [])
    {
        $this->name = $name;
        $this->size = $size;
        $this->align = $align;
        $this->values = $values;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function withName(?string $name): self
    {
        $self = clone $this;
        $self->name = $name ?: null;

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
     * @param array<TypeInterface> $values
     * @return $this
     */
    public function withValues(array $values): self
    {
        $self = clone $this;
        $self->values = $values;

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

    /**
     * @return array<string, positive-int|0>
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
