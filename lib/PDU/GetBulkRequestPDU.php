<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

use ASN1\Element;
use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Tagged\ImplicitlyTaggedType;
use ASN1\Type\TaggedType;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\VarBind\VarBindList;

class GetBulkRequestPDU implements PDU
{

    public const TAG = 5;

    /** @var int */
    private $request_id;
    /** @var int */
    private $non_repeaters;
    /** @var int */
    private $max_repetitions;
    /** @var VarBindList */
    private $variable_bindings;

    /**
     * @param int $request_id
     * @param int $non_repeaters
     * @param int $max_repetitions
     * @param VarBindList $variable_bindings
     */
    public function __construct(
        int $request_id,
        int $non_repeaters,
        int $max_repetitions,
        VarBindList $variable_bindings
    ) {
        $this->request_id = $request_id;
        $this->non_repeaters = $non_repeaters;
        $this->max_repetitions = $max_repetitions;
        $this->variable_bindings = $variable_bindings;
    }

    public function getRequestId(): int
    {
        return $this->request_id;
    }

    public function getNonRepeaters(): int
    {
        return $this->non_repeaters;
    }

    public function getMaxRepetitions(): int
    {
        return $this->max_repetitions;
    }

    public function getVariableBindings(): VarBindList
    {
        return $this->variable_bindings;
    }

    public function equals(PDU $x): bool
    {
        return $x instanceof static
            && $x->request_id === $this->request_id
            && $x->non_repeaters === $this->non_repeaters
            && $x->max_repetitions === $this->max_repetitions
            && $x->variable_bindings->equals($this->variable_bindings);
    }

    public function toBinary(): string
    {
        $asn1 = $this->toASN1();
        return $asn1->toDER();
    }

    public function toASN1(): ImplicitlyTaggedType
    {
        $seq = new Sequence(
            new Integer($this->request_id),
            new Integer(0),
            new Integer(0),
            $this->variable_bindings->toASN1()
        );
        return new ImplicitlyTaggedType(self::TAG, $seq);
    }

    /**
     * @param TaggedType $obj
     * @return static
     * @throws DecodeError
     */
    public static function fromASN1(TaggedType $obj): self
    {
        try {
            $sequence = $obj->asImplicit(Element::TYPE_SEQUENCE, self::TAG)->asSequence();
            if (\count($sequence) !== 4) {
                throw new DecodeError('PDU must be a sequence of [request_id, non_repeaters, max_repetitions, var_bind_list]');
            }
            $req_id = $sequence->at(0)->asInteger()->intNumber();
            $non_repeaters = $sequence->at(1)->asInteger()->intNumber();
            $max_repetitions = $sequence->at(2)->asInteger()->intNumber();
            $var_bind_list = $sequence->at(3)->asSequence();
        } catch (\OutOfBoundsException|\UnexpectedValueException $e) {
            throw new DecodeError('PDU must be a sequence of [request_id, non_repeaters, max_repetitions, var_bind_list]');
        }

        $bindings = VarBindList::fromASN1($var_bind_list);
        return new static($req_id, $non_repeaters, $max_repetitions, $bindings);

    }

    /**
     * @param string $binary
     * @return self
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): self
    {
        try {
            $tagged = UnspecifiedType::fromDER($binary)->asTagged();
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError('Cant decode PDU: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($tagged);
    }

}
