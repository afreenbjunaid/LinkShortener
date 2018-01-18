# LinkShortener
# 
# A link shortening service with HTML V4.01, PHP V5.5.12, ElephantSQL-PostgreSQL DB hosting service on Wamp Server-Apache V2.4.9
# 
# Required Settings:
# -------------------
#
# => Wamp server settings:
# 		- To enable PostgreSQL with PHP:
#			i)  Click on Wamp server-> PHP -> PHP Extensions -> Enable php_pgsql and php_pdo_pgsql dlls
#			ii) Add libp.dll in '..\wamp\bin\apache\apache2.4.9\bin'
#
# Tables Structure:
#-------------------
# => url_Table: Stores every longURL requested to shorten without duplication, maintains short URL creator info
#		Columns			Data Type						Null?		Primary Key?	Sequence
#--------------------------------------------------------------------------------------------------------------------------
#		ID				bigint							Not Null	PK				Auto increement sequence 'id_sequence'
#		shortURL		varying char
#		longURL			varying char					Not Null
#		createdOn		timestamp without timezone
#		lastVisited		timestamp without timezone
#		creatorIP		character varying
#
# => stats_Table: Stores every visit to a shortURL, maintains access logs and user stats
#		Columns			Data Type						Null?		Primary Key?	Sequence
#--------------------------------------------------------------------------------------------------------------------------
#		ID				bigint							Not Null	PK				Auto increement sequence 'stats_idseq'
#		URLID			bigint							Not Null
#		visitorIP		character varying
#		visitedOn		timestamp without timezone
#		referrerURL		character varying
#		browserAgent	character varying
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#
#