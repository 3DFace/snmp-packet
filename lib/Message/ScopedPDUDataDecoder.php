<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;


use ASN1\Component\Identifier;
use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

abstract class ScopedPDUDataDecoder
{

    /**
     * @param string $binary
     * @return ScopedPDUData
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): ScopedPDUData
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
     * @return ScopedPDUData
     * @throws DecodeError
     */
    public static function fromASN1(UnspecifiedType $element): ScopedPDUData
    {
        if ($element->isType(Element::TYPE_OCTET_STRING)) {
            return EncryptedPDU::fromASN1($element);
        }

        if ($element->isType(Element::TYPE_SEQUENCE)) {
            return ScopedPDU::fromASN1($element);
        }

        $class_name = Identifier::classToName($element->typeClass());
        $tag_name = Element::tagToName($element->tag());
        throw new DecodeError("Unknown ScopedPDUData format: class=$class_name, tag=$tag_name");
    }

}
