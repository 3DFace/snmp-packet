<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class MessageDecoderTest extends TestCase
{

    /**
     * @throws DecodeError
     */
    public function testMessageV1Decoded()
    {
        $x = MessageDecoder::fromBinary(hex2bin('302c020100040770726976617465a01e02010102010002010030133011060d2b0601040194780102070302000500'));
        $this->assertInstanceOf(MessageV1::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testMessageV3Decoded()
    {
        $x = MessageDecoder::fromBinary(hex2bin('303e020103301102042c22074a020300ffe30401040201030410300e0400020100020100040004000400301404000400a00e0204272900d20201000201003000'));
        $this->assertInstanceOf(MessageV3::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testBadVersionFails()
    {
        $this->expectException(DecodeError::class);
        MessageDecoder::fromBinary(hex2bin('302c020109040770726976617465a01e02010102010002010030133011060d2b0601040194780102070302000500'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadASN1Fails()
    {
        $this->expectException(DecodeError::class);
        MessageDecoder::fromBinary(hex2bin('00'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonSequenceFails()
    {
        $this->expectException(DecodeError::class);
        MessageDecoder::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testNoVersionAtBeginningFails()
    {
        $this->expectException(DecodeError::class);
        MessageDecoder::fromASN1(new Sequence(
            new OctetString('asd'),
            new Integer(1)));
    }

}
