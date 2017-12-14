<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Component\Identifier;
use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use dface\SnmpPacket\Exception\DecodeError;

class IpAddress implements DataType
{

    public const TAG = 0;

    /** @var string */
    private $value;

    public function __construct(string $ip)
    {
        if (ip2long($ip) === false) {
            throw new \InvalidArgumentException('Invalid IP address: ' . $ip);
        }
        $this->value = $ip;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitIpAddress($this->value);
    }

    public function equals($val): bool
    {
        return $val instanceof self && $val->value === $this->value;
    }

    public function toASN1(): Element
    {
        $byte_arr = explode('.', $this->value);
        $bin = \chr($byte_arr[0]) . \chr($byte_arr[1]) . \chr($byte_arr[2]) . \chr($byte_arr[3]);
        $oct = new \ASN1\Type\Primitive\OctetString($bin);
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
            $tag = $element->tag();
            if ($class !== Identifier::CLASS_APPLICATION || $tag !== self::TAG) {
                throw new DecodeError(__CLASS__ . ' expects asn1 app class with tag ' . self::TAG);
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
            $value = $element->asImplicit(Element::TYPE_OCTET_STRING)->asOctetString()->string();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError(__CLASS__ . ' decode error: ' . $e->getMessage(), 0, $e);
        }
        if (\strlen($value) !== 4) {
            throw new DecodeError('IpAddress must be a binary of length 4');
        }
        $ip = \ord($value[0]) . '.' . \ord($value[1]) . '.' . \ord($value[2]) . '.' . \ord($value[3]);
        return new static($ip);
    }

}
