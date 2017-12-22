<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class OctetString implements DataType
{

    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitOctetString($this->value);
    }

    public function equals($val): bool
    {
        return $val instanceof self && $val->value === $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function toASN1(): Element
    {
        return new \ASN1\Type\Primitive\OctetString($this->value);
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
            $str = UnspecifiedType::fromDER($binary);
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($str);
    }

    /**
     * @param UnspecifiedType $str
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(UnspecifiedType $str): self
    {
        try {
            $value = $str->asOctetString()->string();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return new self($value);
    }

}
