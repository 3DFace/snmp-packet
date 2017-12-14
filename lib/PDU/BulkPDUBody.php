<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\VarBind\VarBindList;

class BulkPDUBody
{

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

    public function toASN1(): Sequence
    {
        return new Sequence(
            new Integer($this->request_id),
            new Integer(0),
            new Integer(0),
            $this->variable_bindings->toASN1()
        );
    }

    public function equals($x): bool
    {
        if (!$x instanceof self
            || $x->request_id !== $this->request_id
            || $x->non_repeaters !== $this->non_repeaters
            || $x->max_repetitions !== $this->max_repetitions
        ) {
            return false;
        }
        return $x->variable_bindings->equals($this->variable_bindings);
    }

    /**
     * @param Sequence $children
     * @return self
     * @throws DecodeError
     */
    public static function fromASN1(Sequence $children): self
    {
        try {
            if (\count($children) !== 4) {
                throw new DecodeError('PDU must be a sequence of [request_id, non_repeaters, max_repetitions, var_bind_list]');
            }
            $req_id = $children->at(0)->asInteger()->intNumber();
            $non_repeaters = $children->at(1)->asInteger()->intNumber();
            $max_repetitions = $children->at(2)->asInteger()->intNumber();
            $var_bind_list = $children->at(3)->asSequence();
        } catch (\OutOfBoundsException|\UnexpectedValueException $e) {
            throw new DecodeError('PDU must be a sequence of [request_id, non_repeaters, max_repetitions, var_bind_list]');
        }

        $bindings = VarBindList::fromASN1($var_bind_list);
        return new self($req_id, $non_repeaters, $max_repetitions, $bindings);
    }

}
