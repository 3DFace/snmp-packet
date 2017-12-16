<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

class SetRequestPDU extends AbstractBasicPDU
{

    public const TAG = 3;

    public static function getTag(): int
    {
        return self::TAG;
    }

}
