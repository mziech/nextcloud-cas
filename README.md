![.github/workflows/release.yml](https://github.com/mziech/nextcloud-cas/workflows/.github/workflows/release.yml/badge.svg)

# CAS Server

This is an very basic and experimental implementation of the CAS server protocol for NextCloud.
It is not another user backend for NextCloud, instead it allows you to authenticate 3rd-party or custom web applications
against the NextCloud user database.

**Use at your own risk!**

Protocol specification:
https://apereo.github.io/cas/4.2.x/protocol/CAS-Protocol-Specification.html

## Supported Features

- Simple login using CAS 1.0/2.0/3.0 protocol
- Expose basic user attributes via `/serviceValidate` (CAS 2.0 in non-strict mode) and `/p3/serviceValidate` (CAS 3.0)
  - `displayName`
  - `email`
  - `memberOf`
- Expose some extra attributes for compatibility:
  - `commaSeparatedGroups`: same as `memberOf`but as a comma-separated list
  - `dotSpaceUsername`: the NextCloud UID with all spaces replaced by dots
- Service tickets with `/proxyValidate` and `/p3/proxyValidate`
- Basic access control: Admin UI allows restricting services to certain groups

## Unsupported Features

Basically everything else.
- Proxy tickets with `/proxy`, `/proxyValidate` and `/p3/proxyValidate`
- `renew` parameter for login
- `/samlValidate`
- `/logout`
- Single Sign-Out
- Remember me (CAS 3.0)

## Install

This is an experimental app which needs to be installed manually.

1. Download the latest release from 
https://github.com/mziech/nextcloud-cas/releases/latest/download/cas.tar.gz
2. Unpack the archive to the `apps` folder of your Nextcloud installation 
3. Check **Apps / Deactivated Apps** whether it contains **CAS Server**
and activate, if required.
