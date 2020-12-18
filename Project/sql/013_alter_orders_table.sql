ALTER Table Orders
    ADD COLUMN address varchar(60) default '';
    ADD COLUMN payment_method varchar(60) default '';