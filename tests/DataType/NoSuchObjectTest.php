<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\NoSuchObject;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class NoSuchObjectTest extends TestCase
{

    private const example = '8000';

    public function testEncoded()
    {
        $data = new NoSuchObject();
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = NoSuchObject::fromBinary(hex2bin(self::example));
        $data = new NoSuchObject();
        $this->assertTrue($decoded->equals($data));
    }

}
