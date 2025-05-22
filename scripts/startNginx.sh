#!/bin/bash

# Remove PID antigo se existir
rm -f /run/nginx.pid

# Inicia o Nginx em foreground
exec nginx -g "daemon off;"