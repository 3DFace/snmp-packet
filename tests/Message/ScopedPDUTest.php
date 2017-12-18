<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace Message;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\OctetString;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\Message\ScopedPDU;
use dface\SnmpPacket\PDU\GetRequestPDU;
use dface\SnmpPacket\VarBind\VarBind;
use dface\SnmpPacket\VarBind\VarBindList;
use PHPUnit\Framework\TestCase;

class ScopedPDUTest extends TestCase
{

    private const example = '3028040361736404037a7863a01c02041ae944c8020100020100300e300c06082b060102010103000500';

    public function testEncoded()
    {
        $pdu = new GetRequestPDU(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $scoped_pdu = new ScopedPDU('asd', 'zxc', $pdu);
        $bin = $scoped_pdu->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = ScopedPDU::fromBinary(hex2bin(self::example));
        $pdu = new GetRequestPDU(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $scoped_pdu = new ScopedPDU('asd', 'zxc', $pdu);
        $this->assertTrue($decoded->equals($scoped_pdu));
    }

    /**
     * @throws DecodeError
     */
    public function testBadASN1Fails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        ScopedPDU::fromBinary(hex2bin('05'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonSequenceFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        ScopedPDU::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceCountFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        ScopedPDU::fromASN1((new Sequence(
            new Integer(1)
        ))->asUnspecified());
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceElementsFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        ScopedPDU::fromASN1((new Sequence(
            new Integer(1),
            new Integer(1),
            new Integer(1)))->asUnspecified());
    }

    public function testGetters()
    {
        $pdu = new GetRequestPDU(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $x = new ScopedPDU('asd', 'zxc', $pdu);

        $this->assertEquals('asd', $x->getContextEngineId());
        $this->assertEquals('zxc', $x->getContextName());
        $this->assertTrue($pdu->equals($x->getData()));
    }

    public function testEquals()
    {
        $pdu = new GetRequestPDU(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $x1 = new ScopedPDU('asd', 'zxc', $pdu);
        $x2 = new ScopedPDU('asd', 'zxc', $pdu);
        $this->assertTrue($x1->equals($x2));
    }

    public function testNotEquals()
    {
        $pdu1 = new GetRequestPDU(451495112, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $pdu2 = new GetRequestPDU(451495113, 0, 0, new VarBindList(
            new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
        ));
        $x1 = new ScopedPDU('asd', 'zxc', $pdu1);

        $this->assertFalse($x1->equals(new ScopedPDU('zxc', 'zxc', $pdu1)));
        $this->assertFalse($x1->equals(new ScopedPDU('asd', 'asd', $pdu1)));
        $this->assertFalse($x1->equals(new ScopedPDU('asd', 'zxc', $pdu2)));
        $this->assertFalse($x1->equals(new ScopedPDU('zxc', 'asd', $pdu2)));

        $this->assertFalse($x1->equals(new OctetString('asd')));
    }

}
