<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

class Gauge32 extends AbstractUnsigned32
{

    public const TAG = 2;

    protected static function getTag(): int
    {
        return self::TAG;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitCounter32($this->value);
    }

}
