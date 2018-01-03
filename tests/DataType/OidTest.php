<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\Primitive\ObjectIdentifier;
use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class OidTest extends TestCase
{

    private const example = '06062b0601020104';

    public function testEncoded()
    {
        $data = new Oid('1.3.6.1.2.1.4');
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = Oid::fromBinary(hex2bin(self::example));
        $data = new Oid('1.3.6.1.2.1.4');
        $this->assertTrue($decoded->equals($data));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidOctetLengthFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Oid::fromBinary(hex2bin('4000'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonUniversalFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Oid::fromBinary(hex2bin('430101'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonOidAsn1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Oid::fromASN1((new OctetString('1.3.6.1.2.1.4'))->asUnspecified());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidSnmpOidAsn1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Oid::fromASN1((new ObjectIdentifier('0.3'))->asUnspecified());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        Oid::fromBinary(hex2bin('81'));
    }

    public function testBadOidFails1()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        new Oid('2');
    }

    public function testBadOidFails2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        new Oid('1.2');
    }

    public function testAllowedPrefixesPass()
    {
        $this->assertEquals('', new Oid(''));
        $this->assertEquals('1', new Oid('1'));
        $this->assertEquals('1.3', new Oid('1.3'));
    }

    public function testGetValue()
    {
        $i = new Oid('1.3.6.1.2.1.4');
        $this->assertEquals('1.3.6.1.2.1.4', $i->getValue());
    }

    public function testEquals()
    {
        $x1 = new Oid('1.3.6.1.2.1.4');
        $x2 = new Oid('1.3.6.1.2.1.4');
        $this->assertTrue($x1->equals($x2));
    }

    public function testToString()
    {
        $x1 = new Oid('1.3.6.1.2.1.4');
        $this->assertEquals('1.3.6.1.2.1.4', (string)$x1);
    }

    public function testNotEquals()
    {
        $x1 = new Oid('1.3.6.1.2.1.4');
        $this->assertFalse($x1->equals(new Oid('1.3.6.1.2.1.5')));
        $this->assertFalse($x1->equals(new OctetString('1.3.6.1.2.1.4')));
    }

}
