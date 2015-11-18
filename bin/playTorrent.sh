#!/usr/bin/env bash

#SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

echo "Lancement de peerflix"
peerflix "$1" &
PID=$!
echo "Peerflix est lancé avec le PID $PID"

echo $PID > /tmp/peerflix.pid

