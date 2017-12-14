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

    /** @var int|string */
    protected $value;

    public function __construct($value)
    {
        if (gmp_cmp($value, 0) < 0 || gmp_cmp($value, 4294967295) > 0) {
            throw new \InvalidArgumentException('Counter32 must be in range [0...4294967295]');
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
            $element = Element::fromDER($binary);
            $class = $element->typeClass();
            $tag = $element->tag();
            $self_tag = static::getTag();
            if ($class !== Identifier::CLASS_APPLICATION || $tag !== $self_tag) {
                throw new DecodeError(__CLASS__ . " expects asn1 app class with tag $self_tag");
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
            $value = $element->asImplicit(Element::TYPE_INTEGER)->asInteger()->number();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        return new static($value);
    }

}
