<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\NsapAddress;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class NsapAddressTest extends TestCase
{

    private const example = '45023131';

    public function testEncoded()
    {
        $data = new NsapAddress('11');
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = NsapAddress::fromBinary(hex2bin(self::example));
        $data = new NsapAddress('11');
        $this->assertTrue($decoded->equals($data));
    }

}
