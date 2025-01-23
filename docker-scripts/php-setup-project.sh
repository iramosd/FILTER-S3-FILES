#!/bin/bash

# Install dependencies
composer install

# Run Server
php -S 0.0.0.0:8000
