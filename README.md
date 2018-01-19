# Link Shortener Service
# -----------------------
# 
# A link shortening service with HTML V4.01, ElephantSQL-PostgreSQL DB hosting service on PHP V7.2.1 Built-In Server
# 
# ***Note***: The timestamp currently stored is based on the timezone of the server the DB is being hosted on by ElephantSQL.
#
# Features:
# ----------
# -> Number(ID) to Short-String URL Shortening Web API built using Bijective conversion
# -> Char set includes numbers, large & small alphabets, total of 51 chars as "aeiou AEIOU l1 O0" are removed to avoid undesired and ambiguous words
# -> Every short code is unique to the particular URL
# -> View stats for a particular short URL
#
#
# Required Settings:
# -------------------
#
# => Download and install PHP on Windows and add the path to 'php.ini' from the installed location to the system's 'PATH' environment variable
# => PHP Server settings: To enable PostgreSQL with PHP on Windows:
#			i)  Open "php.ini" (rename 'php.ini-production' to 'php.ini') and edit the following to enable PostgreSQL extension
#				Uncomment the following by removing ';':
#					-> ';extension=pgsql'
#					-> ';extension=php_pdo_pgsql' and 
#					-> ';extension_dir = "ext"'
#
#
# Using the Shortener:
# --------------------
# - Download the proejct
# - Start command prompt on Windows
# - Change directory to the project folder "../LinkShortener/"
# - Type: 'php -S localhost:4555'
# - Open browser and enter: 'localhost:4555/index.php'
# - Enter long URL and press button to shorten URL and view stats!
#