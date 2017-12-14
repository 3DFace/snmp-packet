<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\VarBind;

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

}
