<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\Integer32;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class Integer32Test extends TestCase
{

    private const example = '020101';

    public function testEncoded()
    {
        $data = new Integer32(1);
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = Integer32::fromBinary(hex2bin(self::example));
        $data = new Integer32(1);
        $this->assertTrue($decoded->equals($data));
    }

}
