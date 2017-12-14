<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\TimeTicks;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class TimeTicksTest extends TestCase
{

    private const example = '430101';

    public function testEncoded()
    {
        $data = new TimeTicks(1);
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = TimeTicks::fromBinary(hex2bin(self::example));
        $data = new TimeTicks(1);
        $this->assertTrue($decoded->equals($data));
    }

}
