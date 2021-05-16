<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml;

use Serafim\CastXml\Exception\CastXmlException;
use Serafim\CastXml\Internal\Process;
use Serafim\CastXml\Internal\ProcessInterface;

final class CastXml
{
    /**
     * @var string
     */
    private const PCRE_TPL_VERSION = '/%s\hversion\h(\d+\.\d+\.\d+(?:-\d+)?)/iu';

    /**
     * @var string
     */
    private const DEFAULT_BINARY = 'castxml';

    /**
     * @var ProcessInterface
     */
    private ProcessInterface $process;

    /**
     * @var string
     */
    private string $temp;

    /**
     * @param string $binary
     */
    public function __construct(string $binary = self::DEFAULT_BINARY)
    {
        $this->process = new Process($binary);
        $this->temp = \sys_get_temp_dir();
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function withTempDirectory(string $directory): self
    {
        $self = clone $this;
        $self->temp = $directory;

        return $self;
    }

    /**
     * @return string
     */
    public function getTempDirectory(): string
    {
        return $this->temp;
    }

    /**
     * @return string
     * @throws CastXmlException
     */
    public function getVersion(): string
    {
        return $this->parseVersionSection('castxml');
    }

    /**
     * @return string
     * @throws CastXmlException
     */
    public function getClangVersion(): string
    {
        return $this->parseVersionSection('clang');
    }

    /**
     * @param string $prefix
     * @return string
     * @throws CastXmlException
     */
    private function parseVersionSection(string $prefix): string
    {
        $result = $this->process->run('--version');

        $pcre = \sprintf(self::PCRE_TPL_VERSION, \preg_quote($prefix, '/'));
        \preg_match($pcre, $result, $output);

        if (!isset($output[1])) {
            throw new CastXmlException(
                'Can not parse version section.' . \PHP_EOL .
                'Actual CastXML output:' . \PHP_EOL .
                $result
            );
        }

        return $output[1];
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        try {
            $this->process->run();
        } catch (CastXmlException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $file
     * @param string|null $cwd
     * @return Result
     * @throws CastXmlException
     */
    public function parse(string $file, string $cwd = null): Result
    {
        if (! \is_file($file)) {
            throw new \InvalidArgumentException(\sprintf('File [%s] not found', $file));
        }

        $out = $this->getTempDirectory() . '/' . \basename($file, '.h') . '.xml';

        $this->process->runIn($cwd ?? \dirname($file), $file, '--castxml-output=1', '-o', $out);

        if (! \is_file($out)) {
            throw new CastXmlException('Generated file not available');
        }

        return Result::fromPathname($out, true);
    }
}
