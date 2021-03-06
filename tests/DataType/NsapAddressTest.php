<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\DataType\NsapAddress;
use dface\SnmpPacket\DataType\OctetString;
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

    /**
     * @throws DecodeError
     */
    public function testNonApplicationFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        NsapAddress::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidTagFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        NsapAddress::fromASN1(UnspecifiedType::fromDER(hex2bin('430101'))->asApplication());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        NsapAddress::fromBinary(hex2bin('81'));
    }

    public function testGetValue()
    {
        $i = new NsapAddress('wat?');
        $this->assertEquals('wat?', $i->getValue());
    }


    public function testEquals()
    {
        $x1 = new NsapAddress('wat?');
        $x2 = new NsapAddress('wat?');
        $this->assertTrue($x1->equals($x2));
    }

    public function testToString()
    {
        $x1 = new NsapAddress('wat?');
        $this->assertEquals('wat?', (string)$x1);
    }

    public function testNotEquals()
    {
        $x1 = new NsapAddress('wat?');
        $this->assertFalse($x1->equals(new NsapAddress('wat!')));
        $this->assertFalse($x1->equals(new OctetString('wat?')));
    }

}
