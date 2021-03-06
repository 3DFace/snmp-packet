<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class NullTest extends TestCase
{

    private const example = '0500';

    public function testEncoded()
    {
        $data = new NullValue();
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = NullValue::fromBinary(hex2bin(self::example));
        $data = new NullValue();
        $this->assertTrue($decoded->equals($data));
    }

    /**
     * @throws DecodeError
     */
    public function testNonUniversalFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        NullValue::fromBinary(hex2bin('430101'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonNullAsn1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        NullValue::fromASN1((new OctetString('asd'))->asUnspecified());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        NullValue::fromBinary(hex2bin('81'));
    }

    public function testEquals(){
        $x1 = new NullValue();
        $x2 = new NullValue();
        $this->assertTrue($x1->equals($x2));
    }

    public function testToString(){
        $x1 = new NullValue();
        $this->assertEquals('', (string)$x1);
    }

}
