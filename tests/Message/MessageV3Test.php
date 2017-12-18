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

class MessageV3Test extends TestCase
{

    private const get_request_example = '307b020103301102043eed8d08020300ffe304010502010304323030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040cfc28d803bd8fc625cb3f93340400302f040d80000103037072cf48b4f800000400a01c02041ae944c8020100020100300e300c06082b060102010103000500';

    public function testGetRequestEncoded()
    {
        $pdu = new GetRequestPDU(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $scoped_pdu = new ScopedPDU(hex2bin('80000103037072cf48b4f80000'), '', $pdu);
        $header = new HeaderData(1055755528, 65507, 5, 3);
        $testMessage = new MessageV3(3, $header,
            hex2bin('3030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040cfc28d803bd8fc625cb3f93340400'),
            $scoped_pdu);

        $this->assertEquals(self::get_request_example, bin2hex($testMessage->toBinary()));

        $this->assertEquals(new Sequence(
            new Integer(3),
            $header->toASN1(),
            new OctetString(hex2bin('3030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040cfc28d803bd8fc625cb3f93340400')),
            $scoped_pdu->toASN1()
        ), $testMessage->toASN1());
    }

    /**
     * @throws DecodeError
     */
    public function testGetRequestDecoded(): void
    {
        $bin = hex2bin(self::get_request_example);
        $decodedMessage = MessageV3::fromBinary($bin);

        $pdu = new GetRequestPDU(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $scoped_pdu = new ScopedPDU(hex2bin('80000103037072cf48b4f80000'), '', $pdu);
        $header = new HeaderData(1055755528, 65507, 5, 3);
        $testMessage = new MessageV3(3, $header,
            hex2bin('3030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040cfc28d803bd8fc625cb3f93340400'),
            $scoped_pdu);

        $this->assertTrue($testMessage->equals($decodedMessage));
    }

    /**
     * @throws DecodeError
     */
    public function testNonSequenceFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        MessageV3::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceCountFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        MessageV3::fromASN1(new Sequence(
            new Integer(1)));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceElementsFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        MessageV3::fromASN1(new Sequence(
            new OctetString('asd'),
            new Integer(1),
            new Integer(1),
            new Integer(1)));
    }

    public function testGetters()
    {

        $pdu = new GetRequestPDU(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $scoped_pdu = new ScopedPDU('1', '2', $pdu);
        $headers = new HeaderData(1, 1, 1, 1);

        $msg = new MessageV3(3, $headers, 'asd', $scoped_pdu);

        $this->assertEquals(3, $msg->getVersion());
        $this->assertTrue($headers->equals($msg->getGlobalData()));
        $this->assertEquals('asd', $msg->getSecurityParameters());
        $this->assertTrue($scoped_pdu->equals($msg->getData()));
    }


    public function testEquals()
    {
        $pdu = new GetRequestPDU(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $scoped_pdu = new ScopedPDU('1', '2', $pdu);
        $headers = new HeaderData(1, 1, 1, 1);

        $x1 = new MessageV3(3, $headers, 'asd', $scoped_pdu);
        $x2 = new MessageV3(3, $headers, 'asd', $scoped_pdu);
        $this->assertTrue($x1->equals($x2));
    }

    public function testNotEquals()
    {
        $pdu = new GetRequestPDU(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $scoped_pdu1 = new ScopedPDU('1', '2', $pdu);
        $scoped_pdu2 = new ScopedPDU('2', '3', $pdu);
        $headers1 = new HeaderData(1, 1, 1, 1);
        $headers2 = new HeaderData(2, 2, 2, 2);

        $x1 = new MessageV3(3, $headers1, 'asd', $scoped_pdu1);

        $this->assertFalse($x1->equals(new MessageV3(2, $headers1, 'asd', $scoped_pdu1)));
        $this->assertFalse($x1->equals(new MessageV3(3, $headers2, 'asd', $scoped_pdu1)));
        $this->assertFalse($x1->equals(new MessageV3(3, $headers1, 'zxc', $scoped_pdu1)));
        $this->assertFalse($x1->equals(new MessageV3(3, $headers1, 'asd', $scoped_pdu2)));
        $this->assertFalse($x1->equals(new MessageV3(2, $headers2, 'zxc', $scoped_pdu2)));

        $this->assertFalse($x1->equals(new OctetString('asd')));
    }

}
