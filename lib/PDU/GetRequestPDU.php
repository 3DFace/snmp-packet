<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

class GetRequestPDU extends AbstractBasicPDU
{

    public const TAG = 0;

    public static function getTag(): int
    {
        return self::TAG;
    }

}
