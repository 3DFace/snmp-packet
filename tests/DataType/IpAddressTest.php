<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\IpAddress;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class IpAddressTest extends TestCase
{

    private const example = '40040a007260';

    public function testEncoded()
    {
        $data = new IpAddress('10.0.114.96');
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = IpAddress::fromBinary(hex2bin(self::example));
        $data = new IpAddress('10.0.114.96');
        $this->assertTrue($decoded->equals($data));
    }

}
