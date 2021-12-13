![chaosnet logo](chaosnet.svg)

chaosnet specification draft (rev. 0.3.1)
=======================================

An API proposal draft for an HTTP-based game
server browser master server network

Publication
-----------

This draft has been conceptualized by Zibon "PixL" Badi and "Wane" and
written by Zibon "PixL" Badi. It was first released to the public on
2021-11-12.


Introduction
------------

> It is perfectly possible to create your own master server for people to
> advertise games on. People who want to make an MS where people are
> allowed to host unauthorized ports are free to do so.

- [SeventhSentinel (2021-10-18).][sevquote] [(Archive)][archivelink]

[sevquote]: <https://mb.srb2.org/threads/about-portlegs-and-the-ms-rules.33560/post-552101>
[archivelink]: <https://web.archive.org/web/20211123005547/https://mb.srb2.org/threads/about-portlegs-and-the-ms-rules.33560>

This document defines a proposal for a revised, non-backwards-compatible
SRB2 Master Server HTTP API ("chaosnet"; to be changed upon approval).
Target of this API is to extend the currently applied HTTP API ("V1") with
the following features:

1. Improve security by providing authentication checks to the server
   registration logic.
2. Streamline HTTP routes in order to ease implementation of chaosnet and
   other software written against chaosnet. Backwards compatibility to V1
   will be dropped.
3. Restructure request and response data and routes in order to comply with
   universally applied practice for web services according to the model of
   representational state transfer ([REST]).
4. Streamline server listing data in order to be applicable to hosting
   heterogeneous listings of arbitrary game servers.
5. Introduce synchronization capabilities between running implementations
   of chaosnet ("nodes") to enable chaosnet to be operated as a distributed
   network system following the principle of eventual consistency using
   data redundancy and a flooding-based update propagation algorithm.

[REST]: <https://en.wikipedia.org/w/index.php?title=Representational_state_transfer&oldid=1051540340>

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT",
"SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this
specification are to be interpreted as described in [RFC2119].

[RFC2119]: <https://datatracker.ietf.org/doc/html/rfc2119>

Routing and requests
--------------------

Each route of chaosnet will be defined by an HTTP URL compliant to
[RFC1738]. For brevity, this document will list only the `<path>` and
`<searchpart>` of it's routes, starting with `/` signifying the server's
root resource. All routes in this document will be noted in BNF syntax.

[RFC1738]: <https://datatracker.ietf.org/doc/html/rfc1738>

The following table defines the routes that must be used by a chaosnet
compliant node. Each route will be explained in it's own dedicated
subsection, labeled as noted here:

Route                 | Method(s)   | Description
----------------------|-------------|---------------------------------
`/servers?<filters>`  | GET         | List servers; filters optional
`/servers`            | POST \| PUT | Add/update a server.
`/servers/<host>`     | DELETE      | Remove a server from the network
`/auth`               | GET \| POST | Authentication mechanism. Recommended implementation explained in the section "Recommended authentication algorithm".


Server list queries
-------------------

A GET request to the route `/servers` shall be used to query a node's
server database. Nodes compliant to chaosnet must by default return MIME
type `text/csv;header=absent;charset=UTF-8` as defined in [RFC4180].
Additional formats may be returned in data types specified by the HTTP
header field `Accept`. Support for other commonly used data interchange
formats such as `application/x-yaml`, `application/json` or `text/html` is
recommended, which will be explained in the following sections.

[RFC4180]: <https://datatracker.ietf.org/doc/html/rfc4180>

If a node is unable to return any of the desired formats, it shall respond
empty with a 404 response code (resource not found). Nodes should be aware
of potential responses in other encoding schemes. Conventions for CSV
formatting concerns such as delimiter, newline and enclosure characters
shall follow [RFC4180] (comma, CRLF and double quotes repectively). The
response structure for possible tabular and index-based response formats
are explained in the section "Server data specification".

Each request may specify an arbitrary amount of filters in it's searchpath.
The returned listings shall originate from the intersection of all applied
filtering criteria, or in other words: All specified filter conditions
shall be interpreted as connected through a logical AND. Each compliant
node must support at least the following fields as filter arguments:

`host=<STRING>`
: Filter by the host string containing the input. To be discarded upon invalid input.

`game=<STRING>`
: Filter by the game string containing the input. To be discarded upon invalid input.

`version=<STRING>`
: Filter by the game version string containing the input. To be discarded upon invalid input.

`name=<STRING>`
: Filter by the server name string containing the input. To be discarded upon invalid input.

`meta=<STRING>`
: Filter by the meta contents string containing the input. To be discarded upon invalid input.

`count=<NUMBER>`
: Only return the specified numeric amount of entries. To be discarded upon invalid input.

`page=<NUMBER>`
: Page count. Show the n-th chunk of data divided by `count`. To be discarded upon invalid input.

