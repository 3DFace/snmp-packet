<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

class TimeTicks extends AbstractUnsigned32
{

    public const TAG = 3;

    protected static function getTag(): int
    {
        return self::TAG;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitTimeTicks($this->value);
    }

}
