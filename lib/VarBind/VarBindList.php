<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\VarBind;

use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class VarBindList
{

    /** @var VarBind[] */
    private $list;

    /**
     * @param VarBind[] $list
     */
    public function __construct(VarBind ... $list)
    {
        $this->list = $list;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function equals(VarBindList $x): bool
    {
        if (\count($x->list) !== \count($this->list)) {
            return false;
        }
        foreach ($x->list as $i => $xVarBind) {
            $thisVarBind = $this->list[$i];
            if (!$xVarBind->equals($thisVarBind)) {
                return false;
            }
        }
        return true;
    }

    public function toASN1(): Sequence
    {
        $asn1_list = [];
        foreach ($this->list as $varBind) {
            $asn1_list[] = $varBind->toASN1();
        }
        return new Sequence(...$asn1_list);
    }

    /**
     * @param Sequence $var_bind_list
     * @return VarBindList
     * @throws DecodeError
     */
    public static function fromASN1(Sequence $var_bind_list): self
    {
        $bindings = [];
        /** @var UnspecifiedType $var_bind_un */
        foreach ($var_bind_list->getIterator() as $i => $var_bind_un) {
            try {
                $var_bind_seq = $var_bind_un->asSequence();
                $bindings[] = VarBind::fromASN1($var_bind_seq);
            } catch (\UnexpectedValueException $e) {
                throw new DecodeError("Cant decode VarBind item #$i: " . $e->getMessage(), 0, $e);
            }
        }
        return new self(...$bindings);
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
            throw new DecodeError('Cant decode VarBindList: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($tagged);
    }

}
