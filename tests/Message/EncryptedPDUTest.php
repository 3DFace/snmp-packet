<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;


use dface\SnmpPacket\DataType\OctetString;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class EncryptedPDUTest extends TestCase
{

    public function testEncoded()
    {
        $encrypted_pdu = new EncryptedPDU('');
        $bin = $encrypted_pdu->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals('0400', $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = EncryptedPDU::fromBinary(hex2bin('0400'));
        $encrypted_pdu = new EncryptedPDU('');
        $this->assertTrue($decoded->equals($encrypted_pdu));
    }

    /**
     * @throws DecodeError
     */
    public function testBadASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        EncryptedPDU::fromBinary(hex2bin('05'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonOctetStringFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        EncryptedPDU::fromBinary(hex2bin('0500'));
    }

    public function testGetters()
    {
        $x = new EncryptedPDU('asd');
        $this->assertEquals('asd', $x->getContent());
    }

    public function testEquals()
    {
        $x1 = new EncryptedPDU('asd');
        $x2 = new EncryptedPDU('asd');
        $this->assertTrue($x1->equals($x2));
    }

    public function testNotEquals()
    {
        $x1 = new EncryptedPDU('asd');
        $this->assertFalse($x1->equals(new EncryptedPDU('zxc')));
        $this->assertFalse($x1->equals(new OctetString('asd')));
    }

}
