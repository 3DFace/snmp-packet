<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\UnspecifiedType;
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
        Opaque::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidTagFails()
    {
        $this->expectException(DecodeError::class);
        Opaque::fromASN1(UnspecifiedType::fromDER(hex2bin('430101'))->asApplication());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        Opaque::fromBinary(hex2bin('81'));
    }

    public function testGetValue()
    {
        $i = new Opaque('asd');
        $this->assertEquals('asd', $i->getValue());
    }

}
