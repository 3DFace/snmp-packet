<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\PDU\PDU;
use dface\SnmpPacket\PDU\PDUDecoder;

class MessageV1 implements Message
{

    /** @var int */
    private $version;
    /** @var string */
    private $community;
    /** @var PDU */
    private $pdu;

    public function __construct(int $version, string $community, PDU $pdu)
    {
        $this->version = $version;
        $this->community = $community;
        $this->pdu = $pdu;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getCommunity(): string
    {
        return $this->community;
    }

    public function getPdu(): PDU
    {
        return $this->pdu;
    }

    public function equals($message): bool
    {
        return $message instanceof self
            && $message->version === $this->version
            && $message->community === $this->community
            && $message->pdu->equals($this->pdu);
    }

    public function toASN1(): Element
    {
        return new Sequence(
            new Integer($this->version),
            new OctetString($this->community),
            $this->pdu->toASN1()
        );
    }

    public function toBinary(): string
    {
        $obj = $this->toASN1();
        return $obj->toDER();
    }

    /**
     * @param string $binary
     * @return MessageV1
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): self
    {
        try {
            $seq = UnspecifiedType::fromDER($binary)->asSequence();
            return self::fromASN1($seq);
        } catch (DecodeException | \UnexpectedValueException $e) {
            throw new DecodeError('Cant decode snmp message: ' . $e->getMessage(), 1, $e);
        }
    }

    /**
     * @param Sequence $seq
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(Sequence $seq): self
    {
        try {

            try {
                $ver = $seq->at(0)->asInteger()->intNumber();
                $community = $seq->at(1)->asOctetString()->string();
                $pdu_obj = $seq->at(2)->asTagged();
            } catch (\OutOfBoundsException $e) {
                throw new DecodeError('MessageV1 must be a sequence of [version, community, pdu]');
            }

            $pdu = PDUDecoder::fromASN1($pdu_obj);
            return new self($ver, $community, $pdu);

        } catch (DecodeException | \UnexpectedValueException $e) {
            throw new DecodeError('Cant decode MessageV1: ' . $e->getMessage(), 1, $e);
        }
    }

}
