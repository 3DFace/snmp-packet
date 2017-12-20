<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace PDU;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\DataType\TimeTicks;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\PDU\TrapPDU;
use dface\SnmpPacket\VarBind\VarBind;
use dface\SnmpPacket\VarBind\VarBindList;
use PHPUnit\Framework\TestCase;

class TrapPDUTest extends TestCase
{

    private const example = 'a43206082b0601020101030004093132372e302e302e310201010201010201013012301006082b0601020101030043040b1608c5';

    public function testEncoded()
    {
        $pdu = new TrapPDU('1.3.6.1.2.1.1.3.0', '127.0.0.1', 1, 1, 1, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new TimeTicks(185993413)),
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
        $decoded = TrapPDU::fromBinary(hex2bin(self::example));
        $pdu = new TrapPDU('1.3.6.1.2.1.1.3.0', '127.0.0.1', 1, 1, 1, new VarBindList(...[
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new TimeTicks(185993413)),
        ]));
        $this->assertTrue($decoded->equals($pdu));
    }

    /**
     * @throws DecodeError
     */
    public function testNonContextFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        TrapPDU::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceCountFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        TrapPDU::fromASN1(
            new ImplicitlyTaggedType(
                TrapPDU::TAG,
                new Sequence(new Integer(1))
            )
        );
    }

    /**
     * @throws DecodeError
     */
    public function testBadEnterpriseElementFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        TrapPDU::fromASN1(new ImplicitlyTaggedType(TrapPDU::TAG, new Sequence(
            new Integer(1),
            new OctetString('asd'),
            new Integer(1),
            new Integer(1),
            new Integer(1),
            new Sequence())));
    }

    /**
     * @throws DecodeError
     */
    public function testBadAgentAddressElementFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        TrapPDU::fromASN1(new ImplicitlyTaggedType(TrapPDU::TAG, new Sequence(
            new OctetString('asd'),
            new Integer(1),
            new Integer(1),
            new Integer(1),
            new Integer(1),
            new Sequence())));
    }

    /**
     * @throws DecodeError
     */
    public function testBadGenericTrapElementFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        TrapPDU::fromASN1(new ImplicitlyTaggedType(TrapPDU::TAG, new Sequence(
            new OctetString('asd'),
            new OctetString('asd'),
            new OctetString('asd'),
            new Integer(1),
            new Integer(1),
            new Sequence())));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSpecificTrapElementFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        TrapPDU::fromASN1(new ImplicitlyTaggedType(TrapPDU::TAG, new Sequence(
            new OctetString('asd'),
            new OctetString('asd'),
            new Integer(1),
            new OctetString('asd'),
            new Integer(1),
            new Sequence())));
    }

    /**
     * @throws DecodeError
     */
    public function testBadTimeStampElementFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        TrapPDU::fromASN1(new ImplicitlyTaggedType(TrapPDU::TAG, new Sequence(
            new OctetString('asd'),
            new OctetString('asd'),
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
        $this->expectExceptionCode(0);
        TrapPDU::fromASN1(new ImplicitlyTaggedType(TrapPDU::TAG, new Sequence(
            new OctetString('asd'),
            new OctetString('asd'),
            new Integer(1),
            new Integer(1),
            new Integer(1),
            new OctetString('asd'))));
    }

    public function testGetters()
    {
        $enterprise = '1.3.6.1.2.1.1.3.0';
        $agent = '127.0.0.1';
        $var_bind = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $var_bind_list = new VarBindList($var_bind);
        $pdu = new TrapPDU($enterprise, $agent, 1, 2, 3, $var_bind_list);
        $this->assertEquals($enterprise, $pdu->getEnterprise());
        $this->assertEquals($agent, $pdu->getAgentAddress());
        $this->assertEquals(1, $pdu->getGenericTrap());
        $this->assertEquals(2, $pdu->getSpecificTrap());
        $this->assertEquals(3, $pdu->getTimestamp());
        $this->assertTrue($var_bind_list->equals($pdu->getVariableBindings()));
    }

    public function testTag()
    {
        $pdu = new TrapPDU('', '', 0, 0, 0, new VarBindList());
        $this->assertEquals(TrapPDU::TAG, $pdu->getTag());
    }

    public function testEquals()
    {
        $enterprise = '1.3.6.1.2.1.1.3.0';
        $agent = '127.0.0.1';
        $var_bind = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $var_bind_list = new VarBindList($var_bind);
        $x1 = new TrapPDU($enterprise, $agent, 1, 2, 3, $var_bind_list);
        $x2 = new TrapPDU($enterprise, $agent, 1, 2, 3, $var_bind_list);
        $this->assertTrue($x1->equals($x2));
    }

    public function testNotEquals()
    {
        $enterprise1 = '1.3.6.1.2.1.1.3.0';
        $enterprise2 = '1.3.6.1.2.1.1.3.1';
        $agent1 = '127.0.0.1';
        $agent2 = '10.10.10.10';
        $var_bind1 = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $var_bind_list1 = new VarBindList($var_bind1);
        $var_bind2 = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.1'), new NullValue());
        $var_bind_list2 = new VarBindList($var_bind2);
        $x1 = new TrapPDU($enterprise1, $agent1, 1, 2, 3, $var_bind_list1);

        $this->assertFalse($x1->equals(new TrapPDU($enterprise2, $agent1, 1, 2, 3, $var_bind_list1)));
        $this->assertFalse($x1->equals(new TrapPDU($enterprise1, $agent2, 1, 2, 3, $var_bind_list1)));
        $this->assertFalse($x1->equals(new TrapPDU($enterprise1, $agent1, 2, 2, 3, $var_bind_list1)));
        $this->assertFalse($x1->equals(new TrapPDU($enterprise1, $agent1, 1, 3, 3, $var_bind_list1)));
        $this->assertFalse($x1->equals(new TrapPDU($enterprise1, $agent1, 1, 2, 4, $var_bind_list1)));
        $this->assertFalse($x1->equals(new TrapPDU($enterprise1, $agent1, 1, 2, 3, $var_bind_list2)));
        $this->assertFalse($x1->equals(new TrapPDU($enterprise2, $agent2, 5, 5, 5, $var_bind_list2)));
    }

}
