<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace PDU;

use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\PDU\BasicPDUBody;
use dface\SnmpPacket\PDU\InformRequestPDU;
use dface\SnmpPacket\PDU\PDUDecoder;
use dface\SnmpPacket\VarBind\VarBind;
use dface\SnmpPacket\VarBind\VarBindList;
use PHPUnit\Framework\TestCase;

class InformRequestPDUTest extends TestCase
{

    private const example = 'a61e02010102010002010030133011060d2b0601040194780102070302000500';

    public function testEncoded()
    {
        $pdu_body = new BasicPDUBody(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $pdu = new InformRequestPDU($pdu_body);
        $bin = $pdu->toBinary();

        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = PDUDecoder::fromBinary(hex2bin(self::example));
        $pdu_body = new BasicPDUBody(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $pdu = new InformRequestPDU($pdu_body);
        $this->assertTrue($decoded->equals($pdu));
    }

}
