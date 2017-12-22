<?php
/* author: Ponomarev Denis <ponomarev@gmail.com> */

namespace dface\SnmpPacket\Examples;

use dface\SnmpPacket\DataType\NullValue;
use dface\SnmpPacket\DataType\Oid;
use dface\SnmpPacket\Exception\DecodeError;
use dface\SnmpPacket\Message\MessageV1;
use dface\SnmpPacket\PDU\GetRequestPDU;
use dface\SnmpPacket\PDU\ResponsePDU;
use dface\SnmpPacket\VarBind\VarBind;
use dface\SnmpPacket\VarBind\VarBindList;

include_once __DIR__ . '/../vendor/autoload.php';

class NaiveSnmpGet
{

    /** @var string */
    private $agent_ip;
    /** @var string */
    private $community;

    public function __construct(string $agent_ip, string $community)
    {
        $this->agent_ip = $agent_ip;
        $this->community = $community;
    }

    /**
     * @param string $oid
     * @return array
     * @throws \RuntimeException
     */
    public function getOne(string $oid): array
    {
        return $this->getMany([$oid])[$oid];
    }

    /**
     * @param string[] $oid_list
     * @return array
     * @throws \RuntimeException
     */
    public function getMany(array $oid_list): array
    {
        $request = $this->prepareRequest($oid_list);
        $response = $this->queryOverUDP($request);
        return $this->processResponse($response);
    }

    private function prepareRequest(array $oid_list): string
    {
        $bind_arr = array_map(function (string $oid) {
            return new VarBind(new Oid($oid), new NullValue());
        }, $oid_list);

        $pdu = new GetRequestPDU(1, 0, 0, new VarBindList(...$bind_arr));

        $message = new MessageV1(1, $this->community, $pdu);

        return $message->toBinary();
    }


    /**
     * @param string $response
     * @return array
     * @throws \RuntimeException
     */
    private function processResponse(string $response): array
    {
        try {
            $message = MessageV1::fromBinary($response);
        } catch (DecodeError $e) {
            throw new \RuntimeException('Cant decode agent response');
        }

        /** @var ResponsePDU $pdu */
        $pdu = $message->getPdu();

        if ($err_status = ($pdu->getErrorStatus() !== 0)) {
            throw new \RuntimeException('GetRequest failed, error_status=' . $err_status . ', error_index=' . $pdu->getErrorIndex());
        }

        $bindings = $pdu->getVariableBindings()->getList();

        /**
         * @see DataTypeToString in examples
         */
        $converter = new DataTypeToString();

        $result = [];

        foreach ($bindings as $var_bind) {
            $val = $var_bind->getValue();
            $result[$var_bind->getOid()->getValue()] = $val->acceptVisitor($converter);
        }

        return $result;
    }

    private function queryOverUDP(string $request): string
    {
        // send request and fetch response
        $socket = \socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        try {
            \socket_connect($socket, $this->agent_ip, 161);
            \socket_write($socket, $request);
            return \socket_read($socket, 65535);
        } finally {
            \socket_close($socket);
        }
    }

}

$getter = new NaiveSnmpGet('10.0.114.96', 'public');

print_r($getter->getMany([
    '1.3.6.1.2.1.1.3.0',    // up time
    '1.3.6.1.2.1.1.2.0',    // mac address
    '1.3.6.1.2.1.1.9999.0', // no such object
]));

print_r($getter->getOne('1.3.6.1.2.1.1.3.0'));
