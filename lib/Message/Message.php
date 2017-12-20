<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Message;

interface Message
{

    public const V1 = 0;
    public const V2C = 1;
    public const V3 = 3;

    public function getVersion();

}
