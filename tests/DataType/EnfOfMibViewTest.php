<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\EndOfMibView;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class EnfOfMibViewTest extends TestCase
{

    private const example = '8200';

    public function testEncoded()
    {
        $data = new EndOfMibView();
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = EndOfMibView::fromBinary(hex2bin(self::example));
        $data = new EndOfMibView();
        $this->assertTrue($decoded->equals($data));
    }

}
