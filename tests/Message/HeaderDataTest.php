<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace Message;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\Message\HeaderData;
use PHPUnit\Framework\TestCase;

class HeaderDataTest extends TestCase
{

    private const example = '300d020101020201f4040100020101';

    public function testEncoded()
    {
        $data = new HeaderData(1, 500, 0, 1);
        $bin = $data->toBinary();
        $hex = bin2hex($bin);
        $this->assertEquals(self::example, $hex);
    }

    /**
     * @throws DecodeError
     */
    public function testDecoded()
    {
        $decoded = HeaderData::fromBinary(hex2bin(self::example));
        $data = new HeaderData(1, 500, 0, 1);
        $this->assertTrue($decoded->equals($data));
    }

    /**
     * @throws DecodeError
     */
    public function testNonSequenceFails()
    {
        $this->expectException(DecodeError::class);
        HeaderData::fromBinary(hex2bin('0500'));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceCountFails()
    {
        $this->expectException(DecodeError::class);
        HeaderData::fromASN1(new Sequence(
            new Integer(1)));
    }

    /**
     * @throws DecodeError
     */
    public function testBadSequenceElementsFails()
    {
        $this->expectException(DecodeError::class);
        HeaderData::fromASN1(new Sequence(
            new OctetString('asd'),
            new Integer(1),
            new Integer(1),
            new Integer(1)));
    }

    public function testGetters()
    {
        $h = new HeaderData(1, 2, 3, 4);
        $this->assertEquals(1, $h->getId());
        $this->assertEquals(2, $h->getMaxSize());
        $this->assertEquals(3, $h->getFlags());
        $this->assertEquals(4, $h->getSecurityModel());
    }

}
