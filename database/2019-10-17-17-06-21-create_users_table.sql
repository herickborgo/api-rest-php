CREATE TABLE IF NOT EXISTS users (
	id varchar(36) primary key not null,
	name varchar(191) not null,
	email varchar(191) not null,
	password varchar(191) not null,
	created_at datetime not null,
	updated_at datetime not null,
	deleted_at datetime
)