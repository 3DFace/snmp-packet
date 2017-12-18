<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\PDU\GetRequestPDU;
use dface\SnmpPacket\VarBind\VarBind;
use dface\SnmpPacket\VarBind\VarBindList;
use PHPUnit\Framework\TestCase;

class MessageV1Test extends TestCase
{

    private const get_request_example = '302c020100040770726976617465a01e02010102010002010030133011060d2b0601040194780102070302000500';

    public function testEncoded()
    {
        $pdu = new GetRequestPDU(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $testMessage = new MessageV1(0, 'private', $pdu);

        $this->assertEquals(self::get_request_example, bin2hex($testMessage->toBinary()));
        $this->assertEquals(new Sequence(
            new Integer(0),
            new OctetString('private'),
            $pdu->toASN1()
        ), $testMessage->toASN1());
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded(): void
    {
        $bin = hex2bin(self::get_request_example);
        $decodedMessage = MessageV1::fromBinary($bin);

        $pdu = new GetRequestPDU(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $testMessage = new MessageV1(0, 'private', $pdu);

        $this->assertTrue($testMessage->equals($decodedMessage));
    }

    /**
     * @throws DecodeError
     */
    public function testNonSequenceFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        MessageV1::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceCountFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        MessageV1::fromASN1(new Sequence(
            new Integer(1)));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceElementsFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        MessageV1::fromASN1(new Sequence(
            new OctetString('asd'),
            new Integer(1),
            new Integer(1)));
    }

    public function testGetters()
    {

        $pdu = new GetRequestPDU(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));

        $msg = new MessageV1(0, 'public', $pdu);
        $this->assertEquals(0, $msg->getVersion());
        $this->assertEquals('public', $msg->getCommunity());
        $this->assertTrue($pdu->equals($msg->getPdu()));
    }

    public function testEquals()
    {
        $pdu = new GetRequestPDU(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $x1 = new MessageV1(0, 'public', $pdu);
        $x2 = new MessageV1(0, 'public', $pdu);
        $this->assertTrue($x1->equals($x2));
    }

    public function testNotEquals()
    {
        $pdu1 = new GetRequestPDU(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $pdu2 = new GetRequestPDU(2, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $x1 = new MessageV1(0, 'public', $pdu1);
        $this->assertFalse($x1->equals(new MessageV1(1, 'public', $pdu1)));
        $this->assertFalse($x1->equals(new MessageV1(0, 'private', $pdu1)));
        $this->assertFalse($x1->equals(new MessageV1(0, 'public', $pdu2)));
        $this->assertFalse($x1->equals(new MessageV1(1, 'private', $pdu2)));
        $this->assertFalse($x1->equals(new OctetString('asd')));
    }

}
