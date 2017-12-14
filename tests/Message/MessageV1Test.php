<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\PDU\BasicPDUBody;
use dface\SnmpPacket\PDU\GetRequestPDU;
use dface\SnmpPacket\VarBind\VarBind;
use dface\SnmpPacket\VarBind\VarBindList;
use PHPUnit\Framework\TestCase;

class MessageV1Test extends TestCase
{

    private const get_request_example = '302c020100040770726976617465a01e02010102010002010030133011060d2b0601040194780102070302000500';

    public function testEncoded()
    {
        $pdu_body = new BasicPDUBody(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $pdu = new GetRequestPDU($pdu_body);
        $testMessage = new MessageV1(0, 'private', $pdu);
        $bin = $testMessage->toBinary();

        $this->assertEquals(self::get_request_example, bin2hex($bin));
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded(): void
    {
        $bin = hex2bin(self::get_request_example);
        $decodedMessage = MessageDecoder::fromBinary($bin);

        $pdu_body = new BasicPDUBody(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $pdu = new GetRequestPDU($pdu_body);
        $testMessage = new MessageV1(0, 'private', $pdu);

        $this->assertTrue($testMessage->equals($decodedMessage));
    }

}
