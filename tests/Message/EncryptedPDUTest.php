<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;


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
        EncryptedPDU::fromBinary(hex2bin('05'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonOctetStringFails()
    {
        $this->expectException(DecodeError::class);
        EncryptedPDU::fromBinary(hex2bin('0500'));
    }

    public function testGetters()
    {
        $x = new EncryptedPDU('asd');
        $this->assertEquals('asd', $x->getContent());
    }

}
