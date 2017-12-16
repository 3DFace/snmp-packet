<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\VarBind;

use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\UnspecifiedType;
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

    public function equals($x): bool
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
            $oid = new Oid($oid_str);
        } catch (\OutOfBoundsException|\UnexpectedValueException  $e) {
            throw new DecodeError('Variable binding must be a sequence of [oid, value]');
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
            $tagged = UnspecifiedType::fromDER($binary)->asSequence();
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError('Cant decode variable binding: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($tagged);
    }

}
