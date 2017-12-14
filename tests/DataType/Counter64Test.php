<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\Counter64;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class Counter64Test extends TestCase
{

    private const example = '460101';

    public function testEncoded()
    {
        $data = new Counter64(1);
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = Counter64::fromBinary(hex2bin(self::example));
        $data = new Counter64(1);
        $this->assertTrue($decoded->equals($data));
    }

}
