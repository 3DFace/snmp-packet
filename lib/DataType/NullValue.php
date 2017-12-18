<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Primitive\NullType;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class NullValue implements DataType
{

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitNull();
    }

    public function equals($val): bool
    {
        return $val instanceof self;
    }

    public function toASN1(): Element
    {
        return new NullType();
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
            $null = UnspecifiedType::fromDER($binary);
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($null);
    }

    /**
     * @param UnspecifiedType $null
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(UnspecifiedType $null): self
    {
        try {
            $null->asNull();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return new self();
    }

}
