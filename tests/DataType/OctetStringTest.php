<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\OctetString;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class OctetStringTest extends TestCase
{

    private const example = '040131';

    public function testEncoded()
    {
        $data = new OctetString('1');
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = OctetString::fromBinary(hex2bin(self::example));
        $data = new OctetString('1');
        $this->assertTrue($decoded->equals($data));
    }

}
