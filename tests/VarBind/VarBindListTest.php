<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\VarBind;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class VarBindListTest extends TestCase
{

    private const example = '30133011060d2b0601040194780102070302000500';

    public function testEncoded()
    {
        $var_bind = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $var_bind_list = new VarBindList($var_bind);
        $bin = $var_bind_list->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = VarBindList::fromBinary(hex2bin(self::example));
        $var_bind = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $var_bind_list = new VarBindList($var_bind);
        $this->assertTrue($decoded->equals($var_bind_list));
    }

    /**
     * @throws DecodeError
     */
    public function testNonSequenceFails()
    {
        $this->expectException(DecodeError::class);
        VarBindList::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testNonSequenceOfSequenceFails()
    {
        $this->expectException(DecodeError::class);
        VarBindList::fromASN1(new Sequence(new Integer(1)));
    }

    public function testGetters()
    {
        $var_bind = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $var_bind_list = new VarBindList($var_bind);
        $this->assertTrue($var_bind->equals($var_bind_list->getList()[0]));
    }

    public function testEquals()
    {
        $var_bind = new VarBind(new Oid('1.3.6.1.4.1.2680.1.2.7.3.2.0'), new NullValue());
        $list1 = new VarBindList($var_bind);
        $list2 = new VarBindList($var_bind);
        $list3 = new VarBindList();
        $this->assertTrue($list1->equals($list2));
        $this->assertFalse($list1->equals($list3));

    }

}
