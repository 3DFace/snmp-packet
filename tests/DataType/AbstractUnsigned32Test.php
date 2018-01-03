<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Component\Identifier;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Tagged\ApplicationType;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
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

    /**
     * @throws DecodeError
     */
    public function testLowLimitDecode()
    {
        /** @var ApplicationType $x1 */
        $x1 = ApplicationType::fromDER(
            (new ImplicitlyTaggedType(
                Counter32::TAG,
                new Integer(Counter32::MIN),
                Identifier::CLASS_APPLICATION)
            )->toDER()
        );
        Counter32::fromASN1($x1);

        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        /** @var ApplicationType $x2 */
        $x2 = ApplicationType::fromDER(
            (new ImplicitlyTaggedType(
                Counter32::TAG,
                new Integer(Counter32::MIN - 1),
                Identifier::CLASS_APPLICATION)
            )->toDER()
        );
        Counter32::fromASN1($x2);
    }

    /**
     * @throws DecodeError
     */
    public function testHighLimitDecode()
    {
        /** @var ApplicationType $x1 */
        $x1 = ApplicationType::fromDER(
            (new ImplicitlyTaggedType(
                Counter32::TAG,
                new Integer(Counter32::MAX),
                Identifier::CLASS_APPLICATION)
            )->toDER()
        );
        Counter32::fromASN1($x1);

        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        /** @var ApplicationType $x2 */
        $x2 = ApplicationType::fromDER(
            (new ImplicitlyTaggedType(
                Counter32::TAG,
                new Integer(Counter32::MAX + 1),
                Identifier::CLASS_APPLICATION)
            )->toDER()
        );
        Counter32::fromASN1($x2);
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

    public function testToString()
    {
        $x1 = new Counter32(123);
        $this->assertEquals('123', (string)$x1);
    }

    public function testNotEquals()
    {
        $x1 = new Counter32(123);
        $this->assertFalse($x1->equals(new TimeTicks(123)));
        $this->assertFalse($x1->equals(new Counter32(321)));
        $this->assertFalse($x1->equals(new TimeTicks(321)));
    }

}
