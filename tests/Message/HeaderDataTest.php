<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace Message;

use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\Message\HeaderData;
use PHPUnit\Framework\TestCase;

class HeaderDataTest extends TestCase
{

    private const example = '300d020101020201f4040100020101';

    public function testEncoded()
    {
        $data = new HeaderData(1, 500, 0, 1);
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = HeaderData::fromBinary(hex2bin(self::example));
        $data = new HeaderData(1, 500, 0, 1);
        $this->assertTrue($decoded->equals($data));
    }

}
