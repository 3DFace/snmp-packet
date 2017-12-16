<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Primitive\OctetString;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class EncryptedPDU implements ScopedPDUData
{

    /** @var string */
    private $content;

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function equals($x): bool
    {
        return $x instanceof self && $x->content === $this->content;
    }

    public function toBinary(): string
    {
        $asn1 = $this->toASN1();
        return $asn1->toDER();
    }

    public function toASN1(): Element
    {
        return new OctetString($this->content);
    }

    /**
     * @param string $binary
     * @return self
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): self
    {
        try {
            $element = UnspecifiedType::fromDER($binary);
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError('Cant decode EncryptedPDU: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($element);
    }

    /**
     * @param UnspecifiedType $element
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(UnspecifiedType $element): self
    {
        try {
            $content = $element->asOctetString()->string();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError('Cant decode EncryptedPDU: ' . $e->getMessage(), 0, $e);
        }
        return new self($content);
    }

}
