<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\Primitive\OctetString;
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

    /**
     * @throws DecodeError
     */
    public function testNonUniversalFails()
    {
        $this->expectException(DecodeError::class);
        Integer32::fromBinary(hex2bin('430101'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonIntegerAsn1Fails()
    {
        $this->expectException(DecodeError::class);
        Integer32::fromASN1((new OctetString('asd'))->asUnspecified());
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails()
    {
        $this->expectException(DecodeError::class);
        Integer32::fromBinary(hex2bin('81'));
    }

    public function testLowLimit()
    {
        new Integer32(Integer32::MIN);
        $this->expectException(\InvalidArgumentException::class);
        new Integer32(Integer32::MIN - 1);
    }

    public function testHighLimit()
    {
        new Integer32(Integer32::MAX);
        $this->expectException(\InvalidArgumentException::class);
        new Integer32(Integer32::MAX + 1);
    }

    public function testGetValue(){
        $i = new Integer32(123);
        $this->assertEquals(123, $i->getValue());
    }

}
