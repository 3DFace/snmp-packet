<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\Primitive\Integer;
use dface\SnmpPacket\DataType\BitString;
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
        BitString::fromBinary(hex2bin('410101'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadASN1Fails()
    {
        $this->expectException(DecodeError::class);
        BitString::fromBinary(hex2bin('05'));
    }

    /**
     * @throws DecodeError
     */
    public function testNotBitStringBinFails()
    {
        $this->expectException(DecodeError::class);
        BitString::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testNotBitStringAsn1Fails()
    {
        $this->expectException(DecodeError::class);
        BitString::fromASN1((new Integer(0))->asUnspecified());
    }

    public function testGetters()
    {
        $bits = hex2bin('a0a0');
        $data = new BitString($bits, 3);
        $this->assertEquals($bits, $data->getValue());
        $this->assertEquals(3, $data->getUnusedBits());
    }

}
