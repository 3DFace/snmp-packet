<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

use ASN1\Exception\DecodeException;
use ASN1\Type\TaggedType;
use dface\SnmpPacket\Exception\DecodeError;

class PDUDecoder
{

    /**
     * @param string $binary
     * @return PDU
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): PDU
    {
        try {
            /** @var TaggedType $tagged */
            $tagged = TaggedType::fromDER($binary);
        } catch (DecodeException|\UnexpectedValueException $e) {
            throw new DecodeError('Cant decode PDU binary: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($tagged);
    }

    /**
     * @param TaggedType $pdu_obj
     * @return PDU
     * @throws DecodeError
     */
    public static function fromASN1(TaggedType $pdu_obj): PDU
    {
        $pdu_tag = $pdu_obj->tag();
        switch ($pdu_tag) {
            case GetRequestPDU::TAG:
                return GetRequestPDU::fromASN1($pdu_obj);
            case GetNextRequestPDU::TAG:
                return GetNextRequestPDU::fromASN1($pdu_obj);
            case ResponsePDU::TAG:
                return ResponsePDU::fromASN1($pdu_obj);
            case SetRequestPDU::TAG:
                return SetRequestPDU::fromASN1($pdu_obj);
            case TrapPDU::TAG:
                return TrapPDU::fromASN1($pdu_obj);
            case GetBulkRequestPDU::TAG:
                return GetBulkRequestPDU::fromASN1($pdu_obj);
            case InformRequestPDU::TAG:
                return InformRequestPDU::fromASN1($pdu_obj);
            case ReportPDU::TAG:
                return ReportPDU::fromASN1($pdu_obj);
            case TrapV2PDU::TAG:
                return TrapV2PDU::fromASN1($pdu_obj);
            default:
                throw new DecodeError('Unknown pdu tag: ' . $pdu_tag);
        }
    }

}
