<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace DataType;

use ASN1\Type\Primitive\Integer;
use dface\SnmpPacket\DataType\NoSuchObject;
use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class AbstractNoValueTest extends TestCase
{

    /**
     * @throws DecodeError
     */
    public function testNonContextFails(){
        $this->expectException(DecodeError::class);
        NoSuchObject::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidTagFails(){
        $this->expectException(DecodeError::class);
        NoSuchObject::fromBinary(hex2bin('8100'));
    }

    /**
     * @throws DecodeError
     */
    public function testInvalidASN1Fails(){
        $this->expectException(DecodeError::class);
        NoSuchObject::fromBinary(hex2bin('81'));
    }

    /**
     * @throws DecodeError
     */
    public function testUnexpectedASN1Fails(){
        $this->expectException(DecodeError::class);
        NoSuchObject::fromASN1((new Integer(1))->asUnspecified());
    }

}
