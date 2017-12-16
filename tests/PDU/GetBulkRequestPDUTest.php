<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace PDU;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\PDU\GetBulkRequestPDU;
use dface\SnmpPacket\VarBind\VarBind;
use dface\SnmpPacket\VarBind\VarBindList;
use PHPUnit\Framework\TestCase;

class GetBulkRequestPDUTest extends TestCase
{

    private const example = 'a51e02010102010002010030133011060d2b0601040194780102070302000500';

    public function testEncoded()
    {
        $pdu = new GetBulkRequestPDU(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $bin = $pdu->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = GetBulkRequestPDU::fromBinary(hex2bin(self::example));
        $pdu = new GetBulkRequestPDU(1, 0, 0, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue()),
        ]));
        $this->assertTrue($decoded->equals($pdu));
    }

    /**
     * @throws DecodeError
     */
    public function testNonContextFails()
    {
        $this->expectException(DecodeError::class);
        GetBulkRequestPDU::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceCountFails()
    {
        $this->expectException(DecodeError::class);
        GetBulkRequestPDU::fromASN1(
            new ImplicitlyTaggedType(
                GetBulkRequestPDU::TAG,
                new Sequence(new Integer(1))
            )
        );
    }

    /**
     * @throws DecodeError
     */
    public function testBadReqIdElementFails()
    {
        $this->expectException(DecodeError::class);
        GetBulkRequestPDU::fromASN1(new ImplicitlyTaggedType(GetBulkRequestPDU::TAG, new Sequence(
            new OctetString('asd'),
            new Integer(1),
            new Integer(1),
            new Sequence())));
    }

    /**
     * @throws DecodeError
     */
    public function testBadErrStatusElementFails()
    {
        $this->expectException(DecodeError::class);
        GetBulkRequestPDU::fromASN1(new ImplicitlyTaggedType(GetBulkRequestPDU::TAG, new Sequence(
            new Integer(1),
            new OctetString('asd'),
            new Integer(1),
            new Sequence())));
    }

    /**
     * @throws DecodeError
     */
    public function testBadErrIndexElementFails()
    {
        $this->expectException(DecodeError::class);
        GetBulkRequestPDU::fromASN1(new ImplicitlyTaggedType(GetBulkRequestPDU::TAG, new Sequence(
            new Integer(1),
            new Integer(1),
            new OctetString('asd'),
            new Sequence())));
    }

    /**
     * @throws DecodeError
     */
    public function testBadVarBindListElementFails()
    {
        $this->expectException(DecodeError::class);
        GetBulkRequestPDU::fromASN1(new ImplicitlyTaggedType(GetBulkRequestPDU::TAG, new Sequence(
            new Integer(1),
            new Integer(1),
            new Integer(1),
            new OctetString('asd'))));
    }

    public function testGetters()
    {
        $var_bind = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $var_bind_list = new VarBindList($var_bind);
        $pdu = new GetBulkRequestPDU(1, 2, 3, $var_bind_list);
        $this->assertEquals(1, $pdu->getRequestId());
        $this->assertEquals(2, $pdu->getNonRepeaters());
        $this->assertEquals(3, $pdu->getMaxRepetitions());
        $this->assertTrue($var_bind_list->equals($pdu->getVariableBindings()));
    }

}
