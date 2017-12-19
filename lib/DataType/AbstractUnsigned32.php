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

abstract class AbstractUnsigned32 implements DataType
{

    public const MIN = 0;
    public const MAX = 4294967295;

    /** @var int|string */
    protected $value;

    /**
     * @param int|string $value
     */
    public function __construct($value)
    {
        if (gmp_cmp($value, 0) < 0) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            throw new \InvalidArgumentException('Unsigned32 must be in range [' . self::MIN . '...' . self::MAX . ']');
        }
        if (gmp_cmp($value, self::MAX) > 0) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            throw new \InvalidArgumentException('Unsigned32 must be in range [' . self::MIN . '...' . self::MAX . ']');
        }
        $this->value = $value;
    }

    abstract protected static function getTag(): int;

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function equals($val): bool
    {
        return $val instanceof static && gmp_cmp($val->value, $this->value) === 0;
    }

    public function toASN1(): Element
    {
        $int = new Integer($this->value);
        return new ImplicitlyTaggedType(static::getTag(), $int, Identifier::CLASS_APPLICATION);
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
        $self_tag = static::getTag();
        try {
            $value = $element->asImplicit(Element::TYPE_INTEGER, $self_tag)->asInteger()->number();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return new static($value);
    }

}
