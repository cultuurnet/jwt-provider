# JWT Provider
[![Packagist](https://img.shields.io/packagist/v/cultuurnet/jwt-provider.svg?maxAge=2592000?style=flat-square)](https://github.com/cultuurnet/jwt-provider)
[![Travis](https://img.shields.io/travis/cultuurnet/jwt-provider.svg?maxAge=2592000?style=flat-square)](https://github.com/cultuurnet/jwt-provider)
[![Coveralls](https://img.shields.io/coveralls/cultuurnet/jwt-provider.svg?maxAge=2592000?style=flat-square)](https://github.com/cultuurnet/jwt-provider)

Silex application that provides JSON Web Tokens

## CLI commands

### Generating tokens

```bash
$ ./bin/app.php jwt:generate <uid> <nickname> <email> <uitid-token> <uitid-secret>
```

Example:

```bash
$ ./bin/app.php jwt:generate 1 bert2dotstwice bert@2dotstwice.be 1234567 AZERTY
    
eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOiIxIiwibmljayI6ImJlcnQyZG90c3R3aWNlIiwiZW1haWwiOiJiZXJ0QDJkb3RzdHdpY2UuYmUiLCJ0b2tlbiI6IjEyMzQ1NjciLCJzZWNyZXQiOiJBWkVSVFkiLCJpc3MiOiJodHRwOlwvXC9jdWx1ZGItand0LXByb3ZpZGVyLmRldiIsImV4cCI6MTQ2MTc3NjE1NSwibmJmIjoxNDYxNzcyNTU1fQ.f32PejGZRqIRsN__FC88asBCJFUhRcr2DZwfQGjLA05Gwhio3Ney3wWeXN_GFroTfl5ONhqMi4N1gqOlULvv-2GjC4yP2IPtTiEQfVNmMpgP1BXEN7NEy9-axgRsMCii6qZwTbKs09Q0GJg0FU7nlyf4PLlBb2gkPzp7qttZ9vz0RhTjYvaaGW_kDdhZ4Zah1Go416zAM_cTMkhF_BAeNHLa2Y3t9qFW_UHhPxqDOufThmzJDPoTfqLN1WpleSXDCEROQUErdqLsdUPWD2WIkYC3VIewR5OGjPn3zlKZ-vfPHALD1bXn3guO3wm2Bo6pWjlmgKLRCXcgJPd522LN5w
```

### Decoding, validating, and verifying tokens

```bash
$ ./bin/app.php jwt:decode <token>
```
    
Example:

```bash
$ ./bin/app.php jwt:decode eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOiIxIiwibmljayI6ImJlcnQyZG90c3R3aWNlIiwiZW1haWwiOiJiZXJ0QDJkb3RzdHdpY2UuYmUiLCJ0b2tlbiI6IjEyMzQ1NjciLCJzZWNyZXQiOiJBWkVSVFkiLCJpc3MiOiJodHRwOlwvXC9jdWx1ZGItand0LXByb3ZpZGVyLmRldiIsImV4cCI6MTQ2MTc3NTIyMywibmJmIjoxNDYxNzcxNjIzfQ.Ep-V2UtAWEOgK_4DWB1VzS4nHMOb1yRnLGH0A9q1W3TB984Ob_US5E_Fg_aPv5ypbplXJvLaZDWosWA8qOnG51uT1twxnugkdJ2NeivYGxvcpd9KrXs2So65deQNXmAHWAoBEaJsYzUtub8-clKGboRRl724mpzsvwssLqP2tDtjtMP7gvb6bqTFsNh7gGfm0Rxn8Ct_cTsjpqWmHg9-FnYJSc-o9fs0HhBOKPzdzbUZMPNcIY6G7tZddkHHTNrW8ISy8dXwHLsToHs4vBgRsjjqDkDKKk5Fp7JmRdb0ElFJBm_suuzvWCSpxWErFwYjq_S5nyoqsWUs6i98s-qRIA
    
uid: 1
nick: bert2dotstwice
email: bert@2dotstwice.be
token: 1234567
secret: AZERTY
iss: http://culudb-jwt-provider.dev
exp: 1461775223
nbf: 1461771623
Valid: ✓
Signature verification: ✓
```
