<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

use ASN1\Element;

interface DataType
{

    /**
     * @param DataTypeVisitor $visitor
     * @return mixed
     */
    public function acceptVisitor(DataTypeVisitor $visitor);

    /**
     * @param $val
     * @return bool
     */
    public function equals($val): bool;

    /**
     * @return Element
     */
    public function toASN1(): Element;

}
