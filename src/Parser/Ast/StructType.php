<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Parser\Ast;

class StructType extends Type implements NamedTypeInterface, LazyInitializedTypeInterface
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
     * @var array<Field|StructType|UnionType>
     */
    private iterable $fields;

    /**
     * @param string $name
     * @param positive-int|0 $size
     * @param positive-int|0 $align
     * @param iterable<Field|StructType|UnionType> $fields
     */
    public function __construct(string $name, int $size = 8, int $align = 8, iterable $fields = [])
    {
        $this->name = $name;
        $this->size = $size;
        $this->align = $align;
        $this->fields = $fields;
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
     * @param iterable<Field|StructType|UnionType> $fields
     * @return $this
     */
    public function withFields(iterable $fields): self
    {
        $self = clone $this;
        $self->fields = $fields;

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

    /**
     * @return array<Field|StructType|UnionType>
     */
    public function getFields(): array
    {
        $this->resolve();

        return $this->fields;
    }

    /**
     * @return void
     */
    public function resolve(): void
    {
        if ($this->fields instanceof \Traversable) {
            // Avoid recursive resolving
            [$fields, $this->fields] = [$this->fields, []];

            foreach ($fields as $field) {
                switch (true) {
                    case $field instanceof Field:
                        $this->fields[] = $field;
                        break;

                    case $field instanceof OptionalNamedTypeInterface:
                        if ($field->getName()) {
                            $this->fields[] = new Field($field, $field->getName());
                        }
                        break;

                    default:
                        $this->fields[] = $field;
                }
            }
        }
    }
}
