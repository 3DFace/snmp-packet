<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class BitString implements DataType
{

    /** @var string */
    private $value;
    /** @var int */
    private $unused_bits;

    public function __construct(string $value, int $unused_bits)
    {
        $this->value = $value;
        $this->unused_bits = $unused_bits;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getUnusedBits(): int
    {
        return $this->unused_bits;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitBitString($this->value, $this->unused_bits);
    }

    public function equals($val): bool
    {
        return $val instanceof self
            && $val->value === $this->value
            && $val->unused_bits === $this->unused_bits;
    }

    public function toASN1(): Element
    {
        return new \ASN1\Type\Primitive\BitString($this->value, $this->unused_bits);
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
            $bit_string = UnspecifiedType::fromDER($binary);
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($bit_string);
    }

    /**
     * @param UnspecifiedType $element
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(UnspecifiedType $element): self
    {
        try {
            $bit_string = $element->asBitString();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        $value = $bit_string->string();
        $unused = $bit_string->unusedBits();
        return new self($value, $unused);
    }

}
