<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\DataType\OctetString;
use dface\SnmpPacket\DataType\Opaque;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class OpaqueTest extends TestCase
{

    private const example = '440131';

    public function testEncoded()
    {
        $data = new Opaque('1');
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = Opaque::fromBinary(hex2bin(self::example));
        $data = new Opaque('1');
        $this->assertTrue($decoded->equals($data));
    }

    /**
     * @throws DecodeError
     */
    public function testNonApplicationFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Opaque::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidTagFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Opaque::fromASN1(UnspecifiedType::fromDER(hex2bin('430101'))->asApplication());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Opaque::fromBinary(hex2bin('81'));
    }

    public function testGetValue()
    {
        $i = new Opaque('asd');
        $this->assertEquals('asd', $i->getValue());
    }

    public function testEquals()
    {
        $x1 = new Opaque('asd');
        $x2 = new Opaque('asd');
        $this->assertTrue($x1->equals($x2));
    }

    public function testToString()
    {
        $bin = hex2bin('a0a0');
        $x1 = new Opaque($bin);
        $this->assertEquals('a0a0', (string)$x1);
    }

    public function testNotEquals()
    {
        $x1 = new Opaque('asd');
        $this->assertFalse($x1->equals(new Opaque('zxc')));
        $this->assertFalse($x1->equals(new OctetString('asd')));
    }

}
