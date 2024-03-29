<?php

/**
 * This file is part of CastXml package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\CastXml\Internal;

use Serafim\CastXml\Ast\ArrayType;
use Serafim\CastXml\Ast\CallbackType;
use Serafim\CastXml\Ast\ConstantType;
use Serafim\CastXml\Ast\EnumType;
use Serafim\CastXml\Ast\Field;
use Serafim\CastXml\Ast\FunctionArgument;
use Serafim\CastXml\Ast\FunctionDefinition;
use Serafim\CastXml\Ast\FundamentalType;
use Serafim\CastXml\Ast\LazyInitializedTypeInterface;
use Serafim\CastXml\Ast\Pointer;
use Serafim\CastXml\Ast\StructType;
use Serafim\CastXml\Ast\TypeDefinition;
use Serafim\CastXml\Ast\TypeInterface;
use Serafim\CastXml\Ast\UnimplementedType;
use Serafim\CastXml\Ast\UnionType;

/**
 * @internal CastXMLParser is an internal library class, please do not use it in your code.
 * @psalm-internal Serafim\CastXml
 */
final class CastXMLParser extends Parser
{
    /**
     * @var \DOMDocument
     */
    private \DOMDocument $dom;

    /**
     * @var string
     */
    private const IGNORED_NODES = [
        'Namespace',
        'File',
    ];

    /**
     * @var \ArrayObject
     */
    private \ArrayObject $storage;

    /**
     * @param \DOMDocument $dom
     */
    public function __construct(\DOMDocument $dom)
    {
        $this->storage = new \ArrayObject();

        if (! $dom->firstChild || $dom->firstChild->nodeName !== 'CastXML') {
            throw new \InvalidArgumentException('Passed DOMDocument is not looks like of CastXML file');
        }

        $this->dom = $dom;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        /** @var \DOMNode $root */
        $root = $this->dom->firstChild;

        /** @var \DOMElement $el */
        foreach ($root->childNodes as $el) {
            if (\in_array($el->nodeName, self::IGNORED_NODES, true)) {
                continue;
            }

            if ($el instanceof \DOMElement) {
                /** @var TypeDefinition $result */
                yield $this->findOrCreate($el);
            }
        }
    }

    /**
     * @param \DOMElement $el
     * @return TypeInterface
     */
    private function findOrCreate(\DOMElement $el): TypeInterface
    {
        $id = $el->getAttribute('id');

        $result = $this->storage[$id] ??= $this->create($el);

        if ($result instanceof LazyInitializedTypeInterface) {
            $result->resolve();
        }

        return $result;
    }

    /**
     * @param \DOMElement $el
     * @return TypeInterface
     */
    private function create(\DOMElement $el): TypeInterface
    {
        switch ($el->nodeName) {
            // Relation
            case 'ElaboratedType':
                return $this->findOrCreateId(
                    $el->getAttribute('type')
                );

            case 'Typedef':
                return $this->createTypeDefinition($el);

            case 'FundamentalType':
                return $this->createFundamentalType($el);

            case 'Struct':
                return $this->createStructType($el);

            case 'PointerType':
                return $this->createPointer($el);

            case 'ArrayType':
                return $this->createArrayType($el);

            case 'Enumeration':
                return $this->createEnumType($el);

            case 'FunctionType':
                return $this->createCallbackType($el);

            case 'Function':
                return $this->createFunctionDefinition($el);

            case 'Unimplemented':
                return $this->createUnimplementedType($el);

            case 'Union':
                return $this->createUnionType($el);

            case 'CvQualifiedType':
                return $this->createConstantType($el);

            case 'Field':
                return $this->createField($el);

            default:
                throw new \LogicException('Unsupported element [' . $el->nodeName . ']');
        }
    }

