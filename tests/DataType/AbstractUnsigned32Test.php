<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\Counter32;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class AbstractUnsigned32Test extends TestCase
{

    public function testNegativeFails()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Counter32(-1);
    }

    public function testOverflowFails()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Counter32(Counter32::MAX + 1);
    }

    public function testGetValue()
    {
        $c = new Counter32(123);
        $this->assertEquals(123, $c->getValue());
    }

    /**
     * @throws DecodeError
     */
    public function testNonApplicationFails()
    {
        $this->expectException(DecodeError::class);
        Counter32::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidTagFails()
    {
        $this->expectException(DecodeError::class);
        Counter32::fromBinary(hex2bin('430101'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        Counter32::fromBinary(hex2bin('81'));
    }

}
