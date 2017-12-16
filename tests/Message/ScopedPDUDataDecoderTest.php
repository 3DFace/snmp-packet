<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;


use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class ScopedPDUDataDecoderTest extends TestCase
{

    /**
     * @throws DecodeError
     */
    public function testScopedPDUDecoded()
    {
        $x = ScopedPDUDataDecoder::fromBinary(hex2bin('3028040361736404037a7863a01c02041ae944c8020100020100300e300c06082b060102010103000500'));
        $this->assertInstanceOf(ScopedPDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testEncryptedPDUDecoded()
    {
        $x = ScopedPDUDataDecoder::fromBinary(hex2bin('0400'));
        $this->assertInstanceOf(EncryptedPDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testBadASN1Fails()
    {
        $this->expectException(DecodeError::class);
        ScopedPDUDataDecoder::fromBinary(hex2bin('00'));
    }

    /**
     * @throws DecodeError
     */
    public function testUnexpectedASN1Fails()
    {
        $this->expectException(DecodeError::class);
        ScopedPDUDataDecoder::fromBinary(hex2bin('0500'));
    }


}
