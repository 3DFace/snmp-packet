<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;


use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use dface\SnmpPacket\Exception\DecodeError;

abstract class MessageDecoder
{

    /**
     * @param string $binary
     * @return Message
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): Message
    {
        try {
            /** @var Sequence $seq */
            $seq = Sequence::fromDER($binary);
            return self::fromASN1($seq);
        } catch (DecodeException | \UnexpectedValueException $e) {
            throw new DecodeError('Cant decode snmp message: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param Sequence $sequence
     * @return Message
     * @throws DecodeError
     */
    public static function fromASN1(Sequence $sequence): Message
    {
        try {
            $version = $sequence->at(0)->asInteger()->intNumber();
        } catch (\UnexpectedValueException|\OutOfBoundsException $e) {
            throw new DecodeError('Message must be an ASN1 sequence with version number in the beginning');
        }
        switch ($version) {
            case Message::V1:
            case Message::V2C:
                return MessageV1::fromASN1($sequence);
            case Message::V3:
                return MessageV3::fromASN1($sequence);
            default:
                throw new DecodeError('Unsupported message version: ' . $version);
        }
    }

}
