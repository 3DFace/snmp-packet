<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Component\Identifier;
use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class DataTypeDecoder
{

    /**
     * @param string $binary
     * @return DataType
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): DataType
    {
        try {
            $tagged = UnspecifiedType::fromDER($binary);
        } catch (DecodeException|\UnexpectedValueException $e) {
            throw new DecodeError('Cant decode value binary: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($tagged);
    }

    /**
     * @param UnspecifiedType $element
     * @return DataType
     * @throws DecodeError
     */
    public static function fromASN1(UnspecifiedType $element): DataType
    {
        $class = $element->typeClass();
        $tag = $element->tag();

        switch ($class) {

            case Identifier::CLASS_APPLICATION:
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                // should not throw, as we already checked that element is of `application` class
                $app = $element->asApplication();
                switch ($tag) {
                    case IpAddress::TAG:
                        return IpAddress::fromASN1($app);
                    case Counter32::TAG:
                        return Counter32::fromASN1($app);
                    case Gauge32::TAG:
                        return Gauge32::fromASN1($app);
                    case TimeTicks::TAG:
                        return TimeTicks::fromASN1($app);
                    case Opaque::TAG:
                        return Opaque::fromASN1($app);
                    case NsapAddress::TAG:
                        return NsapAddress::fromASN1($app);
                    case Counter64::TAG:
                        return Counter64::fromASN1($app);
                    case UInteger32::TAG:
                        return UInteger32::fromASN1($app);
                    default:
                        throw new DecodeError("Unknown application data type, tag=$tag");
                }

            case Identifier::CLASS_UNIVERSAL:
                switch ($tag) {
                    case Element::TYPE_INTEGER:
                        return Integer32::fromASN1($element);
                    case Element::TYPE_OCTET_STRING:
                        return OctetString::fromASN1($element);
                    case Element::TYPE_BIT_STRING:
                        return BitString::fromASN1($element);
                    case Element::TYPE_OBJECT_IDENTIFIER:
                        return Oid::fromASN1($element);
                    case Element::TYPE_NULL:
                        return new NullValue();
                    default:
                        $type_name = Element::tagToName($tag);
                        throw new DecodeError("SNMP does not support ASN1 Universal type '$type_name'");
                }

            case Identifier::CLASS_CONTEXT_SPECIFIC:
                switch ($tag) {
                    case NoSuchObject::TAG:
                        return new NoSuchObject();
                    case NoSuchInstance::TAG:
                        return new NoSuchInstance();
                    case EndOfMibView::TAG:
                        return new EndOfMibView();
                    default:
                        throw new DecodeError("Unknown context specific data type, tag=$tag");
                }
            default:
                $class_name = Identifier::classToName($class);
                throw new DecodeError("SNMP does not support ASN1 class '$class_name'");
        }

    }

}
