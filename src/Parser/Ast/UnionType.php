<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Parser\Ast;

class UnionType extends Type implements OptionalNamedTypeInterface, LazyInitializedTypeInterface
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
     * @var array<Field|StructType|UnionType>
     */
    private iterable $variants;

    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @param string|null $name
     * @param positive-int|0 $size
     * @param positive-int|0 $align
     * @param iterable<Field|StructType|UnionType> $variants
     */
    public function __construct(?string $name, int $size = 8, int $align = 8, iterable $variants = [])
    {
        $this->size = $size;
        $this->align = $align;
        $this->variants = $variants;
        $this->name = $name;
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
     * @param iterable<Field|StructType|UnionType> $variants
     * @return $this
     */
    public function withVariants(iterable $variants): self
    {
        $self = clone $this;
        $self->variants = $variants;

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
     * @return string|null
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
     * @return array<Field|StructType|UnionType>
     */
    public function getVariants(): array
    {
        $this->resolve();

        return $this->variants;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(): void
    {
        if ($this->variants instanceof \Traversable) {
            // Avoid recursive resolving
            [$variants, $this->variants] = [$this->variants, []];

            foreach ($variants as $field) {
                switch (true) {
                    case $field instanceof Field:
                        $this->variants[] = $field;
                        break;

                    case $field instanceof OptionalNamedTypeInterface:
                        if ($field->getName()) {
                            $this->variants[] = new Field($field, $field->getName());
                        }
                        break;

                    default:
                        $this->variants[] = $field;
                }
            }
        }
    }
}
