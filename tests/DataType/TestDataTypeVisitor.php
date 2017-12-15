<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

class TestDataTypeVisitor implements DataTypeVisitor
{
    public function visitInteger32(int $value)
    {
        return new Integer32($value);
    }

    public function visitOctetString(string $value)
    {
        return new OctetString($value);
    }

    public function visitOid(string $value)
    {
        return new Oid($value);
    }

    public function visitBitString(string $value, int $unused_bits)
    {
        return new BitString($value, $unused_bits);
    }

    public function visitNull()
    {
        return new NullValue();
    }

    public function visitIpAddress(string $ipString)
    {
        return new IpAddress($ipString);
    }

    public function visitNsapAddress(string $octetString)
    {
        return new NsapAddress($octetString);
    }

    public function visitCounter32($value)
    {
        return new Counter32($value);
    }

    public function visitCounter64($value)
    {
        return new Counter64($value);
    }

    public function visitGauge32($value)
    {
        return new Gauge32($value);
    }

    public function visitUInteger32($value)
    {
        return new UInteger32($value);
    }

    public function visitTimeTicks($value)
    {
        return new TimeTicks($value);
    }

    public function visitOpaque(string $value)
    {
        return new Opaque($value);
    }

    public function visitNoSuchObject()
    {
        return new NoSuchObject();
    }

    public function visitNoSuchInstance()
    {
        return new NoSuchInstance();
    }

    public function visitEndOfMibView()
    {
        return new EndOfMibView();
    }

}
