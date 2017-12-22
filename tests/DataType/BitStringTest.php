<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\Primitive\Integer;
use dface\SnmpPacket\DataType\BitString;
use dface\SnmpPacket\DataType\Counter32;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class BitStringTest extends TestCase
{

    private const example = '030303a0a0';

    public function testEncoded()
    {
        $data = new BitString(hex2bin('a0a0'), 3);
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = BitString::fromBinary(hex2bin(self::example));
        $data = new BitString(hex2bin('a0a0'), 3);
        $this->assertTrue($decoded->equals($data));
    }

    /**
     * @throws DecodeError
     */
    public function testNonUniversalBinFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        BitString::fromBinary(hex2bin('410101'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        BitString::fromBinary(hex2bin('05'));
    }

    /**
     * @throws DecodeError
     */
    public function testNotBitStringBinFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        BitString::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testNotBitStringAsn1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        BitString::fromASN1((new Integer(0))->asUnspecified());
    }

    public function testGetters()
    {
        $bits = hex2bin('a0a0');
        $data = new BitString($bits, 3);
        $this->assertEquals($bits, $data->getValue());
        $this->assertEquals(3, $data->getUnusedBits());
    }

    public function testEquals()
    {
        $x1 = new BitString('asd', 0);
        $x2 = new BitString('asd', 0);
        $this->assertTrue($x1->equals($x2));
    }

    public function testToString()
    {
        $x1 = new BitString(hex2bin('a0a0'), 1);
        $this->assertEquals('a0a0/1', (string)$x1);
    }

    public function testNotEquals()
    {
        $x1 = new BitString('asd', 0);
        $this->assertFalse($x1->equals(new BitString('asd', 1)));
        $this->assertFalse($x1->equals(new BitString('zxc', 0)));
        $this->assertFalse($x1->equals(new BitString('zxc', 1)));
        $this->assertFalse($x1->equals(new Counter32(321)));
    }

}
