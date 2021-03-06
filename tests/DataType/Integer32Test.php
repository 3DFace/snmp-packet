<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\DataType\Integer32;
use dface\SnmpPacket\DataType\TimeTicks;
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

    /**
     * @throws DecodeError
     */
    public function testNonUniversalFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Integer32::fromBinary(hex2bin('430101'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonIntegerAsn1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Integer32::fromASN1((new OctetString('asd'))->asUnspecified());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Integer32::fromBinary(hex2bin('81'));
    }

    public function testLowLimit()
    {
        new Integer32(Integer32::MIN);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        new Integer32(Integer32::MIN - 1);
    }

    public function testHighLimit()
    {
        new Integer32(Integer32::MAX);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        new Integer32(Integer32::MAX + 1);
    }

    /**
     * @throws DecodeError
     */
    public function testLowLimitDecode()
    {
        Integer32::fromASN1((new Integer(Integer32::MIN))->asUnspecified());
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Integer32::fromASN1((new Integer(Integer32::MIN - 1))->asUnspecified());
    }

    /**
     * @throws DecodeError
     */
    public function testHighLimitDecode()
    {
        Integer32::fromASN1((new Integer(Integer32::MAX))->asUnspecified());
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Integer32::fromASN1((new Integer(Integer32::MAX + 1))->asUnspecified());
    }

    public function testGetValue()
    {
        $i = new Integer32(123);
        $this->assertEquals(123, $i->getValue());
    }

    public function testEquals()
    {
        $x1 = new Integer32(123);
        $x2 = new Integer32(123);
        $this->assertTrue($x1->equals($x2));
    }

    public function testToString()
    {
        $x1 = new Integer32(123);
        $this->assertEquals('123', (string)$x1);
    }

    public function testNotEquals()
    {
        $x1 = new Integer32(123);
        $this->assertFalse($x1->equals(new Integer32(321)));
        $this->assertFalse($x1->equals(new TimeTicks(123)));
        $this->assertFalse($x1->equals(new TimeTicks(321)));
    }

}
