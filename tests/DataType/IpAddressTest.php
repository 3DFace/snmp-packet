<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\DataType\IpAddress;
use dface\SnmpPacket\DataType\OctetString;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class IpAddressTest extends TestCase
{

    private const example = '40040a007260';

    public function testEncoded()
    {
        $data = new IpAddress('10.0.114.96');
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = IpAddress::fromBinary(hex2bin(self::example));
        $data = new IpAddress('10.0.114.96');
        $this->assertTrue($decoded->equals($data));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidOctetLengthFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        IpAddress::fromBinary(hex2bin('40030a0072'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonApplicationFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        IpAddress::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidTagFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        IpAddress::fromASN1(UnspecifiedType::fromDER(hex2bin('430101'))->asApplication());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        IpAddress::fromBinary(hex2bin('81'));
    }

    public function testBadIpFails()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        new IpAddress('10.10.10.10.10');
    }

    public function testGetValue()
    {
        $i = new IpAddress('10.10.10.10');
        $this->assertEquals('10.10.10.10', $i->getValue());
    }

    public function testEquals()
    {
        $x1 = new IpAddress('10.10.10.10');
        $x2 = new IpAddress('10.10.10.10');
        $this->assertTrue($x1->equals($x2));
    }

    public function testToString()
    {
        $x1 = new IpAddress('10.10.10.10');
        $this->assertEquals('10.10.10.10', (string)$x1);
    }

    public function testNotEquals()
    {
        $x1 = new IpAddress('10.10.10.10');
        $this->assertFalse($x1->equals(new IpAddress('10.10.10.20')));
        $this->assertFalse($x1->equals(new OctetString('10.10.10.10')));
    }

}
