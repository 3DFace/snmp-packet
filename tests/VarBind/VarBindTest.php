<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\VarBind;

use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
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

}
