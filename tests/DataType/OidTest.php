<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

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
        Oid::fromBinary(hex2bin('4000'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonUniversalFails()
    {
        $this->expectException(DecodeError::class);
        Oid::fromBinary(hex2bin('430101'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonOidAsn1Fails()
    {
        $this->expectException(DecodeError::class);
        Oid::fromASN1((new OctetString('asd'))->asUnspecified());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        Oid::fromBinary(hex2bin('81'));
    }

    public function testBadOidFails()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Oid('10');
    }

    public function testGetValue()
    {
        $i = new Oid('1.3.6.1.2.1.4');
        $this->assertEquals('1.3.6.1.2.1.4', $i->getValue());
    }

}
