<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Examples;

use dface\SnmpPacket\DataType\DataTypeVisitor;

class DataTypeToString implements DataTypeVisitor
{

    public function visitInteger32(int $value)
    {
        return [$value, 'INT-32'];
    }

    public function visitOctetString(string $value)
    {
        return [$value, 'OCTET STRING'];
    }

    public function visitOid(string $value)
    {
        return [$value, 'OID'];
    }

    public function visitBitString(string $value, int $unused_bits)
    {
        return [$value.'/'.$unused_bits, 'BIT-STRING'];
    }

    public function visitNull()
    {
        return [null, 'NULL'];
    }

    public function visitIpAddress(string $ipString)
    {
        return [$ipString, 'IP-ADDRESS'];
    }

    public function visitNsapAddress(string $octetString)
    {
        return [$octetString, 'NSAP-ADDRESS'];
    }

    public function visitCounter32($value)
    {
        return [$value, 'COUNTER-32'];
    }

    public function visitCounter64($value)
    {
        return [$value, 'COUNTER-64'];
    }

    public function visitGauge32($value)
    {
        return [$value, 'GAUGE-32'];
    }

    public function visitUInteger32($value)
    {
        return [$value, 'U-INT-32'];
    }

    public function visitTimeTicks($value)
    {
        return [$value, 'TIME-TICKS'];
    }

    public function visitOpaque(string $value)
    {
        return [bin2hex($value), 'OPAQUE-HEX'];
    }

    public function visitNoSuchObject()
    {
        return [null, 'NO-SUCH-OBJECT'];
    }

    public function visitNoSuchInstance()
    {
        return [null, 'NO-SUCH-INSTANCE'];
    }

    public function visitEndOfMibView()
    {
        return [null, 'END-OF-MIB-VIEW'];
    }

}
