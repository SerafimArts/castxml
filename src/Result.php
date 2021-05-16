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
use Serafim\CastXml\Parser\CastXMLParser;
use Serafim\CastXml\Parser\ParserInterface;

final class Result implements \Stringable
{
    /**
     * @var \SplFileInfo
     */
    private \SplFileInfo $file;

    /**
     * @var bool
     */
    private bool $disposable;

    /**
     * @param \SplFileInfo $file
     * @param bool $disposable
     */
    public function __construct(\SplFileInfo $file, bool $disposable = false)
    {
        $this->file = $file;
        $this->disposable = $disposable;
    }

    /**
     * @param string $directory
     * @return $this
     * @throws CastXmlException
     */
    public function saveIn(string $directory): self
    {
        if (!@\mkdir($directory, 0777, true) && !\is_dir($directory)) {
            throw new CastXmlException(\sprintf('Directory [%s] is not available for writing', $directory));
        }

        return $this->saveAs($directory . '/' . $this->file->getBasename());
    }

    /**
     * @param string $filename
     * @return $this
     * @throws CastXmlException
     */
    public function saveAs(string $filename): self
    {
        \error_clear_last();
        $status = @\copy($this->file->getPathname(), $filename);

        if (!$status || !\is_file($filename)) {
            $error = \error_get_last();
            throw new CastXmlException($error['message'] ?? 'Can not save output file');
        }

        return new self(new \SplFileInfo($filename));
    }

    /**
     * @param int $options
     * @return \SimpleXMLElement
     * @throws CastXmlException
     */
    public function toXml(int $options = 0): \SimpleXMLElement
    {
        if (! \function_exists('\\simplexml_load_file')) {
            throw new CastXmlException('ext-simplexml extension not available');
        }

        return \simplexml_load_string(
            \file_get_contents($this->file->getPathname()),
            null,
            $options
        );
    }

    /**
     * @param int $flags
     * @param string|null $encoding
     * @return \XMLReader
     * @throws CastXmlException
     */
    public function toXmlReader(int $flags = 0, string $encoding = null): \XMLReader
    {
        if (! \class_exists(\XMLReader::class)) {
            throw new CastXmlException('ext-xmlreader extension not available');
        }

        if (\version_compare(\PHP_VERSION, '8.0.0') >= 0) {
            return \XMLReader::open($this->file->getPathname(), $encoding, $flags);
        }

        $reader = new \XMLReader();
        $reader->open($this->file->getPathname(), $encoding, $flags);

        return $reader;
    }

    /**
     * @return ParserInterface
     */
    public function toPhp(): ParserInterface
    {
        return new CastXMLParser($this->file);
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return \file_get_contents(
            $this->file->getPathname()
        );
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getContents();
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        if ($this->disposable) {
            @\unlink($this->file->getPathname());
        }
    }
}
