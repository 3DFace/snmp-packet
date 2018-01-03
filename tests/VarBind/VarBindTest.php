<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\VarBind;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\ObjectIdentifier;
use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\DataType\TimeTicks;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class VarBindTest extends TestCase
{

    private const example = '3011060d2b0601040194780102070302000500';

    public function testEncoded()
    {
        $var_bind = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $bin = $var_bind->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = VarBind::fromBinary(hex2bin(self::example));
        $var_bind = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $this->assertTrue($decoded->equals($var_bind));
    }

    /**
     * @throws DecodeError
     */
    public function testNonSequenceFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        VarBind::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceCountFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        VarBind::fromASN1(new Sequence(new Integer(1)));
    }

    /**
     * @throws DecodeError
     */
    public function testBadOidElementFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        VarBind::fromASN1(new Sequence(
            new OctetString('asd'),
            new Integer(1)));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSnmpOidFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        VarBind::fromASN1(new Sequence(
            new ObjectIdentifier('2.2'),
            new Integer(1)));
    }

    /**
     * @throws DecodeError
     */
    public function testBadValueElementFails()
    {
        $this->expectException(DecodeError::class);
        $this->expectExceptionCode(0);
        VarBind::fromASN1(new Sequence(
            new ObjectIdentifier('1.3.6.1.4.1.2680.1.2.7.3.2.0'),
            new Sequence()));
    }

    public function testGetters()
    {
        $oid = new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0');
        $tt = new TimeTicks(1);
        $var_bind = new VarBind($oid, $tt);
        $this->assertTrue($oid->equals($var_bind->getOid()));
        $this->assertTrue($tt->equals($var_bind->getValue()));
    }

    public function testEquals()
    {
        $oid = new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0');
        $tt = new TimeTicks(1);
        $x1 = new VarBind($oid, $tt);
        $x2 = new VarBind($oid, $tt);
        $this->assertTrue($x1->equals($x2));
    }

    public function testNotEquals()
    {
        $oid1 = new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0');
        $oid2 = new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.1');
        $tt1 = new TimeTicks(1);
        $tt2 = new TimeTicks(2);
        $x1 = new VarBind($oid1, $tt1);

        $this->assertFalse($x1->equals(new VarBind($oid2, $tt1)));
        $this->assertFalse($x1->equals(new VarBind($oid1, $tt2)));
        $this->assertFalse($x1->equals(new VarBind($oid2, $tt2)));
    }

}
