<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Parser;

use Serafim\CastXml\Parser\Ast\TypeInterface;

final class StatefulParser extends Parser
{
    /**
     * @var array<TypeInterface>
     */
    private array $types;

    /**
     * @param array<TypeInterface> $types
     */
    public function __construct(array $types)
    {
        $this->types = \array_values($types);
    }

    /**
     * @param string $file
     * @return static
     */
    public static function fromFile(string $file): self
    {
        return new self(require $file);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->types);
    }
}
