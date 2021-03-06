<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\VarBind;

use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use dface\SnmpPacket\DataType\DataType;
use dface\SnmpPacket\DataType\DataTypeDecoder;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;

class VarBind
{

    /** @var Oid */
    private $oid;
    /** @var DataType */
    private $value;

    public function __construct(Oid $oid, DataType $value)
    {
        $this->oid = $oid;
        $this->value = $value;
    }

    public function getOid(): Oid
    {
        return $this->oid;
    }

    public function getValue(): DataType
    {
        return $this->value;
    }

    public function toASN1(): Sequence
    {
        return new Sequence($this->oid->toASN1(), $this->value->toASN1());
    }

    public function equals(VarBind $x): bool
    {
        return $x instanceof self
            && $x->oid->equals($this->oid)
            && $x->value->equals($this->value);
    }

    /**
     * @param Sequence $var_bind
     * @return VarBind
     * @throws DecodeError
     */
    public static function fromASN1(Sequence $var_bind): self
    {
        if ($var_bind->count() !== 2) {
            throw new DecodeError('Variable binding must be a sequence of [oid, value]');
        }

        try {
            $oid_obj = $var_bind->at(0);
            $val_obj = $var_bind->at(1);
            $oid_str = $oid_obj->asObjectIdentifier()->oid();

        } catch (\OutOfBoundsException|\UnexpectedValueException $e) {
            throw new DecodeError('Variable binding must be a sequence of [oid, value]', 0, $e);
        }

        try {
            $oid = new Oid($oid_str);
        } catch (\InvalidArgumentException $e) {
            throw new DecodeError($e->getMessage(), 0, $e);
        }

        $val_snmp = DataTypeDecoder::fromASN1($val_obj);

        return new self($oid, $val_snmp);
    }

    public function toBinary(): string
    {
        $asn1 = $this->toASN1();
        return $asn1->toDER();
    }

    /**
     * @param string $binary
     * @return self
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): self
    {
        try {
            /** @var Sequence $sequence */
            $sequence = Sequence::fromDER($binary);
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError('Cant decode variable binding: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($sequence);
    }

}
