<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\NoSuchInstance;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class NoSuchInstanceTest extends TestCase
{

    private const example = '8100';

    public function testEncoded()
    {
        $data = new NoSuchInstance();
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = NoSuchInstance::fromBinary(hex2bin(self::example));
        $data = new NoSuchInstance();
        $this->assertTrue($decoded->equals($data));
    }

}
