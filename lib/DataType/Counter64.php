<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

class Counter64 extends AbstractUnsigned32
{

    public const TAG = 6;

    protected static function getTag(): int
    {
        return self::TAG;
    }

    public function acceptVisitor(DataTypeVisitor $visitor)
    {
        return $visitor->visitCounter64($this->value);
    }

}
