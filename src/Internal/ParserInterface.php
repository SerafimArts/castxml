<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Internal;

use Serafim\CastXml\Ast\TypeInterface;

/**
 * @internal ParserInterface is an internal library interface, please do not use it in your code.
 * @psalm-internal Serafim\CastXml
 *
 * @template-implements \IteratorAggregate<int, TypeInterface>
 * @see TypeInterface
 */
interface ParserInterface extends \IteratorAggregate
{
    /**
     * @return \Traversable<int, TypeInterface>
     */
    public function getIterator(): \Traversable;
}
