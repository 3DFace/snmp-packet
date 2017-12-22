<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class Integer32 implements DataType
{

    public const MIN = -2147483648;
    public const MAX = 2147483647;

    /** @var int */
    private $value;

    public function __construct(int $value)
    {
        if ($value < self::MIN) {
            throw new \InvalidArgumentException('Integer32 mus be in range [' . self::MIN . '...' . self::MAX . ']');
        }
        if ($value > self::MAX) {
            throw new \InvalidArgumentException('Integer32 mus be in range [' . self::MIN . '...' . self::MAX . ']');
        }
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitInteger32($this->value);
    }

    public function equals($val): bool
    {
        return $val instanceof self && $val->value === $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function toASN1(): Element
    {
        return new Integer($this->value);
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
            $int = UnspecifiedType::fromDER($binary);
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($int);
    }

    /**
     * @param UnspecifiedType $int
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(UnspecifiedType $int): self
    {
        try {
            $value = $int->asInteger()->number();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return new self($value);
    }

}
