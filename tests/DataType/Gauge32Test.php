<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\Gauge32;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class Gauge32Test extends TestCase
{

    private const example = '420101';

    public function testEncoded()
    {
        $data = new Gauge32(1);
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = Gauge32::fromBinary(hex2bin(self::example));
        $data = new Gauge32(1);
        $this->assertTrue($decoded->equals($data));
    }

}
