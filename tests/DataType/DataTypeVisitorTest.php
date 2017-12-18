<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use dface\SnmpPacket\DataType\BitString;
use dface\SnmpPacket\DataType\Counter32;
use dface\SnmpPacket\DataType\Counter64;
use dface\SnmpPacket\DataType\DataType;
use dface\SnmpPacket\DataType\EndOfMibView;
use dface\SnmpPacket\DataType\Gauge32;
use dface\SnmpPacket\DataType\Integer32;
use dface\SnmpPacket\DataType\IpAddress;
use dface\SnmpPacket\DataType\NoSuchInstance;
use dface\SnmpPacket\DataType\NoSuchObject;
use dface\SnmpPacket\DataType\NsapAddress;
use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\OctetString;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\DataType\Opaque;
use dface\SnmpPacket\DataType\TestDataTypeVisitor;
use dface\SnmpPacket\DataType\TimeTicks;
use dface\SnmpPacket\DataType\UInteger32;
use PHPUnit\Framework\TestCase;

class DataTypeVisitorTest extends TestCase
{

    private function ensureVisited(DataType $original)
    {
        $visited = $original->acceptVisitor(new TestDataTypeVisitor());
        $this->assertTrue($original->equals($visited));
    }

    public function testInteger32Visited()
    {
        $this->ensureVisited(new Integer32(123));
    }

    public function testOctetStringVisited()
    {
        $this->ensureVisited(new OctetString('asd'));
    }

    public function testOidVisited()
    {
        $this->ensureVisited(new Oid('1.1'));
    }

    public function testBitStringVisited()
    {
        $this->ensureVisited(new BitString('asd', 3));
    }

    public function testNullVisited()
    {
        $this->ensureVisited(new NullValue());
    }

    public function testIpAddressVisited()
    {
        $this->ensureVisited(new IpAddress('10.10.10.10'));
    }

    public function testNsapAddressVisited()
    {
        $this->ensureVisited(new NsapAddress('wat?'));
    }

    public function testCounter32Visited()
    {
        $this->ensureVisited(new Counter32(123));
    }

    public function testCounter64Visited()
    {
        $this->ensureVisited(new Counter64(123));
    }

    public function testGauge32Visited()
    {
        $this->ensureVisited(new Gauge32(123));
    }

    public function testUInteger32Visited()
    {
        $this->ensureVisited(new UInteger32(123));
    }

    public function testTimeTicksVisited()
    {
        $this->ensureVisited(new TimeTicks(123));
    }

    public function testOpaqueVisited()
    {
        $this->ensureVisited(new Opaque('asd'));
    }

    public function testNoSuchObjectVisited()
    {
        $this->ensureVisited(new NoSuchObject());
    }

    public function testNoSuchInstanceVisited()
    {
        $this->ensureVisited(new NoSuchInstance());
    }

    public function testEndOfMibViewVisited()
    {
        $this->ensureVisited(new EndOfMibView());
    }

}
