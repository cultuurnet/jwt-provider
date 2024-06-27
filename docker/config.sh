#!/bin/sh

DIR="../appconfig/files/udb3/docker/jwt-provider/"
if [ -d "$DIR" ]; then
  cp -R "$DIR"/* .
else
  echo "Error: missing appconfig see docker.md prerequisites to fix this."
  exit 1
fi
