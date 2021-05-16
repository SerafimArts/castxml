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
class CallbackType extends Type
{
    /**
     * @var T
     */
    private TypeInterface $return;

    /**
     * @var array<FunctionArgument>
     */
    private array $arguments;

    /**
     * @param T $return
     * @param array<FunctionArgument> $arguments
     */
    public function __construct(TypeInterface $return, array $arguments = [])
    {
        $this->return = $return;
        $this->arguments = $arguments;
    }

    /**
     * @param TypeInterface $return
     * @return $this
     */
    public function withReturnType(TypeInterface $return): self
    {
        $self = clone $this;
        $self->return = $return;

        return $self;
    }

    /**
     * @internal This method contains internal mutations and may damage the abstract syntax tree.
     * @param TypeInterface $type
     */
    public function setReturnType(TypeInterface $type): void
    {
        $this->return = $type;
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
        return $this->return;
    }

    /**
     * @return array<FunctionArgument>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
