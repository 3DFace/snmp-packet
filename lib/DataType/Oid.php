<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Primitive\ObjectIdentifier;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class Oid implements DataType
{

    /** @var string */
    private $value;

    /**
     * @param string $value
     * @throws \InvalidArgumentException
     */
    public function __construct(string $value)
    {
        if (!preg_match('/^(1(\.3(\.([1-9]\d{0,3}|0))*){0,1}){0,1}$/', $value)) {
            throw new \InvalidArgumentException('Bad SNMP oid: ' . $value);
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitOid($this->value);
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
        return new ObjectIdentifier($this->value);
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
     * @param UnspecifiedType $oid
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(UnspecifiedType $oid): self
    {
        try {
            $value = $oid->asObjectIdentifier()->oid();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        try {
            return new self($value);
        } catch (\InvalidArgumentException $e) {
            // underlying lib should return valid oid, so this will never happen... how to test?
            throw new DecodeError($e->getMessage(), 0, $e);
        }
    }

}
