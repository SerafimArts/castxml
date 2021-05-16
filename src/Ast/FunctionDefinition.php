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
 */
class FunctionDefinition extends Type implements NamedTypeInterface
{
    /**
     * @var T
     */
    private TypeInterface $returns;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var bool
     */
    private bool $inline = false;

    /**
     * @var bool
     */
    private bool $static = false;

    /**
     * @var bool
     */
    private bool $artificial = false;

    /**
     * @var bool
     */
    private bool $extern = false;

    /**
     * @var array<FunctionArgument>
     */
    private array $arguments;

    /**
     * @param T $returns
     * @param string $name
     * @param array<FunctionArgument> $arguments
     */
    public function __construct(TypeInterface $returns, string $name, array $arguments = [])
    {
        $this->returns = $returns;
        $this->name = $name;
        $this->arguments = $arguments;
    }

    /**
     * @param bool $inline
     * @return $this
     */
    public function withInline(bool $inline): self
    {
        $self = clone $this;
        $self->inline = $inline;

        return $self;
    }

    /**
     * @param bool $static
     * @return $this
     */
    public function withStatic(bool $static): self
    {
        $self = clone $this;
        $self->static = $static;

        return $self;
    }

    /**
     * @param bool $artificial
     * @return $this
     */
    public function withArtificial(bool $artificial): self
    {
        $self = clone $this;
        $self->artificial = $artificial;

        return $self;
    }

    /**
     * @return bool
     */
    public function isExtern(): bool
    {
        return $this->extern;
    }

    /**
     * @param bool $extern
     * @return $this
     */
    public function withExtern(bool $extern): self
    {
        $self = clone $this;
        $self->extern = $extern;

        return $self;
    }


    /**
     * @return bool
     */
    public function isInline(): bool
    {
        return $this->inline;
    }

    /**
     * @return bool
     */
    public function isArtificial(): bool
    {
        return $this->artificial;
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->static;
    }

    /**
     * @param TypeInterface $type
     * @return $this
     */
    public function withReturnType(TypeInterface $type): self
    {
        $self = clone $this;
        $self->returns = $type;

        return $self;
    }

    /**
     * @internal This method contains internal mutations and may damage the abstract syntax tree.
     * @param TypeInterface $type
     */
    public function setReturnType(TypeInterface $type): void
    {
        $this->returns = $type;
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
     * @param array<FunctionArgument> $arguments
     * @return $this
     */
    public function withArguments(array $arguments): self
    {
        $self = clone $this;
        $self->arguments = $arguments;

        return $self;
    }


    /**
     * @return TypeInterface
     */
    public function getReturnType(): TypeInterface
    {
        return $this->returns;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<FunctionArgument>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
