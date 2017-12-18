<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

use dface\SnmpPacket\Exception\DecodeError;
use PHPUnit\Framework\TestCase;

class PDUDecoderTest extends TestCase
{

    /**
     * @throws DecodeError
     */
    public function testGetRequestDecoded()
    {
        $x = PDUDecoder::fromBinary(hex2bin('a01e02010102010002010030133011060d2b0601040194780102070302000500'));
        $this->assertInstanceOf(GetRequestPDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testGetNextRequestDecoded()
    {
        $x = PDUDecoder::fromBinary(hex2bin('a11e02010102010002010030133011060d2b0601040194780102070302000500'));
        $this->assertInstanceOf(GetNextRequestPDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testGetResponsePDUDecoded()
    {
        $x = PDUDecoder::fromBinary(hex2bin('a21e02010102010002010030133011060d2b0601040194780102070302000500'));
        $this->assertInstanceOf(GetResponsePDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testInformRequestPDUDecoded()
    {
        $x = PDUDecoder::fromBinary(hex2bin('a61e02010102010002010030133011060d2b0601040194780102070302000500'));
        $this->assertInstanceOf(InformRequestPDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testReportPDUDecoded()
    {
        $x = PDUDecoder::fromBinary(hex2bin('a81e02010102010002010030133011060d2b0601040194780102070302000500'));
        $this->assertInstanceOf(ReportPDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testSetRequestPDUDecoded()
    {
        $x = PDUDecoder::fromBinary(hex2bin('a31e02010102010002010030133011060d2b0601040194780102070302000500'));
        $this->assertInstanceOf(SetRequestPDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testTrapPDUDecoded()
    {
        $x = PDUDecoder::fromBinary(hex2bin('a43206082b0601020101030004093132372e302e302e310201010201010201013012301006082b0601020101030043040b1608c5'));
        $this->assertInstanceOf(TrapPDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testTrapV2PDUDecoded()
    {
        $x = PDUDecoder::fromBinary(hex2bin('a71e02010102010002010030133011060d2b0601040194780102070302000500'));
        $this->assertInstanceOf(TrapV2PDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testGetBulkRequestPDUDecoded()
    {
        $x = PDUDecoder::fromBinary(hex2bin('a51e02010102010002010030133011060d2b0601040194780102070302000500'));
        $this->assertInstanceOf(GetBulkRequestPDU::class, $x);
    }

    /**
     * @throws DecodeError
     */
    public function testBadASN1Fails()
    {
        $this->expectException(DecodeError::class);
        PDUDecoder::fromBinary(hex2bin('00'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadTagFails()
    {
        $this->expectException(DecodeError::class);
        PDUDecoder::fromBinary(hex2bin('af1e02010102010002010030133011060d2b0601040194780102070302000500'));
    }

}
