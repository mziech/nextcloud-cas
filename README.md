# CAS Server

This is an very basic and experimental implementation of the CAS server protocol for NextCloud.
It is not another user backend for NextCloud, instead it allows you to authenticate 3rd-party or custom web applications
against the NextCloud user database.

**Use at your own risk!**

Protocol specification:
https://apereo.github.io/cas/4.2.x/protocol/CAS-Protocol-Specification.html

## Supported Features

- Simple login using CAS 1.0/2.0/3.0 protocol
- Expose basic user attributes via `/p3/serviceValidate` (CAS 3.0)
  - `displayName`
  - `email`
  - `memberOf`
- Basic access control: Admin UI allows restricting services to certain groups

## Unsupported Features

Basically everything else.
- `/proxy` and `/proxyValidate` 
- `renew` parameter for login
- `/samlValidate`
- `/logout`
- Single Sign-Out
- Remember me (CAS 3.0)

## Install

Place the contents of this repository in `nextcloud/apps/cas`