If a node is unable or unwilling to provide a response with the amount of
entries specified by the requester (most likely for security), it shall
respond empty with a 404 response code.


Server data specification
-------------------------

### Table response format (CSV) 

For tabular response formats, the table returned by
the supplier is defined by the following fields, in order in case the
response format does not contain any mechanism of labeling the fields:

Field    | Type   |  Max | Description
---------|--------|------|------------------------------------------------------
host     | string |  512 | Host address (e.g. localhost:5029, 127.0.0.1:5029)
game     | string |  128 | Game name ( default: "Sonic Robo Blast 2" )
version  | string |   32 | Game version number (e.g. "2.2.9")
name     | string |  128 | Server name (e.g. "SRB2 Server")
meta     | string | 4096 | Game specific metadata (e.g.  "mods=srb2ware,sf94")

### Index response format (XML/YAML/JSON )

Index style responses should consist of a document specifying a flat list
of server entries. Each entry shall contain the list of fields specified
within the response field table as indices, example:

```XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<serverlist>
   <server>
      <host>localhost:5029</host>
      <game>Sonic Robo Blast 2</game>
      <version>2.2.9</version>
      <name>SRB2 Server</name>
      <meta>files=VCL_LegacyHinote-v3.5.wad,CL_Skip-v1.pk3,VL_BattleMod-v1.1.pk3;mod=Battlemod V1.1</meta>
   </server>
</serverlist>
```

Should an entry be unable to provide named indices (e.g. numbered arrays),
their structure shall follow the sequence given in the response field
table, top-to-bottom:

```YAML
--- # YAML example. JSON below
- host: "localhost:5029"
  game: "Sonic Robo Blast 2"
  version: "2.2.9"
  name: "SRB2 Server"
  meta: "files=VCL_LegacyHinote-v3.5.wad,CL_Skip-v1.pk3,VL_BattleMod-v1.1.pk3;mod=Battlemod V1.1"
- [ "localhost", "Sonic Robo Blast 2", "2.2.9", 0, "SRB2 Server", "files=VCL_LegacyHinote-v3.5.wad,CL_Skip-v1.pk3,VL_BattleMod-v1.1.pk3;mod=Battlemod V1.1" ]
...
```

```JSON
[
   {
      "host": "localhost:5029",
      "game": "Sonic Robo Blast 2",
      "version": "2.2.9",
      "name": "SRB2 Server"
      "meta": "files=VCL_LegacyHinote-v3.5.wad,CL_Skip-v1.pk3,VL_BattleMod-v1.1.pk3;mod=Battlemod V1.1"
   },
   [ "localhost", "Sonic Robo Blast 2", "2.2.9", 0, "SRB2 Server", "files=VCL_LegacyHinote-v3.5.wad,CL_Skip-v1.pk3,VL_BattleMod-v1.1.pk3;mod=Battlemod V1.1" ]
]
```


### Server Host

The field "host" defines a full network address under which the listed game
server is reachable, agnostic of protocol. Should for example a TCP/IP
based network address ([RFC791], [RFC8200], [RFC793], [RFC7323]) be used in
for server listing, it shall be left up to the game client to interpret the
omission of a default port.  This field must contain any control characters
such as line feed (LF), carriage return (CR), end of transmission (EOT)
etc. in it's original form and shall be parsed in a way appropriate to it's
output format in order to avoid syntax collision errors. The "host" field
must not exceed it's maximum length of 512 characters in it's original form.

[RFC791]: <https://tools.ietf.org/html/rfc791>
[RFC793]: <https://tools.ietf.org/html/rfc793>
[RFC7323]:<https://tools.ietf.org/html/rfc7323>
[RFC8200]: <https://tools.ietf.org/html/rfc8200>


### Game and version information

The fields "game" and "version" define the name and version of the hosted
game in unmodified form. These fields must not contain any control
characters such as line feed (LF), carriage return (CR), end of
transmission (EOT) etc. in their original form and shall be parsed in a way
appropriate to it's output format in order to avoid syntax collision
errors. The original forms of these fields may not exceed a maximum length
of 128 for "game" and 32 characters "version" respectively.


### Server Name

The field "name" is reserved for an arbitrary string with maximum length of
128 characters in it's original form, signifying the server name. These
fields must not contain any control characters such as line feed (LF),
carriage return (CR), end of transmission (EOT) etc. in their original form
and shall be parsed in a way appropriate to it's output format in order to
avoid syntax collision errors. 


### Server metadata

The field "meta" is reserved for textual metadata to be interpreted by the
game client. It must not exceed a maximum length of 4096 characters nor
contain any control characters such as line feed (LF), carriage return
(CR), end of transmission (EOT) etc. in it's original form and shall be
parsed in a way appropriate to it's output format in order to avoid syntax
collision errors. The recommended syntax reserves the characters semicolon
(";"), comma (","), equal sign ("=") and backslash ("\\") and looks roughly
like this:

    meta1=arg1,\"arg2 string\",arg3\=value;meta2=arg;meta3

