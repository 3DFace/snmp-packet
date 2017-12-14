<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Component\Identifier;
use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Primitive\NullType;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

abstract class AbstractNoValue implements DataType
{

    abstract protected static function getTag(): int;

    public function equals($val): bool
    {
        return $val instanceof self;
    }

    public function toASN1(): Element
    {
        $null = new NullType();
        return new ImplicitlyTaggedType(static::getTag(), $null);
    }

    public function toBinary(): string
    {
        $asn1 = $this->toASN1();
        return $asn1->toDER();
    }

    /**
     * @param string $binary
     * @return static
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): self
    {
        try {
            $bit_string = Element::fromDER($binary)->asUnspecified();
            $class = $bit_string->typeClass();
            $tag = $bit_string->tag();
            $self_tag = static::getTag();
            if ($class !== Identifier::CLASS_CONTEXT_SPECIFIC || $tag !== $self_tag) {
                throw new DecodeError(__CLASS__ . ' expects asn1 context specific tag ' . $self_tag);
            }
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($bit_string);
    }

    /**
     * @param UnspecifiedType $element
     * @return static
     * @throws DecodeError
     */
    public static function fromASN1(UnspecifiedType $element): self
    {
        try {
            $element->asTagged()->asImplicit(Element::TYPE_NULL, static::getTag());
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return new static();
    }

}
