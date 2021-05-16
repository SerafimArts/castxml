<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Internal;

/**
 * @internal ProcessInterface is an internal library interface, please do not use it in your code.
 * @psalm-internal Serafim\CastXml
 */
interface ProcessInterface
{
    /**
     * @param string ...$args
     * @return string
     */
    public function run(string ...$args): string;

    /**
     * @param string $cwd
     * @param string ...$args
     * @return string
     */
    public function runIn(string $cwd, string ...$args): string;
}