    /**
     * @param \DOMElement $el
     * @return UnionType
     */
    private function createUnionType(\DOMElement $el): UnionType
    {
        return new UnionType(
            $el->getAttribute('name') ?: null,
            (int)$el->getAttribute('size'),
            (int)$el->getAttribute('align'),
            $this->getMembers($el, 'members'),
        );
    }

    /**
     * TODO Add "bits" attr support
     * TODO Add "offset" attr support
     * TODO Add "mutable" attr support
     * TODO Add "attributes" attr support
     * TODO Add "deprecation" attr support
     * TODO Add "annotation" attr support
     *
     * @param \DOMElement $el
     * @return Field
     */
    private function createField(\DOMElement $el): Field
    {
        return new Field(
            $this->findOrCreateId($el->getAttribute('type')),
            $el->getAttribute('name') ?: null,
        );
    }

    /**
     * @param \DOMNodeList $arguments
     * @return array<FunctionArgument>
     */
    private function createFunctionArguments(\DOMNodeList $arguments): array
    {
        /** @var FunctionArgument[] $result */
        $result = [];

        /** @var \DOMElement $argument */
        foreach ($arguments as $argument) {
            if ($argument->nodeName === '#text') {
                continue;
            }

            switch ($argument->nodeName) {
                case '#text':
                    continue 2;

                case 'Argument':
                    $result[] = new FunctionArgument(
                        $this->findOrCreateId($argument->getAttribute('type')),
                        $argument->getAttribute('name') ?: null,
                    );
                    break;

                case 'Ellipsis':
                    if ($result !== []) {
                        $last = \array_key_last($result);
                        $result[$last] = $result[$last]
                            ->withVariadic(true)
                        ;

                        break;
                    }

                    throw new \LogicException('Ellipsis XML node cannot be the only one');

                default:
                    throw new \LogicException('Unsupported element [' . $argument->nodeName . ']');
            }
        }

        return $result;
    }

    /**
     * TODO Add "const" attr support
     * TODO Add "volatile" attr support
     * TODO Add "restrict" attr support
     * TODO Add "attributes" attr support
     * TODO Add "deprecation" attr support
     * TODO Add "annotation" attr support
     *
     * @param \DOMElement $el
     * @return CallbackType
     */
    private function createCallbackType(\DOMElement $el): CallbackType
    {
        return new CallbackType(
            $this->findOrCreateId($el->getAttribute('returns')),
            $this->createFunctionArguments($el->childNodes),
        );
    }

    /**
     * TODO Add "kind" attr support
     *
     * @param \DOMElement $el
     * @return UnimplementedType
     */
    private function createUnimplementedType(\DOMElement $el): UnimplementedType
    {
        return new UnimplementedType(
            $el->getAttribute('type_class'),
        );
    }

    /**
     * TODO Add "deprecation" attr support
     * TODO Add "annotation" attr support
     * TODO Add "attributes" attr support
     *
     * @param \DOMElement $el
     * @return FunctionDefinition
     */
    private function createFunctionDefinition(\DOMElement $el): FunctionDefinition
    {
        $function = new FunctionDefinition(
            $this->findOrCreateId($el->getAttribute('returns')),
            $el->getAttribute('name'),
            $this->createFunctionArguments($el->childNodes),
        );

        return $function
            ->withArtificial((bool)$el->getAttribute('artificial'))
            ->withInline((bool)$el->getAttribute('inline'))
            ->withStatic((bool)$el->getAttribute('static'))
            ->withExtern((bool)$el->getAttribute('extern'))
        ;
    }

    /**
     * TODO Add "attributes" attr support
     * TODO Add "deprecation" attr support
     * TODO Add "annotation" attr support
     *
     * @param \DOMElement $el
     * @return TypeDefinition
     */
    private function createTypeDefinition(\DOMElement $el): TypeDefinition
    {
        return new TypeDefinition(
            $this->findOrCreateId($el->getAttribute('type')),
            $el->getAttribute('name'),
        );
    }

