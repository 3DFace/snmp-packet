<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\BitString;
use dface\SnmpPacket\DataType\Counter32;
use dface\SnmpPacket\DataType\Counter64;
use dface\SnmpPacket\DataType\DataTypeDecoder;
use dface\SnmpPacket\DataType\EndOfMibView;
use dface\SnmpPacket\DataType\Gauge32;
use dface\SnmpPacket\DataType\Integer32;
use dface\SnmpPacket\DataType\IpAddress;
use dface\SnmpPacket\DataType\NoSuchInstance;
use dface\SnmpPacket\DataType\NoSuchObject;
use dface\SnmpPacket\DataType\NsapAddress;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\OctetString;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\DataType\Opaque;
use dface\SnmpPacket\DataType\TimeTicks;
use dface\SnmpPacket\DataType\UInteger32;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class DataTypeDecoderTest extends TestCase
{

    /**
     * @throws DecodeError
     */
    public function testIpAddressDetected()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('40040a007260'));
        $this->assertInstanceOf(IpAddress::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testNsapAddressDetected()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('45023131'));
        $this->assertInstanceOf(NsapAddress::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testCounter32Decoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('410101'));
        $this->assertInstanceOf(Counter32::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testGauge32Decoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('420101'));
        $this->assertInstanceOf(Gauge32::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testTimeTicksDecoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('430101'));
        $this->assertInstanceOf(TimeTicks::class, $x);
    }


    /**
     * @throws DecodeError
     */
    public function testOpaqueDecoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('440131'));
        $this->assertInstanceOf(Opaque::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testCounter64Decoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('460101'));
        $this->assertInstanceOf(Counter64::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testUInteger32Decoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('470101'));
        $this->assertInstanceOf(UInteger32::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testUnknownAppTagFailed()
    {
        $this->expectException(DecodeError::class);
        DataTypeDecoder::fromBinary(hex2bin('490101'));
    }

    /**
     * @throws DecodeError
     */
    public function testIntegerDecoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('020101'));
        $this->assertInstanceOf(Integer32::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testOctetStringDecoded()
    {
        $ip = DataTypeDecoder::fromBinary(hex2bin('040131'));
        $this->assertInstanceOf(OctetString::class, $ip);
    }

    /**
     * @throws DecodeError
     */
    public function testBitStringDecoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('030303a0a0'));
        $this->assertInstanceOf(BitString::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testOidDecoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('06062b0601020104'));
        $this->assertInstanceOf(Oid::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testNullDecoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('0500'));
        $this->assertInstanceOf(NullValue::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testUnsupportedUniversalFails()
    {
        $this->expectException(DecodeError::class);
        DataTypeDecoder::fromBinary(hex2bin('0900'));
    }

    /**
     * @throws DecodeError
     */
    public function testNoSuchObjectDecoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('8000'));
        $this->assertInstanceOf(NoSuchObject::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testNoSuchInstanceDecoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('8100'));
        $this->assertInstanceOf(NoSuchInstance::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testEndOfMibViewDecoded()
    {
        $x = DataTypeDecoder::fromBinary(hex2bin('8200'));
        $this->assertInstanceOf(EndOfMibView::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testUnsupportedContextFails()
    {
        $this->expectException(DecodeError::class);
        DataTypeDecoder::fromBinary(hex2bin('8300'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadASN1Fails()
    {
        $this->expectException(DecodeError::class);
        DataTypeDecoder::fromBinary(hex2bin('00'));
    }

    /**
     * @throws DecodeError
     */
    public function testASN1PrivateFails()
    {
        $this->expectException(DecodeError::class);
        // for now it fails cause asn1 lib does not implement `private` class support
        DataTypeDecoder::fromBinary(hex2bin('c000'));
    }

}
