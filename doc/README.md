![liquidchaos logo](liquidchaos.svg)

liqiudchaos
===========

liquidchaos is the reference implementation for the chaosnet server browser API.
It is meant to deploy a request-and-response node to be operated within a
standard chaosnet network.

chaosnet is an API designed to facilitate a
self-syncing, heterogenous, distributed database for hosting arbitrary game
server listings to be used by a server browser (originally conceptualized
for [Sonic Robo Blast 2]). For the full chaosnet spec as followed by this
project, see the [chaosnet spec].

[chaosnet spec]: <chaosnet-spec.md>
[Sonic Robo Blast 2]: <https://github.com/STJr/SRB2/>

INSTALLATION
------------

0. You're gonna need the following PHP setup:

   - PHP 8.0 or later
   - EXT_MBSTRING
   - EXT_YAML

   You will also need to set up a MySQL server and configure a connection
   to it in the `env.yaml`.

1. Simply clone the repository: `https://github.com/zibonbadi/liquidchaos.git`
2. Then install the necessary dependencies: `composer install`
3. Run the initial configuration `php liquidchaos init`
4. Configure your database using the newly created user `liquidroot`.

   **WARNING:** ALTHOUGH WE PROVIDE A DEFAULT USER FOR INITIAL
   CONFIGURATION, WE STRONGLY RECOMMEND YOU TO CREATE A SEPERATE
   ADMINISTRATION USER DURING SETUP DESTROY IT AFTERWARDS TO AVOID
   POTENTIAL SECURITY BREACHES. THE PRIVACY AND SECURITY OF YOUR PEERS AND
   THE ENTIRE CHAOSNET NETWORK DEPEND ON IT!

5. Check your `env.yaml` settings
6. Run `php liquidchaos serve`


USAGE/DEPLOYMENT
----------------

To start the server, simply run the following command from within this
repository:

```
php liquidchaos serve
```

CONFIGURATION
-------------

The most important configuration will be located within the file
`env.yaml`. From here you can define the following properties:

```YAML
---
# This is an example env.yaml for a liquidchaos node environment
db:
   # Database configuration (WIP)
peers: # Peers to send requests to
- "http://URL1"
- "https://URL2"
- "etc."
...
```

More security-sensitive configuration, such as user management will have to
be done through the database. For that we provide a tool for easily, but
manually registering users and their public keys:

```
php liquidchaos user:register 
```

**WARNING:** ALTHOUGH WE PROVIDE A DEFAULT USER FOR INITIAL CONFIGURATION,
WE STRONGLY RECOMMEND YOU TO ALTER THESE LOGIN CREDENTIALS TO AVOID
POTENTIAL SECURITY BREACHES. THE PRIVACY AND SECURITY OF YOUR PEERS AND THE
ENTIRE CHAOSNET NETWORK DEPEND ON IT!

