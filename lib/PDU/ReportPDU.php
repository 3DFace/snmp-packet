<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use ASN1\Type\TaggedType;
use dface\SnmpPacket\Exception\DecodeError;

class ReportPDU implements PDU
{

    public const TAG = 8;

    /** @var BasicPDUBody */
    private $body;

    /**
     * @param BasicPDUBody $body
     */
    public function __construct(BasicPDUBody $body)
    {
        $this->body = $body;
    }

    public function getBody(): BasicPDUBody
    {
        return $this->body;
    }

    public function toASN1(): ImplicitlyTaggedType
    {
        return new ImplicitlyTaggedType(self::TAG, $this->body->toASN1());
    }

    public function equals(PDU $pdu): bool
    {
        return $pdu instanceof self && $pdu->body->equals($this->body);
    }

    /**
     * @param TaggedType $obj
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(TaggedType $obj): self
    {
        if ($obj->tag() !== self::TAG) {
            throw new DecodeError('Invalid tagged object tag, ' . self::TAG . ' expected');
        }
        try {
            /** @var Sequence $children */
            $children = $obj->asImplicit(Element::TYPE_SEQUENCE)->asSequence();
        } catch (\UnexpectedValueException $e) {
            throw new DecodeError('Report PDU must be a sequence', 0, $e);
        }
        $body = BasicPDUBody::fromASN1($children);
        return new self($body);
    }

    public function toBinary(): string
    {
        $asn1 = $this->toASN1();
        return $asn1->toDER();
    }

    /**
     * @param string $binary
     * @return self
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): self
    {
        try {
            $tagged = Element::fromDER($binary)->asUnspecified()->asTagged();
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError('Cant decode PDU: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($tagged);
    }

}