    /**
     * @param \DOMElement $el
     * @return ArrayType
     */
    private function createArrayType(\DOMElement $el): ArrayType
    {
        return new ArrayType(
            $this->findOrCreateId($el->getAttribute('type')),
            (int)$el->getAttribute('min'),
            (int)$el->getAttribute('max'),
        );
    }

    /**
     * @param \DOMElement $el
     * @return Pointer
     */
    private function createPointer(\DOMElement $el): Pointer
    {
        return new Pointer(
            $this->findOrCreateId($el->getAttribute('type')),
            // Optional
            (int)$el->getAttribute('size'),
            (int)$el->getAttribute('align'),
        );
    }

    /**
     * @param \DOMElement $el
     * @return FundamentalType
     */
    private function createFundamentalType(\DOMElement $el): FundamentalType
    {
        return new FundamentalType(
            $el->getAttribute('name'),
            (int)$el->getAttribute('size'),
            (int)$el->getAttribute('align'),
        );
    }

    /**
     * @param \DOMElement $el
     * @param string $attr
     * @return iterable<string>
     */
    private function getMemberIdentifiers(\DOMElement $el, string $attr): iterable
    {
        $members = $el->getAttribute($attr);

        if (! $members) {
            return [];
        }

        $map = static fn (string $id): string => \trim($id);

        return \array_map($map, \array_filter(\explode(' ', $members)));
    }

    /**
     * @param \DOMElement $el
     * @param string $attr
     * @return iterable<Field>
     */
    private function getMembers(\DOMElement $el, string $attr): iterable
    {
        foreach ($this->getMemberIdentifiers($el, $attr) as $id) {
            yield $this->findOrCreateId($id);
        }
    }

    /**
     * TODO Add "incomplete" attr support
     * TODO Add "attributes" attr support
     * TODO Add "deprecation" attr support
     * TODO Add "annotation" attr support
     *
     * @param \DOMElement $el
     * @return StructType
     */
    private function createStructType(\DOMElement $el): StructType
    {
        return new StructType(
            $el->getAttribute('name'),
            (int)$el->getAttribute('size'),
            (int)$el->getAttribute('align'),
            $this->getMembers($el, 'members')
        );
    }

    /**
     * TODO Add "attributes" attr support
     * TODO Add "deprecation" attr support
     * TODO Add "annotation" attr support
     *
     * @param \DOMElement $el
     * @return EnumType
     */
    private function createEnumType(\DOMElement $el): EnumType
    {
        $values = [];

        /** @var \DOMElement $value */
        foreach ($el->childNodes as $value) {
            if ($value->nodeName === 'EnumValue') {
                $values[$value->getAttribute('name')] = (int)$value->getAttribute('init');
            }
        }

        return new EnumType(
            $el->getAttribute('name') ?: null,
            (int)$el->getAttribute('size'),
            (int)$el->getAttribute('align'),
            $values
        );
    }

    /**
     * TODO Add "volatile" attr support
     * TODO Add "restrict" attr support
     *
     * @param \DOMElement $el
     * @return ConstantType
     */
    private function createConstantType(\DOMElement $el): ConstantType
    {
        return new ConstantType(
            $this->findOrCreateId($el->getAttribute('type'))
        );
    }

    /**
     * @param string $id
     * @return TypeInterface
     */
    private function findOrCreateId(string $id): TypeInterface
    {
        return $this->findOrCreate(
            $this->getElementById($id)
        );
    }

    /**
     * @param string $id
     * @return \DOMElement
     */
    private function getElementById(string $id): \DOMElement
    {
        $result = $this->findElementById($id);

        if (! $result instanceof \DOMElement) {
            throw new \LogicException('DOMElement(id="' . $id . '") could not be found');
        }

        return $result;
    }

    /**
     * @param string $id
     * @return \DOMElement|null
     */
    private function findElementById(string $id): ?\DOMElement
    {
        return $this->dom->getElementById($id);
    }
}
