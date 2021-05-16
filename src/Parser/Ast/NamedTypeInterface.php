<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Parser\Ast;

interface NamedTypeInterface extends OptionalNamedTypeInterface
{
    /**
     * @return string
     */
    public function getName(): string;
}
