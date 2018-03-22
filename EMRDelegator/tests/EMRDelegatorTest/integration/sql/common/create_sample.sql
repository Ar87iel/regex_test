CREATE TABLE IF NOT EXISTS Product (
    id INT AUTO_INCREMENT NOT NULL,
    PRIMARY KEY(id)
) ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS Feature (
    id INT AUTO_INCREMENT NOT NULL,
    product_id INT DEFAULT NULL,
    PRIMARY KEY(id)
) ENGINE = InnoDB;
ALTER TABLE Feature ADD FOREIGN KEY (product_id) REFERENCES Product(id);

insert into Product values (null);
insert into Product values (null);
insert into Product values (null);

insert into Feature values (null, 1);
insert into Feature values (null, 2);
insert into Feature values (null, 2);
insert into Feature values (null, 3);
insert into Feature values (null, 3);
insert into Feature values (null, 3);