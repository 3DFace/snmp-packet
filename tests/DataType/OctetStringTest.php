<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\Primitive\Integer;
use dface\SnmpPacket\DataType\IpAddress;
use dface\SnmpPacket\DataType\OctetString;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class OctetStringTest extends TestCase
{

    private const example = '040131';

    public function testEncoded()
    {
        $data = new OctetString('1');
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = OctetString::fromBinary(hex2bin(self::example));
        $data = new OctetString('1');
        $this->assertTrue($decoded->equals($data));
    }

    /**
     * @throws DecodeError
     */
    public function testNonUniversalFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        OctetString::fromBinary(hex2bin('430101'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonOctetStringAsn1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        OctetString::fromASN1((new Integer(1))->asUnspecified());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        OctetString::fromBinary(hex2bin('81'));
    }

    public function testGetValue()
    {
        $i = new OctetString('asd');
        $this->assertEquals('asd', $i->getValue());
    }

    public function testEquals()
    {
        $x1 = new OctetString('asd');
        $x2 = new OctetString('asd');
        $this->assertTrue($x1->equals($x2));
    }

    public function testNotEquals()
    {
        $x1 = new OctetString('10.10.10.10');
        $this->assertFalse($x1->equals(new OctetString('asd')));
        $this->assertFalse($x1->equals(new IpAddress('10.10.10.10')));
    }

}
