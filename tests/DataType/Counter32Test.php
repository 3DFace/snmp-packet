<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\Counter32;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class Counter32Test extends TestCase
{

    private const example = '410101';

    public function testEncoded()
    {
        $data = new Counter32(1);
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = Counter32::fromBinary(hex2bin(self::example));
        $data = new Counter32(1);
        $this->assertTrue($decoded->equals($data));
    }

}
