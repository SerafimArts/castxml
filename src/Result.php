<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml;

use Serafim\CastXml\Ast\TypeInterface;
use Serafim\CastXml\Exception\CastXmlException;
use Serafim\CastXml\Exception\DependencyException;
use Serafim\CastXml\Internal\CastXMLParser;
use Serafim\CastXml\Internal\ParserInterface;

/**
 * @template-implements \IteratorAggregate<int, TypeInterface>
 * @see TypeInterface
 */
final class Result implements \Stringable, \IteratorAggregate
{
    /**
     * @var string
     */
    private const SCHEMA_XSD = __DIR__ . '/../resources/castxml.xsd';

    /**
     * @var \SplFileInfo
     */
    private \SplFileInfo $file;

    /**
     * @var bool
     */
    private bool $disposable;

    /**
     * @var ParserInterface|null
     */
    private ?ParserInterface $parser = null;

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
     * @param string $file
     * @param bool $disposable
     * @return static
     */
    public static function fromPathname(string $file, bool $disposable = false): self
    {
        return new self(new \SplFileInfo($file), $disposable);
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
            throw new DependencyException('ext-simplexml extension not available');
        }

        return \simplexml_load_string(
            \file_get_contents($this->file->getPathname()),
            \SimpleXMLElement::class,
            $options
        );
    }

    /**
     * @param int $flags
     * @param string $encoding
     * @return \XMLReader
     * @throws CastXmlException
     */
    public function toXmlReader(int $flags = 0, string $encoding = 'UTF-8'): \XMLReader
    {
        if (! \class_exists(\XMLReader::class)) {
            throw new DependencyException('ext-xmlreader extension not available');
        }

        $reader = new \XMLReader();
        $reader->open($this->file->getPathname(), $encoding, $flags);

        return $reader;
    }

    /**
     * @param string $charset
     * @return \DOMDocument
     * @throws DependencyException
     */
    public function toDomDocument(string $charset = 'UTF-8'): \DOMDocument
    {
        if (! \class_exists(\DOMDocument::class)) {
            throw new DependencyException('ext-dom extension not available');
        }

        $internalErrors = \libxml_use_internal_errors(true);
        if (\LIBXML_VERSION < 20900) {
            $disableEntities = \libxml_disable_entity_loader(true);
        }

        try {
            $dom = new \DOMDocument('1.0', $charset);
            $dom->validateOnParse = true;
            $dom->xmlStandalone = true;
            $dom->load($this->file->getFilename(), \LIBXML_NONET);
            $dom->schemaValidate(self::SCHEMA_XSD);

            return $dom;
        } finally {
            \libxml_use_internal_errors($internalErrors);
            if (isset($disableEntities)) {
                \libxml_disable_entity_loader($disableEntities);
            }
        }
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

    /**
     * @return array<array-key, TypeInterface>
     * @throws DependencyException
     */
    public function toArray(): array
    {
        return \iterator_to_array($this->getIterator(), false);
    }

    /**
     * @return \Traversable<int, TypeInterface>
     * @throws DependencyException
     */
    public function getIterator(): \Traversable
    {
        $this->parser ??= new CastXMLParser($this->toDomDocument());

        return $this->parser->getIterator();
    }
}
