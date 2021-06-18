<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Internal;

use Serafim\CastXml\Exception\CastXmlException;
use Symfony\Component\Process\Process as SymfonyProcess;

/**
 * @internal Process is an internal library class, please do not use it in your code.
 * @psalm-internal Serafim\CastXml
 */
final class Process implements ProcessInterface
{
    /**
     * @var string
     */
    private string $binary;

    /**
     * @param string $binary
     */
    public function __construct(string $binary)
    {
        $this->binary = $binary;
    }

    /**
     * @param string $cwd
     * @param string ...$args
     * @return string
     * @throws CastXmlException
     */
    public function runIn(string $cwd, string ...$args): string
    {
        $process = new SymfonyProcess([$this->binary, ...$args], $cwd);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new CastXmlException($process->getErrorOutput(), (int)$process->getExitCode());
        }

        return $process->getOutput();
    }

    /**
     * {@inheritDoc}
     */
    public function run(string ...$args): string
    {
        return $this->runIn(\getcwd() ?: '.', ...$args);
    }
}
