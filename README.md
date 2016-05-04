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
    
eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9jdWx1ZGItand0LXByb3ZpZGVyLmRldiIsInVpZCI6IjEiLCJuaWNrIjoiZm9vIiwiZW1haWwiOiJmb29AYmFyLmNvbSIsImlhdCI6MTQ2MjM2NDM4NCwiZXhwIjoxNDYyMzY3OTg0LCJuYmYiOjE0NjIzNjQzODR9.o4r8fWtqmK89Cs4ZqapaAoOWw2XA98RSUNrRYZq63MEJNEJ1sU1HHH9luN1g8Rj3rIZkHv1cSVYsL_O4oQy-_l4-CmdQf57_r86yJnVnaejz9TDTLXRUI6ImCSOkbWTnDbTZQpKXKXclKGQ4jFdHnNNDNL5thBAeO0AqEuR4wUNlIDy7xt0tnbzUso1IWf7X_S9EhV6iEIk4aqyMEzwt0n6geOJ13mCJQLok87xVsqACtpIS-n60KCR4CzivJRNM33re-CGtlRO6JcCkjRggDiVC5k6zFZoKycKjXXsZ-1sND5d8OEx3rcn59qdPhsP9tTdFxa-98Ps4pN-rrdj7ow
```

### Decoding, validating, and verifying tokens

```bash
$ ./bin/app.php jwt:decode <token>
```
    
Example:

```bash
$ ./bin/app.php jwt:decode eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9jdWx1ZGItand0LXByb3ZpZGVyLmRldiIsInVpZCI6IjEiLCJuaWNrIjoiZm9vIiwiZW1haWwiOiJmb29AYmFyLmNvbSIsImlhdCI6MTQ2MjM2NDM4NCwiZXhwIjoxNDYyMzY3OTg0LCJuYmYiOjE0NjIzNjQzODR9.o4r8fWtqmK89Cs4ZqapaAoOWw2XA98RSUNrRYZq63MEJNEJ1sU1HHH9luN1g8Rj3rIZkHv1cSVYsL_O4oQy-_l4-CmdQf57_r86yJnVnaejz9TDTLXRUI6ImCSOkbWTnDbTZQpKXKXclKGQ4jFdHnNNDNL5thBAeO0AqEuR4wUNlIDy7xt0tnbzUso1IWf7X_S9EhV6iEIk4aqyMEzwt0n6geOJ13mCJQLok87xVsqACtpIS-n60KCR4CzivJRNM33re-CGtlRO6JcCkjRggDiVC5k6zFZoKycKjXXsZ-1sND5d8OEx3rcn59qdPhsP9tTdFxa-98Ps4pN-rrdj7ow

    
iss: http://culudb-jwt-provider.dev
uid: 1
nick: foo
email: foo@bar.com
iat: 1462364384
exp: 1462367984
nbf: 1462364384
Valid: ✓
Signature verification: ✓
```
