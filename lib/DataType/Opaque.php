<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Component\Identifier;
use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use dface\SnmpPacket\Exception\DecodeError;

class Opaque implements DataType
{

    public const TAG = 4;

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
        return $visitor->visitOpaque($this->value);
    }

    public function equals($val): bool
    {
        return $val instanceof self && $val->value === $this->value;
    }

    public function toASN1(): Element
    {
        $oct = new \ASN1\Type\Primitive\OctetString($this->value);
        return new ImplicitlyTaggedType(self::TAG, $oct, Identifier::CLASS_APPLICATION);
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
            $element = Element::fromDER($binary);
            $class = $element->typeClass();
            if ($class !== Identifier::CLASS_APPLICATION) {
                throw new DecodeError(__CLASS__ . ' expects asn1 app class');
            }
            $app = $element->asUnspecified()->asApplication();
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return static::fromASN1($app);
    }

    /**
     * @param ApplicationType $element
     * @return static
     * @throws DecodeError
     */
    public static function fromASN1(ApplicationType $element): self
    {
        try {
            $value = $element->asImplicit(Element::TYPE_OCTET_STRING, self::TAG)->asOctetString()->string();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return new static($value);
    }

}
