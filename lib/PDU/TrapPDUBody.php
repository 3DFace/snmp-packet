<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\ObjectIdentifier;
use ASN1\Type\Primitive\OctetString;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\VarBind\VarBindList;

class TrapPDUBody
{

    /** @var string */
    private $enterprise;
    /** @var string */
    private $agent_address;
    /** @var int */
    private $generic_trap;
    /** @var int */
    private $specific_trap;
    /** @var int */
    private $timestamp;
    /** @var VarBindList */
    private $variable_bindings;

    /**
     * @param string $enterprise
     * @param string $agent_address
     * @param int $generic_trap
     * @param int $specific_trap
     * @param int $timestamp
     * @param VarBindList $bindings
     */
    public function __construct(
        string $enterprise,
        string $agent_address,
        int $generic_trap,
        int $specific_trap,
        int $timestamp,
        VarBindList $bindings
    ) {
        $this->enterprise = $enterprise;
        $this->agent_address = $agent_address;
        $this->generic_trap = $generic_trap;
        $this->specific_trap = $specific_trap;
        $this->timestamp = $timestamp;
        $this->variable_bindings = $bindings;
    }

    public function getEnterprise(): string
    {
        return $this->enterprise;
    }

    public function getAgentAddress(): string
    {
        return $this->agent_address;
    }

    public function getGenericTrap(): int
    {
        return $this->generic_trap;
    }

    public function getSpecificTrap(): int
    {
        return $this->specific_trap;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getVariableBindings(): VarBindList
    {
        return $this->variable_bindings;
    }

    public function toASN1(): Sequence
    {
        return new Sequence(
            new ObjectIdentifier($this->enterprise),
            new OctetString($this->agent_address),
            new Integer($this->generic_trap),
            new Integer($this->specific_trap),
            new Integer($this->timestamp),
            $this->variable_bindings->toASN1()
        );
    }

    public function equals($x): bool
    {
        if (!$x instanceof self
            || $x->enterprise !== $this->enterprise
            || $x->agent_address !== $this->agent_address
            || $x->generic_trap !== $this->generic_trap
            || $x->specific_trap !== $this->specific_trap
            || $x->timestamp !== $this->timestamp
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
        if (\count($children) !== 6) {
            throw new DecodeError('Trap pdu must be a sequence of [enterprise, agent_address, generic_trap, specific_trap, timestamp, var_bind_list]');
        }

        try {
            $enterprise = $children->at(0)->asObjectIdentifier()->oid();
            $agent_address = $children->at(1)->asOctetString()->string();
            $generic_trap = $children->at(2)->asInteger()->intNumber();
            $specific_trap = $children->at(3)->asInteger()->intNumber();
            $timestamp = $children->at(4)->asInteger()->intNumber();
            $var_bind_list = $children->at(5)->asSequence();
        } catch (\OutOfBoundsException|\UnexpectedValueException $e) {
            throw new DecodeError('Trap pdu must be a sequence of [enterprise, agent_address, generic_trap, specific_trap, timestamp, var_bind_list]');
        }

        $bindings = VarBindList::fromASN1($var_bind_list);
        return new self($enterprise, $agent_address, $generic_trap, $specific_trap, $timestamp, $bindings);
    }

}
