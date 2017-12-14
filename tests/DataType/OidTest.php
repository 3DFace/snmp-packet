<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class OidTest extends TestCase
{

    private const example = '06062b0601020104';

    public function testEncoded()
    {
        $data = new Oid('1.3.6.1.2.1.4');
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = Oid::fromBinary(hex2bin(self::example));
        $data = new Oid('1.3.6.1.2.1.4');
        $this->assertTrue($decoded->equals($data));
    }

}
