# JWT Provider
[![Packagist](https://img.shields.io/packagist/v/cultuurnet/jwt-provider.svg?maxAge=2592000?style=flat-square)](https://github.com/cultuurnet/jwt-provider)
[![Travis](https://img.shields.io/travis/cultuurnet/jwt-provider.svg?maxAge=2592000?style=flat-square)](https://github.com/cultuurnet/jwt-provider)
[![Coveralls](https://img.shields.io/coveralls/cultuurnet/jwt-provider.svg?maxAge=2592000?style=flat-square)](https://github.com/cultuurnet/jwt-provider)

Silex application that provides JSON Web Tokens

## CLI commands

### Encoding tokens

```bash
$ ./bin/app.php jwt:encode <uid> <nickname> <email>
```

Example:

```bash
$ ./bin/app.php jwt:encode 1 foo foo@bar.com
    
eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9jdWx1ZGItand0LXByb3ZpZGVyLmRldiIsInVpZCI6IjEiLCJuaWNrIjoiZm9vIiwiZW1haWwiOiJmb29AYmFyLmNvbSIsImV4cCI6MTQ2MjIwNjk5NywibmJmIjoxNDYyMjAzMzk3fQ.Agb_I2JYyjy2algqi2KxmvDHIUPC5PtYb2bHjWODO9LYxfpy5XCsl9tL8znXUri2mL5yPLd-AIIZ60JLhCL5fU6nREjF16kYrZ28KknOZxam9iYPHhas4KWf8m3e3iaxlQ9iPkDiYPmjGwUdIJZ_Jh5vG4d_83mWgKW2pk_vD64YDaBZ9RmFEvvALNiFaDbgnKMT777SA2dA-DymIIrFeojzBxntsk3oCpzm3S-UGgFKlEYMkEi8IQblXEUNH9bLbeE1GgYAtEIkBf5OhqoQmrrvbTkYJecyNfqqGOIPCiPUJ0mQlgw89m-nSWms6OkGhNwsXt4-nhO1Nc9r5vmR3Q
```

### Decoding, validating, and verifying tokens

```bash
$ ./bin/app.php jwt:decode <token>
```
    
Example:

```bash
$ ./bin/app.php jwt:decode eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9jdWx1ZGItand0LXByb3ZpZGVyLmRldiIsInVpZCI6IjEiLCJuaWNrIjoiZm9vIiwiZW1haWwiOiJmb29AYmFyLmNvbSIsImV4cCI6MTQ2MjIwNjk5NywibmJmIjoxNDYyMjAzMzk3fQ.Agb_I2JYyjy2algqi2KxmvDHIUPC5PtYb2bHjWODO9LYxfpy5XCsl9tL8znXUri2mL5yPLd-AIIZ60JLhCL5fU6nREjF16kYrZ28KknOZxam9iYPHhas4KWf8m3e3iaxlQ9iPkDiYPmjGwUdIJZ_Jh5vG4d_83mWgKW2pk_vD64YDaBZ9RmFEvvALNiFaDbgnKMT777SA2dA-DymIIrFeojzBxntsk3oCpzm3S-UGgFKlEYMkEi8IQblXEUNH9bLbeE1GgYAtEIkBf5OhqoQmrrvbTkYJecyNfqqGOIPCiPUJ0mQlgw89m-nSWms6OkGhNwsXt4-nhO1Nc9r5vmR3Q
    
iss: http://culudb-jwt-provider.dev
uid: 1
nick: foo
email: foo@bar.com
exp: 1461775223
nbf: 1461771623
Valid: ✓
Signature verification: ✓
```
