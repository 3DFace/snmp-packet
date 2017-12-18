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

abstract class AbstractBasicPDU implements PDU
{

    /** @var int */
    private $request_id;
    /** @var int */
    private $error_status;
    /** @var int */
    private $error_index;
    /** @var VarBindList */
    private $variable_bindings;

    /**
     * @param int $request_id
     * @param int $error_status
     * @param int $error_index
     * @param VarBindList $variable_bindings
     */
    public function __construct(
        int $request_id,
        int $error_status,
        int $error_index,
        VarBindList $variable_bindings
    ) {
        $this->request_id = $request_id;
        $this->error_status = $error_status;
        $this->error_index = $error_index;
        $this->variable_bindings = $variable_bindings;
    }

    public function getRequestId(): int
    {
        return $this->request_id;
    }

    public function getErrorStatus(): int
    {
        return $this->error_status;
    }

    public function getErrorIndex(): int
    {
        return $this->error_index;
    }

    public function getVariableBindings(): VarBindList
    {
        return $this->variable_bindings;
    }

    public function equals(PDU $x): bool
    {
        return $x instanceof static
            && $x->request_id === $this->request_id
            && $x->error_status === $this->error_status
            && $x->error_index === $this->error_index
            && $x->variable_bindings->equals($this->variable_bindings);
    }

    public function toBinary(): string
    {
        $asn1 = $this->toASN1();
        return $asn1->toDER();
    }

    abstract protected static function getTag(): int;

    public function toASN1(): ImplicitlyTaggedType
    {
        $seq = new Sequence(
            new Integer($this->request_id),
            new Integer(0),
            new Integer(0),
            $this->variable_bindings->toASN1()
        );
        return new ImplicitlyTaggedType(static::getTag(), $seq);
    }

    /**
     * @param TaggedType $obj
     * @return static
     * @throws DecodeError
     */
    public static function fromASN1(TaggedType $obj): self
    {
        try {
            $sequence = $obj->asImplicit(Element::TYPE_SEQUENCE, static::getTag())->asSequence();
            if (\count($sequence) !== 4) {
                throw new DecodeError('PDU must be a sequence of [request_id, error, error_index, var_bind_list]');
            }
            $req_id = $sequence->at(0)->asInteger()->intNumber();
            $error_status = $sequence->at(1)->asInteger()->intNumber();
            $error_index = $sequence->at(2)->asInteger()->intNumber();
            $var_bind_list = $sequence->at(3)->asSequence();
        } catch (\OutOfBoundsException|\UnexpectedValueException $e) {
            throw new DecodeError('PDU must be a sequence of [request_id, error, error_index, var_bind_list]');
        }

        $bindings = VarBindList::fromASN1($var_bind_list);
        return new static($req_id, $error_status, $error_index, $bindings);

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
