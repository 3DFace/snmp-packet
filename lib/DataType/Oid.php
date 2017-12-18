<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Component\Identifier;
use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Primitive\ObjectIdentifier;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class Oid implements DataType
{

    /** @var string */
    private $value;

    public function __construct(string $value)
    {
        /** @noinspection NotOptimalRegularExpressionsInspection */
        if (!preg_match('/^([1-9][0-9]{0,3}|0)(\.([1-9][0-9]{0,3}|0))+$/', $value)) {
            throw new \InvalidArgumentException('Bad oid: ' . $value);
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
            $str = Element::fromDER($binary)->asUnspecified();
            $class = $str->typeClass();
            $tag = $str->tag();
            if ($class !== Identifier::CLASS_UNIVERSAL || $tag !== Element::TYPE_OBJECT_IDENTIFIER) {
                throw new DecodeError(__CLASS__ . ' expects asn1 universal object identifier');
            }
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
        return new self($value);
    }


}
