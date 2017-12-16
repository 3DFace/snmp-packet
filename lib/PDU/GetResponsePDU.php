<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\PDU;

class GetResponsePDU extends AbstractBasicPDU
{

    public const TAG = 2;

    public static function getTag(): int
    {
        return self::TAG;
    }

}
