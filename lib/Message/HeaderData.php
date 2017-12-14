<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

use ASN1\Exception\DecodeException;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\OctetString;
use ASN1\Type\UnspecifiedType;
use dface\SnmpPacket\Exception\DecodeError;

class HeaderData
{

    /** @var int */
    private $id;
    /** @var int */
    private $max_size;
    /** @var int */
    private $flags;
    /** @var int */
    private $security_model;

    /**
     * @param int $id
     * @param int $max_size
     * @param int $flags
     * @param int $security_model
     */
    public function __construct(int $id, int $max_size, int $flags, int $security_model)
    {
        $this->id = $id;
        $this->max_size = $max_size;
        $this->flags = $flags;
        $this->security_model = $security_model;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMaxSize(): int
    {
        return $this->max_size;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function getSecurityModel(): int
    {
        return $this->security_model;
    }

    public function equals($x): bool
    {
        return $x instanceof self
            && $x->id === $this->id
            && $x->max_size === $this->max_size
            && $x->flags === $this->flags
            && $x->security_model === $this->security_model;
    }

    public function toBinary(): string
    {
        $obj = $this->toASN1();
        return $obj->toDER();
    }

    public function toASN1(): Sequence
    {
        return new Sequence(
            new Integer($this->id),
            new Integer($this->max_size),
            new OctetString(\chr($this->flags)),
            new Integer($this->security_model)
        );
    }

    /**
     * @param string $binary
     * @return HeaderData
     * @throws DecodeError
     */
    public static function fromBinary(string $binary): self
    {
        try {
            $seq = UnspecifiedType::fromDER($binary)->asSequence();
        } catch (\UnexpectedValueException|DecodeException $e) {
            throw new DecodeError('Cant decode HeaderData: ' . $e->getMessage(), 0, $e);
        }
        return self::fromASN1($seq);
    }

    /**
     * @param Sequence $seq
     * @return HeaderData
     * @throws DecodeError
     */
    public static function fromASN1(Sequence $seq): self
    {
        if (\count($seq) !== 4) {
            throw new DecodeError('HeaderData must be a sequence of [id, max_size, flags, security_model]');
        }
        try {
            $id = $seq->at(0)->asInteger()->intNumber();
            $max_size = $seq->at(1)->asInteger()->intNumber();
            $flags_str = $seq->at(2)->asOctetString()->string();
            $flags = \ord($flags_str[0]);
            $security_model = $seq->at(3)->asInteger()->intNumber();
        } catch (\OutOfBoundsException|\UnexpectedValueException $e) {
            throw new DecodeError('HeaderData must be a sequence of [id, max_size, flags, security_model]', 0, $e);
        }
        return new self($id, $max_size, $flags, $security_model);
    }

}
