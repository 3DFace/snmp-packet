<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace PDU;

use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\DataType\TimeTicks;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\PDU\PDUDecoder;
use dface\SnmpPacket\PDU\TrapPDU;
use dface\SnmpPacket\PDU\TrapPDUBody;
use dface\SnmpPacket\VarBind\VarBind;
use dface\SnmpPacket\VarBind\VarBindList;
use PHPUnit\Framework\TestCase;

class TrapPDUTest extends TestCase
{

    private const example = 'a43206082b0601020101030004093132372e302e302e310201010201010201013012301006082b0601020101030043040b1608c5';

    public function testEncoded()
    {
        $pdu_body = new TrapPDUBody('1.3.6.1.2.1.1.3.0', '127.0.0.1', 1, 1, 1, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new TimeTicks(185993413)),
        ]));
        $pdu = new TrapPDU($pdu_body);
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
        $pdu_body = new TrapPDUBody('1.3.6.1.2.1.1.3.0', '127.0.0.1', 1, 1, 1, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new TimeTicks(185993413)),
        ]));
        $pdu = new TrapPDU($pdu_body);
        $this->assertTrue($decoded->equals($pdu));
    }

}
