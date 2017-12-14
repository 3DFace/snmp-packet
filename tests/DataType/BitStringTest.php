<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

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

}
