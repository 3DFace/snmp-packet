<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\DataType\Counter32;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\DataType\TimeTicks;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\PDU\BasicPDUBody;
use dface\SnmpPacket\PDU\GetRequestPDU;
use dface\SnmpPacket\PDU\GetResponsePDU;
use dface\SnmpPacket\PDU\ReportPDU;
use dface\SnmpPacket\VarBind\VarBind;
use dface\SnmpPacket\VarBind\VarBindList;
use PHPUnit\Framework\TestCase;

class MessageV3Test extends TestCase
{

    private const get_request_discovery_example = '303e020103301102042c22074a020300ffe30401040201030410300e0400020100020100040004000400301404000400a00e0204272900d20201000201003000';
    private const get_request_example = '307b020103301102043eed8d08020300ffe304010502010304323030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040cfc28d803bd8fc625cb3f93340400302f040d80000103037072cf48b4f800000400a01c02041ae944c8020100020100300e300c06082b060102010103000500';
    private const get_response_example = '307f020103301102043eed8d08020300ffe304010102010304323030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040c04f1b1af5c0055b27d58ab0404003033040d80000103037072cf48b4f800000400a22002041ae944c80201000201003012301006082b06010201010300430405baed00';
    private const report_example = '306c020103301102042c22074a020300ffe3040100020103041f301d040d80000103037072cf48b4f8000002016802030ed0870400040004003033040d80000103037072cf48b4f800000400a8200204272900d202010002010030123010060a2b060106030f0101040041020496';

    public function testGetRequestEncoded()
    {
        $pdu_body = new BasicPDUBody(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $pdu = new GetRequestPDU($pdu_body);
        $scoped_pdu = new ScopedPDU(hex2bin('80000103037072cf48b4f80000'), '', $pdu);
        $header = new HeaderData(1055755528, 65507, 5, 3);
        $testMessage = new MessageV3(3, $header,
            hex2bin('3030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040cfc28d803bd8fc625cb3f93340400'),
            $scoped_pdu);
        $bin = $testMessage->toBinary();

        $this->assertEquals(self::get_request_example, bin2hex($bin));
    }

    /**
     * @throws DecodeError
     */
    public function testGetRequestDecoded(): void
    {
        $bin = hex2bin(self::get_request_example);
        $decodedMessage = MessageV3::fromBinary($bin);

        $pdu_body = new BasicPDUBody(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $pdu = new GetRequestPDU($pdu_body);
        $scoped_pdu = new ScopedPDU(hex2bin('80000103037072cf48b4f80000'), '', $pdu);
        $header = new HeaderData(1055755528, 65507, 5, 3);
        $testMessage = new MessageV3(3, $header,
            hex2bin('3030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040cfc28d803bd8fc625cb3f93340400'),
            $scoped_pdu);

        $this->assertTrue($testMessage->equals($decodedMessage));
    }

    public function testGetResponseEncoded(): void
    {
        $pdu_body = new BasicPDUBody(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new TimeTicks('96136448'))
        ));
        $pdu = new GetResponsePDU($pdu_body);
        $scoped_pdu = new ScopedPDU(hex2bin('80000103037072cf48b4f80000'), '', $pdu);
        $header = new HeaderData(1055755528, 65507, 1, 3);
        $testMessage = new MessageV3(3, $header,
            hex2bin('3030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040c04f1b1af5c0055b27d58ab040400'),
            $scoped_pdu);
        $bin = $testMessage->toBinary();

        $this->assertEquals(self::get_response_example, bin2hex($bin));
    }

    /**
     * @throws DecodeError
     */
    public function testGetResponseDecoded(): void
    {
        $bin = hex2bin(self::get_response_example);
        $decodedMessage = MessageV3::fromBinary($bin);

        $pdu_body = new BasicPDUBody(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new TimeTicks('96136448'))
        ));
        $pdu = new GetResponsePDU($pdu_body);
        $scoped_pdu = new ScopedPDU(hex2bin('80000103037072cf48b4f80000'), '', $pdu);
        $header = new HeaderData(1055755528, 65507, 1, 3);
        $testMessage = new MessageV3(3, $header,
            hex2bin('3030040d80000103037072cf48b4f8000002016802030eab54040762696c6c696e67040c04f1b1af5c0055b27d58ab040400'),
            $scoped_pdu);

        $this->assertTrue($testMessage->equals($decodedMessage));
    }

    /**
     * @throws DecodeError
     */
    public function testReportDecoded(): void
    {
        $bin = hex2bin(self::report_example);
        $decodedMessage = MessageV3::fromBinary($bin);

        $pdu_body = new BasicPDUBody(656998610, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.6.3.15.1.1.4.0'), new Counter32(1174))
        ));
        $pdu = new ReportPDU($pdu_body);
        $scoped_pdu = new ScopedPDU(hex2bin('80000103037072cf48b4f80000'), '', $pdu);
        $header = new HeaderData(740427594, 65507, 0, 3);
        $testMessage = new MessageV3(3, $header,
            hex2bin('301d040d80000103037072cf48b4f8000002016802030ed087040004000400'),
            $scoped_pdu);

        $this->assertTrue($testMessage->equals($decodedMessage));
    }

    /**
     * @throws DecodeError
     */
    public function testGetRequestDiscoveryDecoded(): void
    {
        $bin = hex2bin(self::get_request_discovery_example);
        $decodedMessage = MessageV3::fromBinary($bin);

        $pdu_body = new BasicPDUBody(656998610, 0, 0, new VarBindList());
        $pdu = new GetRequestPDU($pdu_body);
        $scoped_pdu = new ScopedPDU('', '', $pdu);
        $header = new HeaderData(740427594, 65507, 4, 3);
        $testMessage = new MessageV3(3, $header, hex2bin('300e0400020100020100040004000400'), $scoped_pdu);

        $this->assertTrue($testMessage->equals($decodedMessage));
    }

    /**
     * @throws DecodeError
     */
    public function testNonSequenceFails()
    {
        $this->expectException(DecodeError::class);
        MessageV3::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceCountFails()
    {
        $this->expectException(DecodeError::class);
        MessageV3::fromASN1(new Sequence(
            new Integer(1)));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceElementsFails()
    {
        $this->expectException(DecodeError::class);
        MessageV3::fromASN1(new Sequence(
            new OctetString('asd'),
            new Integer(1),
            new Integer(1),
            new Integer(1)));
    }

    public function testGetters(){

        $pdu_body = new BasicPDUBody(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $pdu = new GetRequestPDU($pdu_body);
        $scoped_pdu = new ScopedPDU('1', '2', $pdu);
        $headers = new HeaderData(1, 1, 1, 1);

        $msg = new MessageV3(3, $headers, 'asd', $scoped_pdu);

        $this->assertEquals(3, $msg->getVersion());
        $this->assertTrue($headers->equals($msg->getGlobalData()));
        $this->assertEquals('asd', $msg->getSecurityParameters());
        $this->assertTrue($scoped_pdu->equals($msg->getData()));
    }

}
