<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\OctetString;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\PDU\PDU;
use dface\SnmpPacket\PDU\PDUDecoder;

class ScopedPDU implements ScopedPDUData
{

    /** @var string */
    private $context_engine_id;
    /** @var string */
    private $context_name;
    /** @var PDU */
    private $data;

    /**
     * @param string $context_engine_id
     * @param string $context_name
     * @param PDU $data
     */
    public function __construct(string $context_engine_id, string $context_name, PDU $data)
    {
        $this->context_engine_id = $context_engine_id;
        $this->context_name = $context_name;
        $this->data = $data;
    }

    public function getContextEngineId(): string
    {
        return $this->context_engine_id;
    }

    public function getContextName(): string
    {
        return $this->context_name;
    }

    public function getData(): PDU
    {
        return $this->data;
    }

    public function equals($x): bool
    {
        return $x instanceof self
            && $x->context_engine_id === $this->context_engine_id
            && $x->context_name === $this->context_name
            && $x->data->equals($this->data);
    }

    public function toBinary(): string
    {
        $asn1 = $this->toASN1();
        return $asn1->toDER();
    }

    public function toASN1(): Element
    {
        return new Sequence(
            new OctetString($this->context_engine_id),
            new OctetString($this->context_name),
            $this->data->toASN1()
        );
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
            throw new DecodeError('Cant decode HeaderData: ' . $e->getMessage(), 0, $e);
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
            $seq = $element->asSequence();
            $context_engine_id = $seq->at(0)->asOctetString()->string();
            $context_name = $seq->at(1)->asOctetString()->string();
            $data_obj = $seq->at(2)->asTagged();
            $pdu = PDUDecoder::fromASN1($data_obj);
        } catch (\UnexpectedValueException|\OutOfBoundsException $e) {
            throw new DecodeError('Cant decode EncryptedPDU: ' . $e->getMessage(), 0, $e);
        }
        return new self($context_engine_id, $context_name, $pdu);
    }

}
