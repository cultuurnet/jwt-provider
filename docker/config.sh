#!/bin/sh

DIR="../appconfig/files/uitdatabank/docker/jwt-provider/"
if [ -d "$DIR" ]; then
  cp -R "$DIR"/* .
else
  echo "Error: missing appconfig. The appconfig and jwt-provider repositories must be cloned into the same parent folder."
  exit 1
fi