<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

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

    public function __toString(): string
    {
        return '';
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
            $element = UnspecifiedType::fromDER($binary);
        } catch (DecodeException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($element);
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
