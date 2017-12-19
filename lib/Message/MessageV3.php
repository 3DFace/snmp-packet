<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\Exception\DecodeError;

class MessageV3 implements Message
{

    /** @var int */
    private $version;
    /** @var HeaderData */
    private $global_data;
    /** @var string */
    private $security_parameters;
    /** @var ScopedPDUData */
    private $data;

    /**
     * @param int $version
     * @param HeaderData $global_data
     * @param string $security_parameters
     * @param ScopedPDUData $data
     */
    public function __construct(
        int $version,
        HeaderData $global_data,
        string $security_parameters,
        ScopedPDUData $data
    ) {
        $this->version = $version;
        $this->global_data = $global_data;
        $this->security_parameters = $security_parameters;
        $this->data = $data;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getGlobalData(): HeaderData
    {
        return $this->global_data;
    }

    public function getSecurityParameters(): string
    {
        return $this->security_parameters;
    }

    public function getData(): ScopedPDUData
    {
        return $this->data;
    }

    public function equals($x): bool
    {
        return $x instanceof self
            && $x->version === $this->version
            && $x->global_data->equals($this->global_data)
            && $x->security_parameters === $this->security_parameters
            && $x->data->equals($this->data);
    }

    public function toBinary(): string
    {
        $obj = $this->toASN1();
        return $obj->toDER();
    }

    public function toASN1(): Sequence
    {
        return new Sequence(
            new Integer($this->version),
            $this->global_data->toASN1(),
            new OctetString($this->security_parameters),
            $this->data->toASN1()
        );
    }

    /**
     * @param string $binary
     * @return self
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): self
    {
        try {
            /** @var Sequence $seq */
            $seq = Sequence::fromDER($binary);
            return self::fromASN1($seq);
        } catch (DecodeException | \UnexpectedValueException $e) {
            throw new DecodeError('Cant decode snmp message: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param Sequence $seq
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(Sequence $seq): self
    {

        if (\count($seq) !== 4) {
            throw new DecodeError('MessageV3 must be a sequence of [version, global_data, security_parameters, data]');
        }

        try {
            $version = $seq->at(0)->asInteger()->intNumber();
            $global_data_seq = $seq->at(1)->asSequence();
            $security_parameters = $seq->at(2)->asOctetString()->string();
            $data_obj = $seq->at(3)->asUnspecified();
            $global_data = HeaderData::fromASN1($global_data_seq);
            $data = ScopedPDUDataDecoder::fromASN1($data_obj);
        } catch (\OutOfBoundsException|\UnexpectedValueException $e) {
            throw new DecodeError('MessageV3 must be a sequence of [version, global_data, security_parameters, data]',
                0, $e);
        }

        return new self($version, $global_data, $security_parameters, $data);
    }

}