This optional syntax defines a series of unique keys, each being separated
by a semicolon. The trailing semicolon of the last key in the list
may be omitted. Each key may be immediately followed by an equal sign
to define a series of values separated by comma, terminated by the
key's semicolon separator.

Valueless keys not followed by an equal sign may be interpreted as switches
by the client, defining explicit use of the designated option. Any
character preceded by a backslash must be interpreted as part of the
intended data, escaping the reserved syntax characters.


Server list management & propagation
------------------------------------

> No Master Server, no rules. Host whatever you want.
> We can't possibly moderate that.

- [SeventhSentinel (2021-10-18).][sevquote] [(Archive)][archivelink]

This section defines the algorithm for server entry management and
constructing a distributed server listing network through propagation of
database changes as well as reference the required security mechanisms and
principles needed for such an to be successfully compliant to chaosnet.

Since game clients and servers are to be treated by the propagation
algorithm as request-only nodes, all mention of the word "node" also apply
to these implementations, excluding the need for subsequent propagation.

### Registering servers

To register a server on a node, nodes shall send a PUT request to the route
`/servers` featuring the following parameters:

Field    | Description
---------|----------------------------------------------------------------------
host     | Host address (e.g. localhost:5029, 127.0.0.1:5029)
name     | Server name (e.g. SRB2%20Server)
game     | Game name ( default: Sonic%20Robo%20Blast%202 )
version  | Game version number (e.g. 2.2.9)
token    | OAuth2 Auth token (explained in "Recommended authentication algorithm")

: Required POST request server entry data structure

Upon successful upsert, the responding node will respond with an HTTP
response code of either 201 (Created) or 200 (No Content), with the
accompanying header field of `Content-Location` defined as a server list
query appropriate for fetching a 1 entry long server list consisting solely
of the upserted server listing. Using the registered host as a unique
identifier for each server entry is recommended.

Upon failed authorization/authentication, the responder node shall respond
with an HTTP code of 401 (Unauthorized).

If the requested update to the server listing does not differ from the one
already registered to the node, the responding node must not propagate the
requested change and must update the entry's TTL (time to live in order to
avoid redundant registrations within network loops. 


### Canceling servers

To cancel a server on a node, game servers and nodes shall send a DELETE
request to the route `/servers` featuring a searchpart that uniquely
matches the desired server. This request must be checked by the responder
for authenticity and authorization of the requester to delete that listing.
Only the immediate node from which the server entry originated shall be
authorized to explicitly delete the entry.

Upon failed authorization/authentication, the responder node shall respond
with an HTTP code of 401 (Unauthorized).

Upon successful deletion, chaosnet nodes shall propagate a subsequent
DELETE request all of their immediate peers, just as defined above to flood
the network. If no deletion was executed, propagation must not be executed.


Managing OAuth2 tokens with `/auth`
-----------------------------------

In order to circumvent false listings and man-in-the-middle attacks, nodes
must authenticate themselves during server management operations. Since
chaosnet is meant to be operated in a heterogeneous distributed
peer-to-peer network of differing and potentially malicious nodes,
implementations shall rely on peer-based authentication checks on behalf of
the supplier to accept or reject requests, creating a web-of-trust
architecture throughout the network. Game clients and servers will be
treated as request-only nodes for practicality.

Consumers will have to verify their legitimacy to the responder for
initial registration as a trusted peer. Once trust has been established,
the responder shall provide the requester with a newly-generated pair of
unique OAuth2 access and refresh tokens, as compliant to [RFC6749].

[RFC6749]: <https://datatracker.ietf.org/doc/html/rfc6749>

### Recommended authentication algorithm

The following authentication sequence applies to both initial as well as
subsequent requests for refreshing OAuth2 tokens to be used for server
management. It is only recommended as it represents the current state of
security research and may be replaced by more secure solutions in the future.

0. The requester creates an asymmetric key pair and registers the
   corresponding public key alongside a user ID on the responder. This part
   of the process is intentionally kept ambiguous here as it is
   fundamentally insecure to automate this process.
1. The requester requests a one-time verification token from the responder
   using a POST request on the route `/auth/generate` containing the
   requester's user ID within the `user` parameter of the request body.
   The responder shall respond with a random, unique and meaningless
   one-time token registered to the user.
2. The received one-time token is encrypted using the requester's private
   key, before being sent as the value of the parameter `token` within a
   POST request towards `/auth/verify`.
3. The responder decrypts the received token and checks it's integrity.
   If the token matches the previously sent one, the requester has
   successfully verified itself using their private key and an OAuth2 token
   pair shall be supplied.
