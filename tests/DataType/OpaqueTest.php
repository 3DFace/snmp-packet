<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

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

}
