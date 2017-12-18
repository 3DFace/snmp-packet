<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\Counter32;
use dface\SnmpPacket\DataType\TimeTicks;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class AbstractUnsigned32Test extends TestCase
{

    public function testLowLimit()
    {
        new Counter32(Counter32::MIN);
        $this->expectException(\InvalidArgumentException::class);
        new Counter32(Counter32::MIN - 1);
    }

    public function testHighLimit()
    {
        new Counter32(Counter32::MAX);
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
        $this->expectExceptionCode(0);
        Counter32::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidTagFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Counter32::fromBinary(hex2bin('430101'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Counter32::fromBinary(hex2bin('81'));
    }

    public function testEquals()
    {
        $x1 = new Counter32(123);
        $x2 = new Counter32(123);
        $this->assertTrue($x1->equals($x2));
    }

    public function testNotEquals()
    {
        $x1 = new Counter32(123);
        $this->assertFalse($x1->equals(new TimeTicks(123)));
        $this->assertFalse($x1->equals(new Counter32(321)));
        $this->assertFalse($x1->equals(new TimeTicks(321)));
    }

}
