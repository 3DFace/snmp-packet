<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\UInteger32;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class UInteger32Test extends TestCase
{

    private const example = '470101';

    public function testEncoded()
    {
        $data = new UInteger32(1);
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = UInteger32::fromBinary(hex2bin(self::example));
        $data = new UInteger32(1);
        $this->assertTrue($decoded->equals($data));
    }

}
