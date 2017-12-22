[![Build Status](https://travis-ci.org/3DFace/snmp-packet.svg?branch=master)](https://travis-ci.org/3DFace/snmp-packet)

# SnmpPacket

> **Note:** This is an early development version.

A PHP library to encode/decode SNMP packets.

This library does not aim to implement SNMP protocol processing.
It's just for decoding SNMP messages from binary strings into PHP-objects and vice versa. 

Supports SNMP messages v1, v2c and v3.

### Installation

`composer require dface/snmp-packet`

### Usage

Example of naive `snmpget` command can be found in `./examples/NaiveSnmpGet.php`.
Take a look at `prepareRequest()` to see how to construct/encode messages.
And `processResponse()` to see how to decode/process them. 

Simple example of message encoding:
```php
// construct pdu:
$bindings = new VarBindList(
   new VarBind(new Oid('1.3.6.1.2.1.1.3.0'), new NullValue())
); 
$pdu = new GetRequestPDU(1, 0, 0, $bindings);

// pack into the message:
$message = new MessageV1(1, $this->community, $pdu);

// take a binary to send it somewhere:
$bin = $message->toBinary();
```

Simple example of message decoding:
```php
// decode message from binary:
$message = MessageV1::fromBinary($bin);

// take pdu:
$pdu = $message->getPdu();

//check on errors:
$err = $pdu->getErrorStatus();

// iterate over bindings:
$bindings = $pdu->getVariableBindings()->getList();
foreach ($pdu as $var_bind) {
    printf("%s: %s\n", $var_bind->getOid(), $var_bind->getValue());
}

```
