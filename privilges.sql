-- Connected as SYS or SYSTEM in SQL*Plus
alter database open;
CREATE USER ecom_user IDENTIFIED BY ecom_pass;
GRANT CONNECT, RESOURCE TO ecom_user;

GRANT CREATE TABLESPACE TO ecom_user;
GRANT CREATE VIEW TO ecom_user;
GRANT SELECT ON v_$undostat TO ecom_user;
GRANT SELECT ON v_$parameter TO ecom_user;
GRANT SELECT ON dba_tablespaces TO ecom_user;
GRANT SELECT ON v_$instance TO ecom_user;


ALTER USER ecom_user QUOTA UNLIMITED ON ECOMMERCE_DATA;
ALTER USER ecom_user QUOTA UNLIMITED ON ECOMMERCE_INDEX;
