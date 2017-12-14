<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

class UInteger32 extends AbstractUnsigned32
{

    public const TAG = 7;

    protected static function getTag(): int
    {
        return self::TAG;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitUInteger32($this->value);
    }

}
