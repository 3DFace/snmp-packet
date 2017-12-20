<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

class GetNextRequestPDU extends AbstractBasicPDU
{

    public const TAG = 1;

    public static function getBasicTag(): int
    {
        return self::TAG;
    }

}
