<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Component\Identifier;
use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use dface\SnmpPacket\Exception\DecodeError;

class Counter64 implements DataType
{

    public const TAG = 6;
    public const MIN = 0;
    public const MAX = '18446744073709551615';

    /** @var int|string */
    private $value;

    /**
     * @param int|string|\gmp $value
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        if (gmp_cmp($value, self::MIN) < 0) {
            throw new \InvalidArgumentException('Counter64 must be in range [' . self::MIN . '...' . self::MAX . ']');
        }
        if (gmp_cmp($value, self::MAX) > 0) {
            throw new \InvalidArgumentException('Counter64 must be in range [' . self::MIN . '...' . self::MAX . ']');
        }
        $this->value = $value;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitCounter64($this->value);
    }

    public function equals($val): bool
    {
        return $val instanceof static && gmp_cmp($val->value, $this->value) === 0;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function toASN1(): Element
    {
        $int = new Integer($this->value);
        return new ImplicitlyTaggedType(self::TAG, $int, Identifier::CLASS_APPLICATION);
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
            /** @var ApplicationType $element */
            $element = ApplicationType::fromDER($binary);
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return static::fromASN1($element);
    }

    /**
     * @param ApplicationType $element
     * @return static
     * @throws DecodeError
     */
    public static function fromASN1(ApplicationType $element): self
    {
        try {
            $value = $element->asImplicit(Element::TYPE_INTEGER, self::TAG)->asInteger()->number();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        try {
            return new static($value);
        } catch (\InvalidArgumentException $e) {
            throw new DecodeError($e->getMessage(), 0, $e);
        }
    }

}
