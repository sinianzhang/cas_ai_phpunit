# change the field type for layout in tt_content to allow for non-numeric, named layout values
CREATE TABLE tt_content (
    layout varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE pages (
	infotext_header varchar(255) DEFAULT '' NOT NULL,
	infotext mediumtext
);
