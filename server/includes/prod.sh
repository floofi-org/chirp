#!/bin/bash
service nginx stop
mv /app/production /app/production-"$(date +%Y-%m-%dT%T%:z)"
cp -rv /app/staging /app/production
service nginx start
