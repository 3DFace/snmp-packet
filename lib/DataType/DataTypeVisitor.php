<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\DataType;

interface DataTypeVisitor
{

    /**
     * @param int $value
     * @return mixed
     */
    public function visitInteger32(int $value);

    /**
     * @param string $value
     * @return mixed
     */
    public function visitOctetString(string $value);

    /**
     * @param string $value
     * @return mixed
     */
    public function visitOid(string $value);

    /**
     * @param string $value
     * @param int $unused_bits
     * @return mixed
     */
    public function visitBitString(string $value, int $unused_bits);

    /**
     * @return mixed
     */
    public function visitNull();

    /**
     * @param string $ipString
     * @return mixed
     */
    public function visitIpAddress(string $ipString);

    /**
     * @param string $octetString
     * @return mixed
     */
    public function visitNsapAddress(string $octetString);

    /**
     * @param string|int $value
     * @return mixed
     */
    public function visitCounter32($value);

    /**
     * @param string|int $value
     * @return mixed
     */
    public function visitCounter64($value);

    /**
     * @param int $value
     * @return mixed
     */
    public function visitGauge32($value);

    /**
     * @param string|int $value
     * @return mixed
     */
    public function visitUInteger32($value);

    /**
     * @param int $value
     * @return mixed
     */
    public function visitTimeTicks($value);

    /**
     * @param string $value
     * @return mixed
     */
    public function visitOpaque(string $value);

    /**
     * @return mixed
     */
    public function visitNoSuchObject();

    /**
     * @return mixed
     */
    public function visitNoSuchInstance();

    /**
     * @return mixed
     */
    public function visitEndOfMibView();

}
