Botphobic
==========

Botphobic is an antibot extension for [FluxBB](https://github.com/fluxbb/fluxbb) register form.

![image](https://www.crachecode.net/botphobic/screenshot.png)

## Features

Botphobic implements 4 different tests that are all individually activable and non intrusive :

* **_Encrypted timestamp_** will check that 4 seconds to 2 hours have elapsed during the form filling.
* **_Honeypot_** ensures that some hidden inputs will not be filled.
* **_Javascript_** checks that Javascript is activated on the browser side.
* **_Cookies_** checks that the user's browser allows cookies.

## Requirements

_Encrypted timestamp_ test requires OpenSSL installed on the server.

Botphobic has been tested on FluxBB 1.5.10 with PHP 5.5 and PHP 7.0.

## Installation

Just copy the two files from _plugins_ and _addons_ in the same directories in your FluxBB installation.

## Usage

Simply activate the tests you want to perform in FluxBB administration panel : _Plugins menu_ -> _Botphobic_.

Here you can also write a custom error message.
