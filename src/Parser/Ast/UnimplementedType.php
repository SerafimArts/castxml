<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Parser\Ast;

class UnimplementedType extends Type implements NamedTypeInterface
{
    /**
     * @var string
     */
    private string $class;

    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function withClass(string $class): self
    {
        $self = clone $this;
        $self->class = $class;

        return $self;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->class;
    }
}
